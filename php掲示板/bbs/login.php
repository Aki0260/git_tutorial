<?php
session_start();
require('library.php');

$error = [];
$email = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);

    if ($email === '' || $password === '') {
        $error['login'] = 'blank';
    } else {
        $db = dbconnect();
        $stmt = $db->prepare('SELECT id, name, password FROM members WHERE email = ? LIMIT 1');
        if (!$stmt) {
            die($db->error);
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($id, $name, $hash);
        $stmt->fetch();

        if ($hash && password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            header('Location: index.php');
            exit();
        } else {
            $error['login'] = 'failed';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="style2.css">
<link rel="stylesheet" href="loader.css">
<title>ログイン</title>
</head>

<body>

<!-- ▼ ローダー -->
<div id="loader-overlay">
  <div class="three-dot-spinner">
    <div class="bounce1"></div>
    <div class="bounce2"></div>
    <div class="bounce3"></div>
  </div>
</div>

<div id="wrap">
  <div id="head">
    <h1>掲示板</h1>
  </div>

  <div id="content">
    <div id="lead">
      <p>メールアドレスとパスワードを記入してログインしてください。</p>
      <p>&raquo;<a href="join/">入会手続きをする</a></p>
    </div>

    <form action="" method="post" id="login-form">
      <dl>
        <dt>メールアドレス</dt>
        <dd>
          <input type="text" name="email" size="35" value="<?php echo h($email); ?>">
          <?php if (isset($error['login']) && $error['login'] === 'blank'): ?>
            <p class="error">* メールアドレスとパスワードをご記入ください</p>
          <?php endif; ?>
          <?php if (isset($error['login']) && $error['login'] === 'failed'): ?>
            <p class="error">* ログインに失敗しました</p>
          <?php endif; ?>
        </dd>

        <dt>パスワード</dt>
        <dd>
          <input type="password" name="password" size="35">
        </dd>
      </dl>

      <div class="form-button">
        <input type="submit" value="ログインする">
      </div>
    </form>
  </div>
</div>

<!-- ローダー表示用JS -->
<script>
document.getElementById('login-form').addEventListener('submit', function () {
  document.getElementById('loader-overlay').style.display = 'flex';
});
</script>

</body>
</html>
