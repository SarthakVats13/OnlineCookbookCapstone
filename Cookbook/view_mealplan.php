<?php include 'header.php'; 
session_start(); 
include 'db.php'; 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
} 

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Query to get meal plans specific to the user
$sql = "SELECT * FROM mealplans WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the user ID parameter
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) { 
    echo "<h1>Your Meal Plans</h1>"; 
    while ($row = $result->fetch_assoc()) { 
        echo "Meal Plan ID: " . htmlspecialchars($row["mealplan_id"]) .  
             " - Start Date: " . htmlspecialchars($row["start_date"]) .  
             " - End Date: " . htmlspecialchars($row["end_date"]) . "<br>"; 
        
        // Get associated recipes for this meal plan
        $mealplan_id = $row["mealplan_id"]; 
        $sql_recipes = "SELECT r.title FROM mealplan_recipes mr JOIN recipes r ON mr.recipe_id = r.recipe_id WHERE mr.mealplan_id = ?";
        $stmt_recipes = $conn->prepare($sql_recipes);
        $stmt_recipes->bind_param("i", $mealplan_id); // Bind the meal plan ID parameter
        $stmt_recipes->execute();
        $result_recipes = $stmt_recipes->get_result();

        if ($result_recipes->num_rows > 0) { 
            echo "<strong>Recipes:</strong> "; 
            while ($recipe = $result_recipes->fetch_assoc()) { 
                echo htmlspecialchars($recipe["title"]) . ", "; 
            } 
            echo "<br>"; 
        } else { 
            echo "<strong>Recipes:</strong> No recipes found for this meal plan.<br>"; 
        }

        // Close the recipe statement
        $stmt_recipes->close();
    } 
} else { 
    echo "<h1>No meal plans found.</h1>"; 
}

// Close the database connection
$stmt->close();
$conn->close(); 

// More Options Section
echo '<h2>More Options:</h2>';
echo '<button onclick="location.href=\'index.html\'">Search By Ingredient</button>';
echo '<button onclick="location.href=\'mainpage.php\'">Go Back Home</button>';
?>