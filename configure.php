<?php
  require_once('lib/library.php');
  $pass = 'mtech';
  if(isset($_POST['password']) && $_POST['password'] == $pass){
      setcookie('ls', md5($pass));
  }else{
    if(!(isset($_COOKIE['ls']) && $_COOKIE['ls'] == md5($pass))){
          ?>
          <div>
          <form method="post" action="">
            Enter password: <input type="password" name="password" required="required">
            <br><input type="submit" value="Login">
            </form>
          </div>
        <?php
                exit();
      }
      if(isset($_GET['deletetestcases'])){
          $db->querydb('TRUNCATE TABLE testcases');
      }
  }
?>
<!doctype html>
<html>
<head>
    <title>Cache memory Configuration</title>
    <link rel="stylesheet" type="text/css" href="styles/common.css">
</head>
<body>
<nav>
    <ul>
        <li><a href="configure.php">Configure</a></li>
        <li><a href="index.php">View</a></li>
    </ul>
</nav>
<a href="?deletetestcases">Delete all test cases</a>
</body>
</html>