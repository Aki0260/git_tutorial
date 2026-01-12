<?php
session_start();
if(!isset($_SESSION['id'])){
    header('Location:login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Loading...</title>
  <link rel="stylesheet" href="loader.css">
</head>
<body>
  <div class="three-dot-spinner">
    <div class="bounce1"></div>
    <div class="bounce2"></div>
    <div class="bounce3"></div>
  </div>

  <script>
    setTimeout(() => {
      location.href = "index.php";
    }, 800);
  </script>
</body>
</html>
