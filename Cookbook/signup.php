<!DOCTYPE html>  
<html lang="en">  
<head>  
<meta charset="UTF-8">  
<meta name="viewport" content="width=device-width, initial-scale=1.0">  
<title>Signup</title>  
<link rel="stylesheet" href="style.css"> <!-- Include CSS styles -->  
</head>  
<body>  
<h1>Signup</h1>  
<form action="add_user.php" method="POST">  
Name: <input type="text" name="name" required>  
Email: <input type="email" name="email" required>  
Password: <input type="password" name="password" required>  
Dietary Preference: <input type="text" name="dietary_preference">  
<input type="submit" value="Sign Up">  
</form>  
<p>Already have an account? <a href="login.php">Login here</a></p>  
</body>  
</html>