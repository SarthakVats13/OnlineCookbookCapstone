const searchForm = document.querySelector('#search-form');
const searchInput = document.querySelector('#search');
const resultsList = document.querySelector('#results');

searchForm.addEventListener('submit', (e) => {
    e.preventDefault();
    searchRecipes();
});

async function searchRecipes() {
    const searchValue = searchInput.value.trim();
    
    if (!searchValue) {
        alert("Please enter a search term.");
        return;
    }

    try {
        const response = await fetch(`https://api.edamam.com/search?q=${encodeURIComponent(searchValue)}&app_id=b121d67a&app_key=ca918ee2d68209a3cd7078eaa07cf02e&from=0&to=10`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        displayRecipes(data.hits);
    } catch (error) {
        console.error("Error fetching recipes:", error);
        alert("Failed to fetch recipes. Please try again.");
    }
}

function displayRecipes(recipes) {
    let html = '';
    recipes.forEach((recipe) => {
        html += `
        <div class='recipe'>
            <img src="${recipe.recipe.image}" alt="${recipe.recipe.label}">
            <h3>${recipe.recipe.label}</h3>
            <ul>
                ${recipe.recipe.ingredientLines.map(ingredient => `<li>${ingredient}</li>`).join('')}
            </ul>
            <a href="${recipe.recipe.url}" target="_blank">View Recipe</a>
        </div> 
        `;
    });
    
    resultsList.innerHTML = html || '<p>No recipes found.</p>';
}