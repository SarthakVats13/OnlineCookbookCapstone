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
    $start_date = $_POST['start_date']; 
    $end_date = $_POST['end_date']; 

    // Insert meal plan using prepared statements.
    if ($stmt_mealplan = $conn->prepare("INSERT INTO mealplans (user_id, start_date, end_date) VALUES (?, ?, ?)")) {
        $stmt_mealplan->bind_param("iss", $user_id, $start_date, $end_date);

        if ($stmt_mealplan->execute()) {
            // Get last inserted meal plan ID.
            $mealplan_id = $conn->insert_id; 
            
            // Add associated recipes (assuming recipe_ids is an array)
            foreach ($_POST['recipe_ids'] as $recipe_id) {
                // Insert into mealplan_recipes table.
                if ($stmt_recipes = $conn->prepare("INSERT INTO mealplan_recipes (mealplan_id, recipe_id) VALUES (?, ?)")) {
                    $stmt_recipes->bind_param("ii", $mealplan_id, $recipe_id);
                    if (!$stmt_recipes->execute()) {
                        echo "Error adding recipe to meal plan: " . htmlspecialchars($stmt_recipes->error);
                    }
                    // Close the recipe statement.
                    $stmt_recipes->close();
                }
            }
            header("Location: mainpage.php"); // Redirect after successful creation
            exit();
        } else {
            echo "Error: " . htmlspecialchars($stmt_mealplan->error);
        }

        // Close the meal plan statement.
        $stmt_mealplan->close();
    }
}

$conn->close();
?>
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
    $start_date = $_POST['start_date']; 
    $end_date = $_POST['end_date']; 

    // Insert meal plan using prepared statements.
    if ($stmt_mealplan = $conn->prepare("INSERT INTO mealplans (user_id, start_date, end_date) VALUES (?, ?, ?)")) {
        $stmt_mealplan->bind_param("iss", $user_id, $start_date, $end_date);

        if ($stmt_mealplan->execute()) {
            // Get last inserted meal plan ID.
            $mealplan_id = $conn->insert_id; 
            
            // Add associated recipes (assuming recipe_ids is an array)
            foreach ($_POST['recipe_ids'] as $recipe_id) {
                // Insert into mealplan_recipes table.
                if ($stmt_recipes = $conn->prepare("INSERT INTO mealplan_recipes (mealplan_id, recipe_id) VALUES (?, ?)")) {
                    $stmt_recipes->bind_param("ii", $mealplan_id, $recipe_id);
                    if (!$stmt_recipes->execute()) {
                        echo "Error adding recipe to meal plan: " . htmlspecialchars($stmt_recipes->error);
                    }
                    // Close the recipe statement.
                    $stmt_recipes->close();
                }
            }
            header("Location: mainpage.php"); // Redirect after successful creation
            exit();
        } else {
            echo "Error: " . htmlspecialchars($stmt_mealplan->error);
        }

        // Close the meal plan statement.
        $stmt_mealplan->close();
    }
}

$conn->close();
?>