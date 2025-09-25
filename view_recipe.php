<?php include 'header.php'; 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Recipes</title>

</head>
<body>

<h1>Saved Recipes</h1>

<h2>More Options:</h2>
<!-- Link to search by ingredient -->
<button onclick="location.href='index.html'">Search By Ingredient</button>

<button onclick="location.href='mainpage.php'">Go Back Home</button>

<!-- Example of recipe listing -->
<div id="recipes">
   <!-- Populate this dynamically from the database -->
   <?php
   include 'db.php';
   $sql_recipes = "SELECT * FROM recipes";
   $result_recipes = $conn->query($sql_recipes);

   while ($row_recipe = $result_recipes->fetch_assoc()) {
       echo "<div class='recipe'>";
       echo "<h3>" . htmlspecialchars($row_recipe['title']) . "</h3>";
       echo "<p><strong>Cuisine:</strong> " . htmlspecialchars($row_recipe['cuisine']) . "</p>";
       echo "<p><strong>Cooking Time:</strong> " . htmlspecialchars($row_recipe['cooking_time']) . " minutes</p>";
       echo "<p><strong>Instructions:</strong> " . nl2br(htmlspecialchars($row_recipe['instructions'])) . "</p>";
       
       // Buttons for actions
       echo "<button onclick=\"location.href='modify_recipe.php?recipe_id=".$row_recipe['recipe_id']."'\">More Actions</button>";

   }
   ?>
</div>



</body>
</html>