<!DOCTYPE html>  
<html lang="en">  
<head>  
<meta charset="UTF-8">  
<meta name="viewport" content="width=device-width, initial-scale=1.0">  
<title>Login</title>  
<link rel="stylesheet" href="style.css"> <!-- Include CSS styles -->  
</head>  
<body>  
<h1>Login</h1>  
<form action="check_login.php" method="POST">  
Email: <input type="email" name="email" required>  
Password: <input type="password" name="password" required>  
<input type="submit" value="Login">  
</form>  
<p>Don't have an account? <a href="signup.php">Sign up here</a></p>  
</body>  
</html>