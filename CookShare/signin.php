<?php
//エラーログ
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
function h($s){
return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}
/*
require_once('config.php');
if($_POST['action'] == 'signin'){
session_start();
//POSTのvalidate
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
echo '入力された値が不正です。';
return false;
}
//DB内でPOSTされたメールアドレスを検索
try {
$pdo = new PDO(DSN, DB_USER, DB_PASS);
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$_POST['email']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (\Exception $e) {
echo $e->getMessage() . PHP_EOL;
}
//emailがDB内に存在しているか確認
if (!isset($row['email'])) {
echo 'メールアドレス又はパスワードが間違っています。';
//return false;
}
//パスワード確認後sessionにメールアドレスを渡す
if (password_verify($_POST['password'], $row['password'])) {
session_regenerate_id(true); //session_idを新しく生成し、置き換える
$_SESSION['EMAIL'] = $row['email'];
$_SESSION['NAME'] = $row['name'];
echo 'ログインしました';
header("Location: ./index.php");
} else {
echo 'メールアドレス又はパスワードが間違っています。';
//return false;
}
}
*/
$alert = '';
$alert_insert = '';
require_once('config.php');
session_start();
//?ログイン処理
if(isset($_POST['action']) && $_POST['action'] == 'signin') {
//DB内でPOSTされたメールアドレスを検索
try {
$pdo = new PDO(DSN, DB_USER, DB_PASS);
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$_POST['email']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (\Exception $e) {
echo $e->getMessage() . PHP_EOL;
}
//POSTのValidate。
if (!$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
$alert = '入力された値が不正です。';
}
//emailがDB内に存在しているか確認
if (!isset($row['email'])) {
$alert = 'メールアドレス又はパスワードが間違っています。';
}
//パスワード確認後sessionにメールアドレスを渡す
if(isset($row['password']) && isset($row['password'])){
if (password_verify($_POST['password'], $row['password'])) {
session_regenerate_id(true); //session_idを新しく生成し、置き換える
$_SESSION['ID'] = $row['id'];
$_SESSION['EMAIL'] = $row['email'];
$_SESSION['NAME'] = $row['name'];
//echo 'ログインしました';
header("Location: ./index.php");
} else {
$alert = 'メールアドレス又はパスワードが間違っています。';
}
}
}
//?新規登録処理
if(isset($_POST['action']) && $_POST['action'] == 'signup') {
//POSTのValidate。
if (!$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
$alert = '入力された値が不正です。';
}
//パスワードの正規表現
if (preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,100}+\z/i', $_POST['password'])) {
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
//登録処理
try {
$name = h($_POST['name']);
$pdo = new PDO(DSN, DB_USER, DB_PASS);
$stmt = $pdo->prepare("INSERT INTO users(email, password, name) VALUES(?, ?, ?)");
$stmt->execute([$email, $password, $name]);
$alert_insert = '登録しました！';
} catch (\Exception $e) {
$alert = '登録済みのメールアドレスです。'.$e;
}
} else {
$alert = 'パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上で設定してください。';
}
}
/*
//DB内のメールアドレスを取得
$stmt = $pdo->prepare("select email from userDeta where email = ?");
$stmt->execute([$email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
//DB内のメールアドレスと重複していない場合、登録する。
if (!isset($row['email'])) {
$stmt = $pdo->prepare("insert into userDeta(email, password) value(?, ?)");
$stmt->execute([$email, $password]);
echo "登録完了";
} else {
echo '既に登録されたメールアドレスです。';
return false;
}
*/
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>クックシェア
    </title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <!-- Bootstrap core CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.18.0/css/mdb.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js">
    </script>
    <!-- Hierarchy Select core CSS -->
    <link rel="stylesheet" href="SELECT/assets/highlight.css">
    <link rel="stylesheet" href="SELECT/assets/pygments.css">
    <link rel="stylesheet" href="SELECT/dist/hierarchy-select.min.css">
  </head>
  <body>
    <!--
<h1>ようこそ、ログインしてください。</h1>
<form  action="login.php" method="post">
<label for="email">email</label>
<input type="email" name="email">
<label for="password">password</label>
<input type="password" name="password">
<button type="submit" name="action" value="signin">Sign In!</button>
</form>
-->
    <div class="container main">
        <h1 class="deep-orange-text text-center font-weight-bold" style="margin: 50px 0">Cook Share</h1>
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
             aria-selected="true">ログイン
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile"
             aria-selected="false">新規登録
          </a>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <form class="text-center border border-light p-5" action="signin.php" method="post">
            <p class="h4 mb-4">ログイン
            </p>
            <!-- アラートメッセージ -->
            <?php if(trim($alert) !== '') :?>
            <div class="alert alert-danger text-left" role="alert">
              <?= $alert ?>
            </div>
            <?php endif; ?>
            <?php if(trim($alert_insert) !== '') :?>
            <div class="alert alert-success text-left" role="alert">
              <?= $alert_insert ?>
            </div>
            <?php endif; ?>
            <!-- Email -->
            <input type="email" name="email" id="defaultLoginFormEmail" class="form-control mb-4" placeholder="メールアドレス">
            <!-- Password -->
            <input type="password" name="password" id="defaultLoginFormPassword" class="form-control mb-4" placeholder="パスワード">
            <!--
<div class="d-flex justify-content-around">
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" id="defaultLoginFormRemember">
<label class="custom-control-label" for="defaultLoginFormRemember">次回からメールアドレスの入力を省略
</label>
</div>
<div>
<a href="">メールアドレスを忘れた方
</a>
<a href="">パスワードを忘れた方
</a>
</div>
</div>
-->
            <button type="submit" class="btn btn-info" name="action" value="signin">ログイン
            </button>
            <!-- Register 
<p>初めての方はこちら
<a href="signup.php">新規登録</a>
</p>
-->
          </form>
        </div>
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <form class="text-center border border-light p-5" action="signin.php" method="post">
            <p class="h4 mb-4">新規登録
            </p>
            <!-- Name -->
            <input type="name" name="name" id="defaultLoginFormName" class="form-control mb-4" placeholder="ユーザー名">
            <!-- Email -->
            <input type="email" name="email" id="defaultLoginFormEmail" class="form-control mb-4" placeholder="メールアドレス">
            <!-- Password -->
            <input type="password" name="password" id="defaultLoginFormPassword" class="form-control mb-4" placeholder="パスワード">
            <p>※パスワードは半角英数字をそれぞれ１文字以上含んだ、８文字以上で設定してください。
            </p>
            <!-- Sign in button -->
            <button class="btn btn-info" type="submit" name="action" value="signup">新規登録
            </button>
            <!--<a href="signin.php">ログインはこちら</a>-->
          </form>
        </div>
      </div>
    </div>
    <!-- JQuery -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js">
    </script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.4/umd/popper.min.js">
    </script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.min.js">
    </script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.18.0/js/mdb.min.js">
    </script>
    <script type="text/javascript" src="script.js">
    </script>
    <script src="http://code.jquery.com/jquery-1.8.3.js">
    </script>
    <script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js">
    </script>
    <!-- Hierarchy Select core JavaScript 
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>-->
    <script src="SELECT/dist/hierarchy-select.min.js">
    </script>
  </body>
</html>
