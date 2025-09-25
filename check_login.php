<?php include 'header.php'; 
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $email = $_POST['email']; 
    $password = $_POST['password']; 
    
   // Check for admin credentials
   if ($email === 'admin@cookbook.com' && password_verify($password, password_hash('admin', PASSWORD_DEFAULT))) {
       $_SESSION['user_id'] = 0; // Admin user ID
       $_SESSION['is_admin'] = true; // Set admin flag
       header("Location: mainpage.php"); 
       exit();
   }

   // Check for regular user credentials
   if ($stmt = $conn->prepare("SELECT * FROM users WHERE email=?")) {
       $stmt->bind_param("s", $email);
       if ($stmt->execute()) {
           $result = $stmt->get_result();
           if ($result->num_rows > 0) {
               while ($row = $result->fetch_assoc()) {
                   if (password_verify($password, $row['password'])) {
                       $_SESSION['user_id'] = $row['user_id'];
                       header("Location: mainpage.php");
                       exit();
                   }
               }
           }
       }
       echo "Invalid email or password.";
       $stmt->close();
   }
}
$conn->close();
?>