<?php include 'header.php'; 
session_start();
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    // Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID
    $recipe_id = $_POST['recipe_id']; 
    $content = $_POST['content']; 

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO comments (user_id, recipe_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $recipe_id, $content); 

    if ($stmt->execute()) { 
        // Redirect back to the recipe view page
        header("Location: view_recipe.php?recipe_id=" . $recipe_id); // Redirect to specific recipe
        exit();
    } else { 
        echo "Error: " . $stmt->error; 
    } 

    $stmt->close(); 
} 

$conn->close(); 
?>