<?php
include("includes/init.php");

log_out();

  if (!$current_user) {
    record_message("You've been successfully logged out.");
  }

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Logout</title>
</head>

<body>

  <div id="content-wrap">
    <center>
    <h1><center>LOG OUT</h1>
      <h2><?php print_messages();?></h2>

      <h2><a href = "gallery.php">Go to Index</a></h2>
      <h2><a href = "index.php">Go back to Login Page</a></h2>
  </div>
</body>

</html>
