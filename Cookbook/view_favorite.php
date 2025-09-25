<?php include 'header.php'; 
session_start(); 
include 'db.php'; 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
} 

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Query to get favorite recipes specific to the user
$sql = "SELECT r.* FROM favorites f JOIN recipes r ON f.recipe_id = r.recipe_id WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the user ID parameter
$stmt->execute();
$result = $stmt->get_result();

echo "<h1>Your Favorite Recipes</h1>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . htmlspecialchars($row["recipe_id"]) .  
             " - Title: " . htmlspecialchars($row["title"]) . "<br>";
    }
} else {
    echo "<h1>You have no favorite recipes.</h1>"; // Changed for clarity
}

// Close the database connection
$stmt->close();
$conn->close(); 

// More Options Section
echo '<h2>More Options:</h2>';
echo '<button onclick="location.href=\'index.html\'">Search By Ingredient</button>';
echo '<button onclick="location.href=\'mainpage.php\'">Go Back Home</button>';
?>