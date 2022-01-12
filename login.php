<?php
include('storage.php');
include('auth.php');
include('userstorage.php');

// functions
function redirect($page) {
  header("Location: ${page}");
  exit();
}
function validate($post, &$data, &$errors) {
  foreach ($errors as $i => $value) {
    unset($errors[$i]);
}
  if(!isset($post['username']) || empty($post['username'])|| preg_match('/[\s-]/',$post['username']))
  {
    $errors["username"] = "Username cannot be empty or with spaces!";
  }

  if(!isset($post['password']) || empty($post['password']))
  {
    $errors["password"] = "Password cannot be empty!";
  }
  
  

  $data = $post;
  return count($errors) === 0;

  return count($errors) === 0;
}

// main
session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$data = [];
$errors = [];
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    $auth_user = $auth->authenticate($data['username'], $data['password']);
    if (!$auth_user) {
      $errors['global'] = "User name or Password is incorrect!";
    } else {
      $auth->login($auth_user);
      redirect('index.php');
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="login.css">
  <title>Document</title>
</head>
<body>
<div class="center">

  <h1>Login</h1>
  <?php if (isset($errors['global'])) : ?>
    <section class="error"><?= $errors['global'] ?></section>
  <?php endif; ?>
  <br><br><br>
  <form action="" method="post">
  <div class="inputbox">
      <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>">

      <?php if (isset($errors['username'])) : ?>
        <span class="error"><?= $errors['username'] ?></span>
      <?php endif; ?>
      <span>Username</span>

    </div>
    <div class="inputbox">
      <input type="password" name="password" id="password">
      <?php if (isset($errors['password'])): ?>
        <span class="error"><?= $errors['password'] ?></span>
      <?php endif; ?>
      <span>Password</span>
    </div>
    <div class="inputbox">
      <button type="submit">Login</button>
    </div>
  </form>
  <section class="goLinks">
    <a href="register.php">Register</a>     
     
    <a href="index.php"> Main Page</a>     
      </section>
      </div>
</body>
</html>

