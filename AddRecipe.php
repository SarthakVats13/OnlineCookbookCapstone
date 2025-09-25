<?php 
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    // Capture the form data
    $title = $_POST['title']; 
    $cuisine = $_POST['cuisine']; 
    $cooking_time = $_POST['cooking_time']; 
    $instructions = $_POST['instructions']; 

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO recipes (title, cuisine, cooking_time, instructions) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $title, $cuisine, $cooking_time, $instructions); 

    if ($stmt->execute()) { 
        // Redirect to a confirmation page or back to the main page
        header("Location: view_recipe.php"); // Redirect to view recipes page 
        exit(); 
    } else { 
        echo "Error: " . $stmt->error; 
    } 

    $stmt->close(); 
} 

$conn->close(); 
?>