<?php include("includes/init.php"); ?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Login</title>
</head>

<body>

<div id="content-wrap">
  <center>
  <h1><center>LOGIN PAGE</h1>
    <h2>Log in to access user functions or proceed as guest</h2>
    <h3><?php print_messages();?></h3>

  <form id="loginForm" action="index.php" method="post">
    <ul>
        <label>Username:</label>
        <input type="text" name="username" required/>
        <p></p>
        <label>Password :</label>
        <input type="password" name="password" required/>
        <p></p>
        <button name="login" type="submit"><center>Log In</button>
          <h2><a href = "gallery.php">Go to Gallery</a></h2>
          <h2><a href = "logout.php">Log Out</a></h2>
    </ul>
  </form>
</div>


</body>
</html>
