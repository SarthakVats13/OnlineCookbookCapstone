<?php include 'header.php'; 
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $name = $_POST['name']; 
    $email = $_POST['email']; 
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing password
    $dietary_preference = $_POST['dietary_preference']; 

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, dietary_preference) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $dietary_preference); 

    if ($stmt->execute()) { 
        header("Location: login.php"); // Redirect to login page 
        exit(); 
    } else { 
        echo "Error: " . $stmt->error; 
    } 

    $stmt->close(); 
} 

$conn->close(); 
?>