<?php

$messages = array();

function record_message($message) {
  global $messages;
  array_push($messages, $message);
}

function print_messages() {
  global $messages;
  foreach ($messages as $message) {
    echo "<p><strong>" . htmlspecialchars($message) . "</strong></p>\n";
  }
}

// show database errors during development.
function handle_db_error($exception) {
  echo '<p><strong>' . htmlspecialchars('Exception : ' . $exception->getMessage()) . '</strong></p>';
}

// execute an SQL query and return the results.
function exec_sql_query($db, $sql, $params = array()) {
  try {
    $query = $db->prepare($sql);
    if ($query and $query->execute($params)) {
      return $query;
    }
  } catch (PDOException $exception) {
    handle_db_error($exception);
  }
  return NULL;
}

// open connection to database
function open_or_init_sqlite_db($db_filename, $init_sql_filename) {
  if (!file_exists($db_filename)) {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db_init_sql = file_get_contents($init_sql_filename);
    if ($db_init_sql) {
      try {
        $result = $db->exec($db_init_sql);
        if ($result) {
          return $db;
        }
      } catch (PDOException $exception) {
        handle_db_error($exception);
      }
    }
  } else {
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }
  return NULL;
}

$db= open_or_init_sqlite_db('photogallery.sqlite', "init/init.sql");

function check_login() {
  global $db;
  if (isset($_COOKIE["session"])) {
    $session = $_COOKIE["session"];
    $sql = "SELECT * FROM login WHERE session = :session_id;";
    $params = array (
      ":session_id" => $session,
    );
    $records = exec_sql_query($db, $sql, $params)->fetchAll();
    if ($records) {
      $login = $records[0];
      return $login["username"];
    }
  }
  return NULL;
}

function log_in($username, $password) {
  global $db;
  if ($username && $password) {
    $sql = "SELECT * FROM login WHERE username = :username;";
    $params = array(
      ':username' => $username
    );
    $records = exec_sql_query($db, $sql, $params)->fetchAll();
    if ($records) {
      $login = $records[0];
      if (password_verify($password,$login['password'])) {
        $session = uniqid();
        $sql = "UPDATE login SET session = :session WHERE id = :user_id;";
        $params = array (
          ":user_id" => $login['id'],
          ":session" => $session
        );
        $result = exec_sql_query($db, $sql, $params);
        if ($result) {
          setcookie("session", $session, time()+3600);
          record_message("Successfully logged in, $username please proceed to Gallery");
          return $username;
        }
      } else {
        record_message("Invalid username or password.");
      }
    } else {
      record_message("Invalid username or password.");
    }
  } else {
    record_message("No username or password given.");
  }
  return NULL;
}

function log_out() {
  global $current_user;
  global $db;
  if ($current_user) {
    $sql = "UPDATE login SET session = :session WHERE username = :username;";
    $params = array (
      ":username" => $current_user,
      ":session" => NULL
    );
    if (!exec_sql_query($db, $sql, $params)) {
      record_message("log out failed.");
    }
    //
    setcookie("session", "", time()-3600);
    $current_user = NULL;
  }
}
// Check if we should login the user
if (isset($_POST['login'])) {
  $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
  $username = trim($username);
  $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
  $current_user = log_in($username, $password);
}
// check if logged in
$current_user = check_login();
?>
