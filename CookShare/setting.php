<?php
//エラーログ
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
function h($s){
return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}
require_once('config.php');
session_start();
$id = h($_SESSION['ID']);
$email = h($_SESSION['EMAIL']);
$user_name = h($_SESSION['NAME']);
try {
$dbh = new PDO(DSN, DB_USER, DB_PASS);
//ログインユーザー情報　取得
$stmt_users = $dbh->prepare('SELECT * FROM users WHERE id = ?');
$stmt_users->execute([h($_SESSION['ID'])]);
$result_users = $stmt_users->fetch();
$alert_profile = '';
$update_profile_flg = 0;
//プロフィール更新
if(isset($_POST['action']) && $_POST['action'] == 'update_prof') {
if(h($_POST['name']) !== '' && h($_POST['email']) !== ''){
//画像ファイル　アップロード
$tempfile = $_FILES['image']['tmp_name'];
$filename = './image/users/' . $_FILES['image']['name'];
//if (is_uploaded_file($tempfile)) {
//if ( move_uploaded_file($tempfile , $filename )) {
//$alert_profile = $filename . "をアップロードしました。<br>";
//写真
if (is_uploaded_file($tempfile)) {
if ( move_uploaded_file($tempfile , $filename )) {
//ファイルのアップロード成功の場合
$image = $_FILES['image']['name'];
}else{
//ファイルのアップロード失敗の場合
$image = null;
}
$stmt_UPDATE_users = $dbh->prepare('UPDATE users SET email = ?, name = ?, image_path = ? WHERE id = ?');
$stmt_UPDATE_users->execute([h($_POST['email']), h($_POST['name']), $image, h($_SESSION['ID'])]);
}else{ //ファイルが未選択の場合
if($_POST['btnImgeUsersDel_flg'] === "1") {
//更新　画像が削除された場合
$image = null;
$stmt_UPDATE_users = $dbh->prepare('UPDATE users SET email = ?, name = ?, image_path = ? WHERE id = ?');
$stmt_UPDATE_users->execute([h($_POST['email']), h($_POST['name']), $image, h($_SESSION['ID'])]);
}else{
//更新　画像がに変更がない場合
$stmt_UPDATE_users = $dbh->prepare('UPDATE users SET email = ?, name = ? WHERE id = ?');
$stmt_UPDATE_users->execute([h($_POST['email']), h($_POST['name']), h($_SESSION['ID'])]);
}
}
header("Location: " . $_SERVER['PHP_SELF']);
$alert_profile = '更新しました！';
$update_profile_flg = 1;
//} else {
//$alert_profile = 'ファイルをアップロードできません。';
//}
//} else {
//$alert_profile = 'ファイルが選択されていません。';
//} 
}
else{
$alert_profile = '未入力があります。';
}
}
$alert_password = '';
$update_password_flg = 0;
//パスワード更新
if(isset($_POST['action']) && $_POST['action'] == 'update_pass') {
if(h($_POST['current_password']) !== '' && h($_POST['new_password']) !== '' && h($_POST['confirmation_password']) !== ''){
//現在のパスワードが合っているか
if (password_verify($_POST['current_password'], $result_users['password'])) {
//パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上で設定してください。
if (preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,100}+\z/i', $_POST['new_password'])) {
//確認用と比較
if($_POST['new_password'] === $_POST['confirmation_password']){
$password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
$stmt_UPDATE_users = $dbh->prepare('UPDATE users SET password = ? WHERE id = ?');
$stmt_UPDATE_users->execute([$password, h($_SESSION['ID'])]);
header("Location: " . $_SERVER['PHP_SELF']);
$alert_password = '更新しました！';
$update_password_flg = 1;
} else {
$alert_password = '新しいパスワードと一致させてください。';
}
} else {
$alert_password = 'パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上で設定してください。';
}
}
else{
$alert_password = '現在のパスワードが一致しません。';
}
}
else{
$alert_password = '未入力があります。';
}
}
$dbh = null;
} catch (PDOException $e) {
print "エラー!: " . $e->getMessage() . "<br/>";
die();
}
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
    <header>
      <nav class="navbar navbar-expand-lg navbar-dark orange lighten-1 mb-4 fixed">
        <div class="container">
          <a class="navbar-brand font-weight-bold" href="./index.php">Cook Share
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
            </span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item">
                <a class="nav-link" href="./index.php">トップ
                </a>
              </li>
            </ul>
            <form class="form-inline" method="post" action="index.php">
              <input class="form-control" type="text" id="txtSearch" name="txtSearch" placeholder="レシピを探す" aria-label="Search">
              <button class="btn btn-info" type="submit" name="action" value="filtering">
                <i class="fas fa-search">
                </i> 検索
              </button>
            </form>
            <button class="btn btn-primary" onclick="location.href='./register.php'">
              <i class="fas fa-edit ml-1">
              </i>
              </i> 新規登録
            </button>
          <ul class="navbar-nav ml-auto nav-flex-icons">
            <li class="nav-item dropdown">
              <button class="btn btn-light-green dropdown-toggle mr-4" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="text-transform: none;">
                <?php if(trim($result_users['image_path']) !== ''): ?>
                <img src="image/users/<?= $result_users['image_path'] ?>" class="rounded-circle z-depth-0" alt="avatar image" height="20">
                <?php elseif(trim($result_users['image_path']) === ''): ?>
                <img src="https://applech2.com/wp-content/uploads/2017/10/macOS-Guest-user-logo-icon.jpg" class="rounded-circle z-depth-0" alt="avatar image" height="20">
                <?php else: ?>
                <?php endif; ?>
                <?= $result_users['name']."さん" ?>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="./favorites.php">
                  <i class="fas fa-heart fa-1x mb-1 red-text">
                  </i> お気に入り
                </a>
                <a class="dropdown-item" href="./list.php">
                  <i class="fas fa-shopping-bag fa-1x mb-1 orange-text">
                  </i> 買い物リスト
                </a>
                <a class="dropdown-item" href="./setting.php">
                  <i class="fas fa-user fa-1x mb-1 blue-text">
                  </i> 登録情報
                </a>
                <a class="dropdown-item" href="./logout.php">
                  <i class="fas fa-sign-out-alt fa-1x mb-1 green-text">
                  </i> ログアウト
                </a>
              </div>
            </li>
          </ul>
        </div>
        </div>
      </nav>
    </header>
  <div class="container main">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">
          <i class="fas fa-user">
          </i> プロフィール
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false">
          <i class="fas fa-lock prefix">
          </i> パスワード
        </a>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent">
      <!-- プロフィールタブ -->
      <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
        <form method="post" enctype="multipart/form-data">
          <!-- アラートメッセージ -->
          <?php if($update_profile_flg === 0 && trim($alert_profile) !== '') :?>
          <div class="alert alert-danger" role="alert">
            <?= $alert_profile ?>
          </div>
          <?php elseif($update_profile_flg === 1 && trim($alert_profile) !== '') :?>
          <div class="alert alert-success" role="alert">
            <?= $alert_profile ?>
          </div>
          <?php endif; ?>
          <br>
          <!-- サムネイル -->
          <?php if(trim($result_users['image_path']) !== ''): ?>
          <img id="thumbnail" src="image/users/<?= $result_users['image_path']?>" alt="avatar" class="avatar rounded-circle d-flex align-self-center mr-2 z-depth-1" width="50" height="50">
          <?php else:?>
          <img id="thumbnail" src="https://applech2.com/wp-content/uploads/2017/10/macOS-Guest-user-logo-icon.jpg" alt="avatar" class="avatar rounded-circle d-flex align-self-center mr-2 z-depth-1" width="50" height="50">
          <?php endif; ?>
          <br>
          <!-- ファイルアップロード -->
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text" id="imageAdd">
                <i class="far fa-image">
                </i>
              </span>
            </div>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="image" aria-describedby="imageAdd" name="image">
              <label class="custom-file-label" for="image" id="lblImage">画像を選択してください
              </label>
            </div>
          </div>
          <button type="button" class="btn btn-danger" id="btnImgeUsersDel">写真を削除
          </button>
          <input type="hidden" id="btnImgeUsersDel_flg" name="btnImgeUsersDel_flg">
          <br>
          <!-- ユーザー名 -->
          <div class="md-form">
            <i class="fas fa-user prefix">
            </i>
            <input type="text" id="name" class="form-control validate" name="name" value="<?= $result_users['name']?>">
            <label for="name" data-error="wrong" data-success="right">ユーザー名
            </label>
          </div>
          <!-- メールアドレス -->
          <div class="md-form">
            <i class="fas fa-envelope prefix">
            </i>
            <input type="email" id="email" class="form-control validate" name="email" value="<?= $result_users['email']?>">
            <label for="email" data-error="wrong" data-success="right">メールアドレス
            </label>
          </div>
          <button class="btn btn-primary my-4" id="update_prof" type="submit" name="action" value="update_prof">更新
          </button>
        </form>
      </div>
      <!-- パスワードタブ -->
      <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
        <form method="post">
          <!-- アラートメッセージ -->
          <?php if($update_password_flg === 0 && trim($alert_password) !== '') :?>
          <div class="alert alert-danger" role="alert">
            <?= $alert_password ?>
          </div>
          <?php elseif($update_password_flg === 1 && trim($alert_password) !== '') :?>
          <div class="alert alert-success" role="alert">
            <?= $alert_password ?>
          </div>
          <?php endif; ?>
          <!-- 現在のパスワード -->
          <div class="md-form">
            <i class="fas fa-lock prefix">
            </i>
            <input type="password" id="current_password" class="form-control validate" name="current_password">
            <label for="current_password" data-error="wrong" data-success="right">現在のパスワード
            </label>
          </div>
          <!-- 新しいパスワード -->
          <div class="md-form">
            <i class="fas fa-lock prefix">
            </i>
            <input type="password" id="new_password" class="form-control validate" name="new_password">
            <label for="new_password" data-error="wrong" data-success="right">新しいパスワード
            </label>
          </div>
          <!-- 確認用のパスワード -->
          <div class="md-form">
            <i class="fas fa-lock prefix">
            </i>
            <input type="password" id="confirmation_password" class="form-control validate" name="confirmation_password">
            <label for="confirmation_password" data-error="wrong" data-success="right">確認用のパスワード
            </label>
          </div>
          <button class="btn btn-primary my-4" id="update_pass" type="submit" name="action" value="update_pass">更新
          </button>
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
