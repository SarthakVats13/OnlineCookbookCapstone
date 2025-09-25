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
        alert("Please enter at least one ingredient.");
        return;
    }
    // Split by comma, trim spaces, filter empty
    const ingredients = searchValue.split(',').map(i => i.trim().toLowerCase()).filter(Boolean);
    if (ingredients.length === 0) {
        alert("Please enter valid ingredients.");
        return;
    }
    try {
        const response = await fetch('http://localhost:3001/search-recipes', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ingredients })
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        displayRecipes(data);
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
            <img src="${recipe.image}" alt="${recipe.name}">
            <h3>${recipe.name}</h3>
            <ul>
                ${recipe.ingredients.map(ingredient => `<li>${ingredient}</li>`).join('')}
            </ul>
            <p>${recipe.instructions}</p>
            <a href="${recipe.url}" target="_blank">View Recipe</a>
        </div> 
        `;
    });
    resultsList.innerHTML = html || '<p>No recipes found.</p>';
}