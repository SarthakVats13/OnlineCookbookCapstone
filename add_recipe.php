<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookbook</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        h2 {
            color: #555;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="date"], textarea, select {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        a {
            display: inline-block;
            margin-top: 10px;
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>Welcome to the Cookbook</h1>

<h2>Add Recipe</h2>
<form action="AddRecipe.php" method="POST">
    Title: <input type="text" name="title" required>
    Cuisine: <input type="text" name="cuisine">
    Cooking Time (minutes): <input type="number" name="cooking_time">
    Instructions: <textarea name="instructions" required></textarea>
    <input type="submit" value="Add Recipe">
</form>

<h2>View Users</h2>
<a href="view_users.php">View Users</a>

<h2>View Recipes</h2>
<a href="view_recipe.php">View Recipes</a>

<h2>View Meal Plans</h2>
<a href="view_mealplan.php">View Meal Plans</a>

<h2>More Options:</h2>
<button onclick="location.href='index.html'">Search By Ingredient</button>
<button onclick="location.href='mainpage.php'">Go Back Home</button>
</body>
</html>