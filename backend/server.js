const express = require('express');
const cors = require('cors');
const fs = require('fs');
const path = require('path');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');

const app = express();
const PORT = 3001;
const JWT_SECRET = 'supersecretkey'; // In production, use env variable

app.use(cors());
app.use(express.json());

// --- Recipes dataset (CSV) loader ---------------------------------------
// CSV dataset loader
const csv = require('csv-parser');
// Use a relative path inside backend folder so the project is portable
const csvFilePath = path.join(__dirname, 'RECEIPES.csv');
let recipesCache = null; // cached array of recipes
let csvFileMtime = null; // last modified time of the CSV file

/**
 * Parse and load recipes from CSV file. Caches the result and only reloads
 * when `forceReload` is true or when the file modification time changes.
 * Each CSV row must include: title, ingredients (comma-separated), instructions, image_url
 * Returns: Promise<recipe[]>
 */
function loadRecipesFromCSV(forceReload = false) {
    return new Promise((resolve, reject) => {
        try {
            // If file doesn't exist, handle gracefully by returning empty array
            if (!fs.existsSync(csvFilePath)) {
                console.error('CSV file not found:', csvFilePath);
                recipesCache = [];
                csvFileMtime = null;
                return resolve(recipesCache);
            }

            const stat = fs.statSync(csvFilePath);
            const mtime = stat.mtimeMs;

            // Return cache if present and unchanged
            if (recipesCache && !forceReload && csvFileMtime === mtime) {
                return resolve(recipesCache);
            }

            const results = [];
            let idCounter = 1;

            fs.createReadStream(csvFilePath)
                .pipe(csv())
                .on('data', (row) => {
                    // Parse row safely
                    // Helper: try several possible header names and case-insensitive keys
                    function getField(r, names) {
                        for (const n of names) {
                            if (r[n] !== undefined) return r[n];
                        }
                        // fallback: case-insensitive match
                        const keys = Object.keys(r || {});
                        for (const k of keys) {
                            for (const n of names) {
                                if (k.toLowerCase() === n.toLowerCase()) return r[k];
                            }
                        }
                        return undefined;
                    }

                    const titleRaw = getField(row, ['title', 'name', 'Dish Name', 'dish name']);
                    const title = (titleRaw || '').toString().trim();

                    const rawIngredients = (getField(row, ['ingredients', 'Ingredients', 'ingredient']) || '').toString();
                    // Clean common array notation and quotes from CSV cells like "['tomato', 'onion']"
                    const cleaned = rawIngredients.replace(/[\[\]'"\(\)]/g, '');
                    const ingredients = cleaned.split(',')
                        .map(i => i.trim().toLowerCase())
                        .filter(Boolean);

                    const instructions = (getField(row, ['instructions', 'instruction']) || '').toString().trim();
                    const image_url = (getField(row, ['image_url', 'image', 'Image']) || '').toString().trim();

                    results.push({
                        id: idCounter++,
                        title,
                        ingredients,
                        instructions,
                        image_url,
                        image: image_url // alias for frontend compatibility
                    });
                })
                .on('end', () => {
                    try {
                        const stat2 = fs.statSync(csvFilePath);
                        csvFileMtime = stat2.mtimeMs;
                    } catch (e) {
                        csvFileMtime = null;
                    }
                    recipesCache = results;
                    console.log(`✅ Loaded ${results.length} recipes from CSV dataset.`);
                    resolve(results);
                })
                .on('error', (err) => {
                    console.error('❌ Error parsing CSV dataset:', err.message || err);
                    // Treat parse errors as fatal for loading
                    reject(err);
                });
        } catch (err) {
            console.error('❌ Error loading CSV dataset:', err.message || err);
            // Return empty array on general failures
            recipesCache = [];
            csvFileMtime = null;
            return resolve(recipesCache);
        }
    });
}

const usersPath = path.join(__dirname, 'users.json');

// Helper to read users
function getUsers() {
    const data = fs.readFileSync(usersPath, 'utf-8');
    return JSON.parse(data);
}

// Helper to write users
function saveUsers(users) {
    fs.writeFileSync(usersPath, JSON.stringify(users, null, 2));
}

// Middleware to verify JWT
function authenticateToken(req, res, next) {
    const authHeader = req.headers['authorization'];
    const token = authHeader?.split(' ')[1];
    if (!token) return res.sendStatus(401);
    jwt.verify(token, JWT_SECRET, (err, user) => {
        if (err) return res.sendStatus(403);
        req.user = user;
        next();
    });
}

// POST /search-recipes
// Expects: { ingredients: ["ingredient1", "ingredient2", ...] }
app.post('/search-recipes', async (req, res) => {
    const userIngredientsRaw = req.body.ingredients || [];
    if (!Array.isArray(userIngredientsRaw) || userIngredientsRaw.length === 0) {
        return res.status(400).json({ error: 'No ingredients provided.' });
    }

    // Normalize user ingredients for case-insensitive matching and use a Set for fast lookup
    const userIngredients = userIngredientsRaw.map(i => i.toString().trim().toLowerCase()).filter(Boolean);
    const userSet = new Set(userIngredients);
    console.log('Searching recipes with:', Array.from(userSet));

    try {
        // Load recipes from CSV (cached)
        const recipes = await loadRecipesFromCSV();

        // Filter: include recipe if ANY of its ingredients matches any user ingredient.
        // We do a forgiving match: exact match OR substring match (user ingredient contained in recipe ingredient or vice versa).
        const matches = recipes.filter(recipe => {
            if (!Array.isArray(recipe.ingredients) || recipe.ingredients.length === 0) return false;
            return recipe.ingredients.some(ing => {
                if (!ing) return false;
                if (userSet.has(ing)) return true;
                for (const u of userSet) {
                    if (!u) continue;
                    if (ing.includes(u) || u.includes(ing)) return true;
                }
                return false;
            });
        });

        // Respond with matches; keep structure compatible with frontend
        res.json(matches);
    } catch (error) {
        console.error('Error loading recipes from CSV:', error);
        res.status(500).json({ error: 'Failed to load recipes dataset.' });
    }
});

// POST /register
// Expects: { username, password }
app.post('/register', async (req, res) => {
    const { username, password } = req.body;
    if (!username || !password) {
        return res.status(400).json({ error: 'Username and password required.' });
    }
    let users = getUsers();
    if (users.find(u => u.username === username)) {
        return res.status(409).json({ error: 'Username already exists.' });
    }
    const hashed = await bcrypt.hash(password, 10);
    const newUser = {
        id: users.length ? users[users.length - 1].id + 1 : 1,
        username,
        password: hashed,
        favorites: [],
        mealplan: []
    };
    users.push(newUser);
    saveUsers(users);
    res.json({ success: true });
});

// POST /login
// Expects: { username, password }
app.post('/login', async (req, res) => {
    const { username, password } = req.body;
    if (!username || !password) {
        return res.status(400).json({ error: 'Username and password required.' });
    }
    const users = getUsers();
    const user = users.find(u => u.username === username);
    if (!user) {
        return res.status(401).json({ error: 'Invalid username or password.' });
    }
    const match = await bcrypt.compare(password, user.password);
    if (!match) {
        return res.status(401).json({ error: 'Invalid username or password.' });
    }
    // Generate JWT
    const token = jwt.sign({ id: user.id, username: user.username }, JWT_SECRET, { expiresIn: '2h' });
    res.json({ token, username: user.username });
});

// --- FAVORITES ---
// GET /favorites (get user's favorites)
app.get('/favorites', authenticateToken, (req, res) => {
    const users = getUsers();
    const user = users.find(u => u.id === req.user.id);
    if (!user) return res.sendStatus(404);
    res.json({ favorites: user.favorites });
});

// POST /favorites (add a recipe to favorites)
// Expects: { recipeId }
app.post('/favorites', authenticateToken, (req, res) => {
    const { recipeId } = req.body;
    if (typeof recipeId !== 'number') return res.status(400).json({ error: 'recipeId required.' });
    let users = getUsers();
    const user = users.find(u => u.id === req.user.id);
    if (!user) return res.sendStatus(404);
    if (!user.favorites.includes(recipeId)) user.favorites.push(recipeId);
    saveUsers(users);
    res.json({ favorites: user.favorites });
});

// DELETE /favorites (remove a recipe from favorites)
// Expects: { recipeId }
app.delete('/favorites', authenticateToken, (req, res) => {
    const { recipeId } = req.body;
    if (typeof recipeId !== 'number') return res.status(400).json({ error: 'recipeId required.' });
    let users = getUsers();
    const user = users.find(u => u.id === req.user.id);
    if (!user) return res.sendStatus(404);
    user.favorites = user.favorites.filter(id => id !== recipeId);
    saveUsers(users);
    res.json({ favorites: user.favorites });
});

// --- MEAL PLAN ---
// GET /mealplan (get user's meal plan)
app.get('/mealplan', authenticateToken, (req, res) => {
    const users = getUsers();
    const user = users.find(u => u.id === req.user.id);
    if (!user) return res.sendStatus(404);
    res.json({ mealplan: user.mealplan });
});

// POST /mealplan (add a recipe to meal plan)
// Expects: { recipeId }
app.post('/mealplan', authenticateToken, (req, res) => {
    const { recipeId } = req.body;
    if (typeof recipeId !== 'number') return res.status(400).json({ error: 'recipeId required.' });
    let users = getUsers();
    const user = users.find(u => u.id === req.user.id);
    if (!user) return res.sendStatus(404);
    if (!user.mealplan.includes(recipeId)) user.mealplan.push(recipeId);
    saveUsers(users);
    res.json({ mealplan: user.mealplan });
});

// DELETE /mealplan (remove a recipe from meal plan)
// Expects: { recipeId }
app.delete('/mealplan', authenticateToken, (req, res) => {
    const { recipeId } = req.body;
    if (typeof recipeId !== 'number') return res.status(400).json({ error: 'recipeId required.' });
    let users = getUsers();
    const user = users.find(u => u.id === req.user.id);
    if (!user) return res.sendStatus(404);
    user.mealplan = user.mealplan.filter(id => id !== recipeId);
    saveUsers(users);
    res.json({ mealplan: user.mealplan });
});

app.listen(PORT, () => {
    console.log(`Recipe backend running on http://localhost:${PORT}`);
});