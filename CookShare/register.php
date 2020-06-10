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
//------------------------------------------------------------JavaScript取得------------------------------------------------------------
if (isset($_POST['data_ddl_categy'])) {
$data_ddl_categy = $_POST['data_ddl_categy'];
//echo 'data_ddl_categy:'.$data_ddl_categy. '----';
}
if (isset($_POST['data_materials'])) {
$data_materials = $_POST['data_materials'];
for($i=1; $i<count($data_materials)+1; $i++){
for($j=0; $j<2; $j++){
//echo $data_materials[$i][$j]. '----';
}
}
}
if (isset($_POST['data_steps'])) {
$data_steps = $_POST['data_steps'];
for($i=1; $i<count($data_steps)+1; $i++){
//echo $data_steps[$i][0]. '----';
}
}
//編集モード　recipe_id
if (isset($_POST['data_recipe_id'])) {
$data_recipe_id = $_POST['data_recipe_id'];
//echo 'data_recipe_id:'.$data_recipe_id. '----';
}
if (isset($_POST['data_access'])) {
$data_access = $_POST['data_access'];
for($i=1; $i<count($data_access)+1; $i++){
for($j=0; $j<2; $j++){
//echo $data_access[$i][$j]. '----';
}
}
}
//$data_ddl_matelials = $_POST['data_ddl_matelials'];
//echo $data_ddl_matelials;
//------------------------------------------------------------JavaScript取得------------------------------------------------------------
try {
$dbh = new PDO(DSN, DB_USER, DB_PASS);
//ログインユーザー情報　取得
$stmt_users = $dbh->prepare('SELECT * FROM users WHERE id = ?');
$stmt_users->execute([h($_SESSION['ID'])]);
$result_users = $stmt_users->fetch();
//編集モード
if(isset($_POST['recipe_id'])){
//カテゴリ　タイトル　写真　コメント　取得
$stmt_recipes = $dbh->prepare('SELECT * FROM recipes WHERE id = ?');
$stmt_recipes->execute([h($_POST['recipe_id'])]);
$result_recipes = $stmt_recipes->fetch();
//材料　取得
$stmt_materials = $dbh->prepare('SELECT * FROM materials WHERE recipe_id = ?');
$stmt_materials->execute([h($_POST['recipe_id'])]);
foreach($stmt_materials as $row2){
$rows2[] = $row2;
}
//作り方　取得
$stmt_seteps = $dbh->prepare('SELECT * FROM steps WHERE recipe_id = ?');
$stmt_seteps->execute([h($_POST['recipe_id'])]);
foreach($stmt_seteps as $row3){
$rows3[] = $row3;
}
//公開範囲　取得
$stmt_access = $dbh->prepare('SELECT * FROM access WHERE recipe_id = ?');
$stmt_access->execute([h($_POST['recipe_id'])]);
foreach($stmt_access as $row6){
$rows6[] = $row6;
}
$recipe_id = $_POST['recipe_id'];
}
//材料ドロップダウンリスト作成
foreach($dbh->query('SELECT food_category.id AS category_id, food_category.name AS category_name, foods.id AS foods_id, foods.category_id AS foods_category_id, foods.name AS foods_name FROM food_category JOIN foods ON food_category.id = foods.category_id ORDER BY food_category.id') as $row) {
$rows[] = $row;
}
//カテゴリドロップダウンリスト作成
foreach($dbh->query('SELECT cuisine_category.id AS category_id, cuisine_category.name AS category_name, cuisines.id AS cuisines_id, cuisines.category_id AS cuisines_category_id, cuisines.name AS cuisines_name FROM cuisine_category JOIN cuisines ON cuisine_category.id = cuisines.category_id ORDER BY cuisine_category.id') as $row4) {
$rows4[] = $row4;
}
//公開範囲　テーブル作成
$stmt_users2 = $dbh->prepare('SELECT * FROM  users WHERE id <> ?');
$stmt_users2->execute([h($_SESSION['ID'])]);
foreach($stmt_users2 as $row5){
$rows5[] = $row5;
}
//------------------------------------------------------------タイトル　写真　コメント------------------------------------------------------------
$alert_profile = '';
if(isset($_POST['action']) && ($_POST['action'] == 'insert' || $_POST['action'] == 'update')) {
//画像ファイル　アップロード
$tempfile = $_FILES['image']['tmp_name'];
$filename = './image/recipes/' . $_FILES['image']['name'];
//if (is_uploaded_file($tempfile)) {
//if ( move_uploaded_file($tempfile , $filename )) {
//コメント
if(h($_POST["txtComment"]) === ''){
$txtComment = null;
} else{
$txtComment = h($_POST["txtComment"]);
}
//写真
if (is_uploaded_file($tempfile)) {
if ( move_uploaded_file($tempfile , $filename )) {
//ファイルのアップロード成功の場合
$image = $_FILES['image']['name'];
}else{
//ファイルのアップロード失敗の場合
$image = null;
}
if($_POST['action'] == 'insert'){
//新規登録
$stmt_recipes = $dbh->prepare('INSERT INTO recipes (cuisine_id, title, image_url, user_id, comment) VALUES (?, ?, ?, ?, ?)');
$stmt_recipes->execute([0, h($_POST['txtTitle']), $image, h($_SESSION['ID']), $txtComment]);
} else if($_POST['action'] == 'update'){
//更新
$stmt_recipes = $dbh->prepare('UPDATE recipes SET cuisine_id = ?, title = ?, image_url = ?, comment = ? WHERE id = ?');
$stmt_recipes->execute([0, h($_POST['txtTitle']), $image, $txtComment, $_POST['recipe_id']]);
}
}else{ //ファイルが未選択の場合
if($_POST['action'] == 'insert'){
//新規登録
$image = null;
$stmt_recipes = $dbh->prepare('INSERT INTO recipes (cuisine_id, title, image_url, user_id, comment) VALUES (?, ?, ?, ?, ?)');
$stmt_recipes->execute([0, h($_POST['txtTitle']), $image, h($_SESSION['ID']), $txtComment]);
} else if($_POST['action'] == 'update'){
if($_POST['btnImgeRecipesDel_flg'] === "1") {
//更新　画像が削除された場合
$image = null;
$stmt_recipes = $dbh->prepare('UPDATE recipes SET cuisine_id = ?, title = ?, image_url = ?, comment = ? WHERE id = ?');
$stmt_recipes->execute([0, h($_POST['txtTitle']), $image, $txtComment, $_POST['recipe_id']]);
}else{
//更新　画像が変更がない場合
$stmt_recipes = $dbh->prepare('UPDATE recipes SET cuisine_id = ?, title = ?, comment = ? WHERE id = ?');
$stmt_recipes->execute([0, h($_POST['txtTitle']), $txtComment, $_POST['recipe_id']]);
}
}
}
//edit_flg 更新
if($_POST['action'] == 'update'){
$stmt_list = $dbh->prepare('UPDATE list SET edit_flg = 1 WHERE recipe_id = ?');
$stmt_list->execute([$_POST['recipe_id']]);
}
$alert_profile = '登録しました！';
header( "Location: index.php" ) ;
//$update_profile_flg = 1;
//} else {
//$alert_profile = 'ファイルをアップロードできません。';
//}
//} else {
//$alert_profile = 'ファイルが選択されていません。';
//}
}
//------------------------------------------------------------タイトル　写真　コメント------------------------------------------------------------
//------------------------------------------------------------材料・調味料　作り方------------------------------------------------------------
if(isset($data_materials) && isset($data_steps)){
//カテゴリ登録のときに必要
$_SESSION['RECIPE_ID'] = $data_recipe_id;
//更新　先にDELETE
if($data_recipe_id !== '0'){
//materials
$stmt_DELETE_materials = $dbh->prepare('DELETE FROM materials WHERE recipe_id = ?');
$stmt_DELETE_materials->execute([(int)$data_recipe_id]);
//steps
$stmt_DELETE_steps = $dbh->prepare('DELETE FROM steps WHERE recipe_id = ?');
$stmt_DELETE_steps->execute([(int)$data_recipe_id]);
}
//materials INSERT
for($i=1; $i<count($data_materials)+1; $i++){
if($data_recipe_id === '0'){
//新規登録
${"stmt_materials{$i}"} = $dbh->prepare('INSERT INTO materials (recipe_id, no, food_id, name, quantity, unit_id) VALUES ((SELECT MAX(id)+1 FROM recipes), ?, 0, ?, ?, NULL)');
${"stmt_materials{$i}"}->execute([$i, $data_materials[$i][0], $data_materials[$i][1]]);
}
else {
//更新
${"stmt_materials{$i}"} = $dbh->prepare('INSERT INTO materials (recipe_id, no, food_id, name, quantity, unit_id) VALUES (?, ?, 0, ?, ?, NULL)');
${"stmt_materials{$i}"}->execute([(int)$data_recipe_id, $i, $data_materials[$i][0], $data_materials[$i][1]]);
}
} 
//steps INSERT
for($i=1; $i<count($data_steps)+1; $i++){
if($data_recipe_id === '0'){
//新規登録
${"stmt_steps{$i}"} = $dbh->prepare('INSERT INTO steps (recipe_id, no, content) VALUES ((SELECT MAX(id)+1 FROM recipes), ?, ?)');
${"stmt_steps{$i}"}->execute([$i, $data_steps[$i][0]]);
}else {
//更新
${"stmt_steps{$i}"} = $dbh->prepare('INSERT INTO steps (recipe_id, no, content) VALUES (?, ?, ?)');
${"stmt_steps{$i}"}->execute([(int)$data_recipe_id, $i, $data_steps[$i][0]]);
}
}
}
//------------------------------------------------------------材料・調味料　作り方------------------------------------------------------------
//------------------------------------------------------------公開範囲------------------------------------------------------------
if(isset($data_access)){
if($data_recipe_id !== '0'){
//access
$stmt_DELETE_access = $dbh->prepare('DELETE FROM access WHERE recipe_id = ?');
$stmt_DELETE_access->execute([(int)$data_recipe_id]);
}
for($i=1; $i<count($data_access)+1; $i++){
if($data_recipe_id === '0'){
if($data_access[$i][0] === 'on'){
//新規登録
${"stmt_access{$i}"} = $dbh->prepare('INSERT INTO access (recipe_id, user_id) VALUES ((SELECT MAX(id)+1 FROM recipes), ?)');
${"stmt_access{$i}"}->execute([(int)$data_access[$i][1]]);
}
}
else {
if($data_access[$i][0] === 'on'){
//更新
${"stmt_access{$i}"} = $dbh->prepare('INSERT INTO access (recipe_id, user_id) VALUES (?, ?)');
${"stmt_access{$i}"}->execute([(int)$data_recipe_id, (int)$data_access[$i][1]]);
}
}
}
}
//------------------------------------------------------------公開範囲------------------------------------------------------------
//------------------------------------------------------------カテゴリ------------------------------------------------------------
if(isset($data_ddl_categy)){
$_SESSION['CATEGORY'] = $data_ddl_categy;
}
if(isset($_SESSION['CATEGORY']) && isset($_SESSION['RECIPE_ID'])){
if($_SESSION['RECIPE_ID'] === '0'){
if(isset($_SESSION['CATEGORY'])){
$stmt_recipes_category = $dbh->prepare('UPDATE recipes SET cuisine_id = ? ORDER BY id DESC LIMIT 1');
$stmt_recipes_category->execute([(int)$_SESSION['CATEGORY']]);
}
} else{
$stmt_recipes_category = $dbh->prepare('UPDATE recipes SET cuisine_id = ? WHERE id = ?');
$stmt_recipes_category->execute([(int)$_SESSION['CATEGORY'], (int)$_SESSION['RECIPE_ID']]);
}
}
//------------------------------------------------------------カテゴリ------------------------------------------------------------
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
                <img src="image/users/noimage.jpg" class="rounded-circle z-depth-0" alt="avatar image" height="20">
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
    <!--
<div class="popup" id="js-popup">
<div class="popup-inner">
<div class="modal fade" id="modal_category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">
<div class="modal-dialog modal-notify modal-info modal-lg modal-dialog-scrollable" role="document">
<div class="modal-content">
<div class="modal-header">
<p class="heading lead">レシピ新規登録　Step.1
</p>
<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload();">
<span aria-hidden="true" class="white-text">×
</span>
</button>
</div>
<div class="modal-body">
<div class="text-center" action="#!">
<div class="card">
<h3 class="card-header text-center font-weight-bold text-uppercase py-4">カテゴリ設定
</h3>
<div class="card-body">
<div class="dropdown hierarchy-select select" id="ddl_categy">
<button type="button" class="btn btn-secondary dropdown-toggle" id="example-one-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
</button>
<div class="dropdown-menu" aria-labelledby="example-one-button">
<div class="hs-searchbox">
<input type="text" class="form-control" autocomplete="off">
</div>
<div class="hs-menu-inner">
<a class="dropdown-item" data-value="0" data-level="1" data-default-selected="" href="#">選択してください
</a>
<?php $old_category_id = '' ?>
<?php foreach($rows4 as $row4): ?>
<?php if($old_category_id !== $row4['category_id']): ?>
<a class="dropdown-item no_link" data-value="<?= $row4['category_id']?>" data-level="1">
<?= $row4['category_name']?>
</a>
<a class="dropdown-item" data-value="<?= $row4['cuisines_id']?>" data-level="2" href="#">
<?= $row4['cuisines_name']?>
</a>
<?php else: ?>
<a class="dropdown-item" data-value="<?= $row4['cuisines_id']?>" data-level="2" href="#">
<?= $row4['cuisines_name']?>
</a>
<?php endif; ?>
<?php $old_category_id = $row4['category_id'] ?>
<?php endforeach; ?>
</div>
</div>
<?php if(!isset($result_recipes['cuisine_id'])): ?>
<input class="d-none" name="ddl_categy" readonly="readonly" aria-hidden="true" type="text">
<?php else: ?>
<input class="d-none" name="ddl_categy" readonly="readonly" aria-hidden="true" type="text" value="<?= $result_recipes['cuisine_id']?>">
<input type="hidden" name="cuisine_id" value="<?= $result_recipes['cuisine_id']?>">
<?php endif; ?>
<p>
</p>
</div>
</div>
</div>
<div class="modal-footer justify-content-center">
<button class="btn btn-info my-4" data-toggle="modal" data-target="#modal_title" data-dismiss="modal" type="submit" id="btnTitle">タイトルの入力へ進む　＞＞
</button>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="black-background" id="js-black-bg">
</div>
</div>
-->
    <div class="text-center">
      <a href="" class="btn btn-default btn-rounded mb-4" data-toggle="modal" data-target="#modal_category">
        <?php if(!isset($_POST['recipe_id'])): ?> レシピ登録を始める
        <?php else: ?> レシピ編集を始める
        <?php endif; ?>
      </a>
    </div>
    <form method="post" enctype="multipart/form-data">
      <!--modal_category-->
      <div class="modal fade" id="modal_category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">
        <div class="modal-dialog modal-notify modal-info modal-lg modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <p class="heading lead">レシピ新規登録　Step.1
              </p>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload();">
                <span aria-hidden="true" class="white-text">×
                </span>
              </button>
            </div>
            <div class="modal-body">
              <div class="text-center" action="#!">
                <div class="card">
                  <h3 class="card-header text-center font-weight-bold text-uppercase py-4">カテゴリ設定
                  </h3>
                  <div class="card-body">
                    <div class="dropdown hierarchy-select" id="ddl_categy">
                      <button type="button" class="btn btn-secondary dropdown-toggle" id="example-one-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      </button>
                      <div class="dropdown-menu" aria-labelledby="example-one-button">
                        <div class="hs-searchbox">
                          <input type="text" class="form-control" autocomplete="off">
                        </div>
                        <div class="hs-menu-inner">
                          <a class="dropdown-item" data-value="0" data-level="1" data-default-selected="" href="#">選択してください
                          </a>
                          <?php $old_category_id = '' ?>
                          <?php foreach($rows4 as $row4): ?>
                          <?php if($old_category_id !== $row4['category_id']): ?>
                          <a class="dropdown-item no_link" data-value="<?= $row4['category_id']?>" data-level="1">
                            <?= $row4['category_name']?>
                          </a>
                          <a class="dropdown-item" data-value="<?= $row4['cuisines_id']?>" data-level="2" href="#">
                            <?= $row4['cuisines_name']?>
                          </a>
                          <?php else: ?>
                          <a class="dropdown-item" data-value="<?= $row4['cuisines_id']?>" data-level="2" href="#">
                            <?= $row4['cuisines_name']?>
                          </a>
                          <?php endif; ?>
                          <?php $old_category_id = $row4['category_id'] ?>
                          <?php endforeach; ?>
                        </div>
                      </div>
                      <?php if(!isset($result_recipes['cuisine_id'])): ?>
                      <input class="d-none" name="ddl_categy" readonly="readonly" aria-hidden="true" type="text">
                      <?php else: ?>
                      <input class="d-none" name="ddl_categy" readonly="readonly" aria-hidden="true" type="text" value="<?= $result_recipes['cuisine_id']?>">
                      <input type="hidden" name="cuisine_id" value="<?= $result_recipes['cuisine_id']?>">
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <div class="modal-footer justify-content-center">
                  <button class="btn btn-info my-4" data-toggle="modal" data-target="#modal_title" data-dismiss="modal" type="submit" id="btnTitle" disabled="true">タイトルの入力へ進む　＞＞
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--modal_title-->
      <div class="modal fade" id="modal_title" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">
        <div class="modal-dialog modal-notify modal-info modal-lg modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <p class="heading lead">レシピ新規登録　Step.2
              </p>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload();">
                <span aria-hidden="true" class="white-text">×
                </span>
              </button>
            </div>
            <div class="modal-body">
              <div class="text-center" action="#!">
                <div class="card">
                  <h3 class="card-header text-center font-weight-bold text-uppercase py-4">タイトルの入力
                  </h3>
                  <div class="card-body">
                    <?php if(!isset($result_recipes['title'])): ?>
                    <input type="text" id="txtTitle" name="txtTitle" class="form-control mb-4" placeholder="例）具沢山カレーライス">
                    <?php else: ?>
                    <input type="text" id="txtTitle" name="txtTitle" class="form-control mb-4" placeholder="例）具沢山カレーライス" value="<?= $result_recipes['title']?>">
                    <?php endif; ?>
                  </div>
                </div>
                <div class="modal-footer justify-content-center">
                  <button class="btn btn-info my-4" data-toggle="modal" data-target="#modal_category" data-dismiss="modal" type="submit" id="btnCategory">＜＜　カテゴリの設定へ戻る
                  </button>
                  <button class="btn btn-info my-4" data-toggle="modal" data-target="#modal_image" data-dismiss="modal" type="submit" id="btnImage" disabled="true">写真の登録へ進む　＞＞
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--modal_image-->
      <div class="modal fade" id="modal_image" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">
        <div class="modal-dialog modal-notify modal-info modal-lg modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <p class="heading lead">レシピ新規登録　Step.3
              </p>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload();">
                <span aria-hidden="true" class="white-text">×
                </span>
              </button>
            </div>
            <div class="modal-body">
              <div class="" action="#!">
                <div class="card">
                  <h3 class="card-header text-center font-weight-bold text-uppercase py-4">写真の登録（任意）
                  </h3>
                  <div class="card-body">
                    <!-- サムネイル -->
                    <?php if(isset($result_recipes['image_url'])): ?>
                    <img id="thumbnail" src="image/recipes/<?= $result_recipes['image_url']?>" alt="avatar" class="avatar rounded-circle d-flex align-self-center mr-2 z-depth-1" width="50" height="50">
                    <?php else:?>
                    <img id="thumbnail" src="image/recipes/noimage.png" alt="avatar" class="avatar rounded-circle d-flex align-self-center mr-2 z-depth-1" width="50" height="50">
                    <!--<img id="thumbnail" src="" alt="avatar" class="avatar rounded-circle d-flex align-self-center mr-2 z-depth-1" width="50" height="50">-->
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
                    <br>
                    <button type="button" class="btn btn-danger" id="btnImgeRecipesDel">写真を削除
                    </button>
                    <input type="hidden" id="btnImgeRecipesDel_flg" name="btnImgeRecipesDel_flg">
                  </div>
                </div>
                <div class="modal-footer justify-content-center">
                  <button class="btn btn-info my-4" data-toggle="modal" data-target="#modal_title" data-dismiss="modal" type="submit">＜＜　タイトルの入力へ戻る
                  </button>
                  <button class="btn btn-info my-4 btn_modal" data-toggle="modal" data-target="#modal_material" data-dismiss="modal" type="submit" id="btnMaterials">材料・調味料の入力へ進む　＞＞
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--modal_material-->
      <div class="modal fade" id="modal_material" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">
        <div class="modal-dialog modal-notify modal-info modal-lg modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <p class="heading lead">レシピ新規登録　Step.4
              </p>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload();">
                <span aria-hidden="true" class="white-text">×
                </span>
              </button>
            </div>
            <div class="modal-body">
              <div class="text-center" action="#!">
                <div class="card">
                  <h3 class="card-header text-center font-weight-bold text-uppercase py-4">材料・調味料の入力
                  </h3>
                  <div class="card-body">
                    <div id="table" class="table-editable">
                      <table class="table table-bordered table-responsive-md table-striped text-center">
                        <thead>
                          <tr>
                            <th class="text-center">材料・調味料
                            </th>
                            <th class="text-center">分量
                            </th>
                            <th class="text-center">
                            </th>
                            <th class="text-center">
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <!--編集モード-->
                          <?php if(isset($_POST['recipe_id'])): ?>
                          <?php foreach($rows2 as $row2): ?>
                          <tr class="hide">
                            <td class="pt-3-half">
                              <input type="text" id="txtMaterial" name="txtMaterial" class="form-control mb-4" placeholder="例）にんじん" value="<?= $row2['name']?>">
                              <!--
<div class="dropdown hierarchy-select select" id="ddl_matelials">
<button type="button" class="btn btn-secondary dropdown-toggle" id="example-one-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
</button>
<div class="dropdown-menu" aria-labelledby="example-one-button">
<div class="hs-searchbox">
<input type="text" class="form-control" autocomplete="off">
</div>
<div class="hs-menu-inner">
<a class="dropdown-item" data-value="0" data-level="1" data-default-selected="" href="#">選択してください
</a>
<?php $old_category_id = '' ?>
<?php foreach($rows as $row): ?>
<?php if($old_category_id !== $row['category_id']): ?>
<a class="dropdown-item no_link" data-value="<?= $row['category_id']?>" data-level="1">
<?= $row['category_name']?>
</a>
<a class="dropdown-item" data-value="<?= $row['foods_id']?>" data-level="2" href="#">
<?= $row['foods_name']?>
</a>
<?php else: ?>
<a class="dropdown-item" data-value="<?= $row['foods_id']?>" data-level="2" href="#">
<?= $row['foods_name']?>
</a>
<?php endif; ?>
<?php $old_category_id = $row['category_id'] ?>
<?php endforeach; ?>
</div>
</div>
<input class="d-none" name="ddl_matelials" readonly="readonly" aria-hidden="true" type="text" value="<?= $row2['food_id']?>">
<input type="hidden" name="food_id" value="<?= $row2['food_id']?>">
<p>
</p>
</div>
-->
                            </td>
                            <td class="pt-3-half">
                              <input type="text" id="txtQuantity" name="txtQuantity" class="form-control mb-4" value="<?= $row2['quantity']?>">
                            </td>
                            <td class="pt-3-half">
                              <span class="table-up">
                                <a href="#!" class="indigo-text">
                                  <i class="fas fa-long-arrow-alt-up" aria-hidden="true">
                                  </i>
                                </a>
                              </span>
                              <span class="table-down">
                                <a href="#!" class="indigo-text">
                                  <i class="fas fa-long-arrow-alt-down" aria-hidden="true">
                                  </i>
                                </a>
                              </span>
                            </td>
                            <td>
                              <span class="table-remove">
                                <button type="button" class="btn btn-danger btn-rounded btn-sm my-0">削除
                                </button>
                              </span>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                          <!--新規モード-->
                          <?php else: ?>
                          <tr class="hide">
                            <td class="pt-3-half">
                              <input type="text" id="txtMaterial" name="txtMaterial" class="form-control mb-4" placeholder="例）にんじん">
                              <!--修正中 ドロップダウンリスト
<div class="dropdown hierarchy-select" id="ddl_matelials">
<button type="button" class="btn btn-secondary dropdown-toggle" id="example-one-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
</button>
<div class="dropdown-menu" aria-labelledby="example-one-button">
<div class="hs-searchbox">
<input type="text" class="form-control" autocomplete="off">
</div>
<div class="hs-menu-inner">
<a class="dropdown-item" data-value="0" data-level="1" data-default-selected="" href="#">選択してください
</a>
<?php $old_category_id = '' ?>
<?php foreach($rows as $row): ?>
<?php if($old_category_id !== $row['category_id']): ?>
<a class="dropdown-item no_link" data-value="<?= $row['category_id']?>" data-level="1">
<?= $row['category_name']?>
</a>
<a class="dropdown-item" data-value="<?= $row['foods_id']?>" data-level="2" href="#">
<?= $row['foods_name']?>
</a>
<?php else: ?>
<a class="dropdown-item" data-value="<?= $row['foods_id']?>" data-level="2" href="#">
<?= $row['foods_name']?>
</a>
<?php endif; ?>
<?php $old_category_id = $row['category_id'] ?>
<?php endforeach; ?>
</div>
</div>
<input class="d-none" name="ddl_matelials" readonly="readonly" aria-hidden="true" type="text">
<p>
</p>
</div>
修正中 ドロップダウンリスト-->
                            </td>
                            <td class="pt-3-half">
                              <input type="text" id="txtQuantity" name="txtQuantity" class="form-control mb-4" placeholder="例）1">
                            </td>
                            <td class="pt-3-half">
                              <span class="table-up">
                                <a href="#!" class="indigo-text">
                                  <i class="fas fa-long-arrow-alt-up" aria-hidden="true">
                                  </i>
                                </a>
                              </span>
                              <span class="table-down">
                                <a href="#!" class="indigo-text">
                                  <i class="fas fa-long-arrow-alt-down" aria-hidden="true">
                                  </i>
                                </a>
                              </span>
                            </td>
                            <td>
                              <span class="table-remove">
                                <button type="button" class="btn btn-danger btn-rounded btn-sm my-0">削除
                                </button>
                              </span>
                            </td>
                          </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                      <span class="table-add float-left mb-3 mr-2">
                        <a href="#!" class="text-success">
                          <i class="fas fa-plus fa-2x" aria-hidden="true">行を追加する
                          </i>
                        </a>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="modal-footer justify-content-center">
                  <button class="btn btn-info my-4" data-toggle="modal" data-target="#modal_image" data-dismiss="modal" type="submit">＜＜　写真の登録へ戻る
                  </button>
                  <button class="btn btn-info my-4 btn_modal" data-toggle="modal" data-target="#modal_step" data-dismiss="modal" type="submit" id="btnSteps" disabled="true">作り方の入力へ進む　＞＞
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--modal_step-->
      <div class="modal fade" id="modal_step" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">
        <div class="modal-dialog modal-notify modal-info modal-lg modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <p class="heading lead">レシピ新規登録　Step.5
              </p>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload();">
                <span aria-hidden="true" class="white-text" >×
                </span>
              </button>
            </div>
            <div class="modal-body">
              <div class="text-center" action="#!">
                <div class="card">
                  <h3 class="card-header text-center font-weight-bold text-uppercase py-4">作り方の入力
                  </h3>
                  <div class="card-body">
                    <div id="tb_step" class="table-editable">
                      <table class="table table-bordered table-responsive-md table-striped text-center">
                        <thead>
                          <tr>
                            <th class="text-center">手順
                            </th>
                            <th class="text-center">
                            </th>
                            <th class="text-center">
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if(isset($_POST['recipe_id'])): ?>
                          <?php foreach($rows3 as $row3): ?>
                          <tr class="hide">
                            <td class="pt-3-half">
                              <div class="col-auto">
                                <label class="sr-only" for="inlineFormInputGroup">
                                </label>
                                <div class="input-group mb-2">
                                  <div class="input-group-prepend">
                                    <div class="input-group-text">
                                      <?= $row3['no']?>
                                    </div>
                                  </div>
                                  <input type="text" class="form-control py-0" id="txtStep" name="txtStep" placeholder="例）にんじんを千切りします。" value="<?= $row3['content']?>">
                                </div>
                              </div>
                            </td>
                            <td class="pt-3-half">
                              <span class="table-up">
                                <a href="#!" class="indigo-text">
                                  <i class="fas fa-long-arrow-alt-up" aria-hidden="true">
                                  </i>
                                </a>
                              </span>
                              <span class="table-down">
                                <a href="#!" class="indigo-text">
                                  <i class="fas fa-long-arrow-alt-down" aria-hidden="true">
                                  </i>
                                </a>
                              </span>
                            </td>
                            <td>
                              <span class="table-remove">
                                <button type="button" class="btn btn-danger btn-rounded btn-sm my-0">削除
                                </button>
                              </span>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                          <?php else: ?>
                          <tr class="hide">
                            <td class="pt-3-half">
                              <div class="col-auto">
                                <label class="sr-only" for="inlineFormInputGroup">
                                </label>
                                <div class="input-group mb-2">
                                  <div class="input-group-prepend">
                                    <div class="input-group-text">1
                                    </div>
                                  </div>
                                  <input type="text" class="form-control py-0" id="txtStep" name="txtStep" placeholder="例）にんじんを千切りします。">
                                </div>
                              </div>
                            </td>
                            <td class="pt-3-half">
                              <span class="table-up">
                                <a href="#!" class="indigo-text">
                                  <i class="fas fa-long-arrow-alt-up" aria-hidden="true">
                                  </i>
                                </a>
                              </span>
                              <span class="table-down">
                                <a href="#!" class="indigo-text">
                                  <i class="fas fa-long-arrow-alt-down" aria-hidden="true">
                                  </i>
                                </a>
                              </span>
                            </td>
                            <td>
                              <span class="table-remove">
                                <button type="button" class="btn btn-danger btn-rounded btn-sm my-0">削除
                                </button>
                              </span>
                            </td>
                          </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                      <span class="table-add float-left mb-3 mr-2">
                        <a href="#!" class="text-success">
                          <i class="fas fa-plus fa-2x" aria-hidden="true">行を追加する
                          </i>
                        </a>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="modal-footer justify-content-center">
                  <button class="btn btn-info my-4 btn_modal" data-toggle="modal" data-target="#modal_material" data-dismiss="modal" type="submit">＜＜　材料の入力へ戻る
                  </button>
                  <button class="btn btn-info my-4" data-toggle="modal" data-target="#modal_comment" data-dismiss="modal" type="submit" id="btnComment" disabled="true">コメントの入力へ進む　＞＞
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--modal_comment-->
      <div class="modal fade" id="modal_comment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">
        <div class="modal-dialog modal-notify modal-info modal-lg modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <p class="heading lead">レシピ新規登録　Step.6
              </p>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload();">
                <span aria-hidden="true" class="white-text">×
                </span>
              </button>
            </div>
            <div class="modal-body">
              <div class="text-center" action="#!">
                <div class="card">
                  <h3 class="card-header text-center font-weight-bold text-uppercase py-4">コメントの入力（任意）
                  </h3>
                  <div class="card-body">
                    <?php if(!isset($result_recipes['comment'])): ?>
                    <input type="text" id="txtComment" name="txtComment" class="form-control mb-4" placeholder="例）簡単に作れるよ！">
                    <?php else: ?>
                    <input type="text" id="txtComment" name="txtComment" class="form-control mb-4" placeholder="例）簡単に作れるよ！" value="<?= $result_recipes['comment']?>">
                    <?php endif; ?>
                  </div>
                </div>
                <div class="modal-footer justify-content-center">
                  <button class="btn btn-info my-4 btn_modal" data-toggle="modal" data-target="#modal_step" data-dismiss="modal" type="submit">＜＜　作り方の入力に戻る
                  </button>
                  <button class="btn btn-info my-4" data-toggle="modal" data-target="#modal_access" data-dismiss="modal" type="submit" id="btnAccess">公開範囲の設定へ進む　＞＞
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--modal_access-->
      <div class="modal fade" id="modal_access" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">
        <div class="modal-dialog modal-notify modal-info modal-lg modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <p class="heading lead">レシピ新規登録　Step.7
              </p>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload();">
                <span aria-hidden="true" class="white-text">×
                </span>
              </button>
            </div>
            <div class="modal-body">
              <div class="text-center" action="#!">
                <div class="card">
                  <h3 class="card-header text-center font-weight-bold text-uppercase py-4">公開範囲の設定
                  </h3>
                  <div class="card-body">
                    <h4 class="text-center font-weight-bold text-uppercase py-4">レシピを公開したいユーザを選択してください
                    </h4>
                    <div id="tb_access">
                      <table class="table">
                        <thead class="grey lighten-3">
                          <tr>
                            <th scope="col" class="text-center">
                              <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="allChecked" id="all">
                                <label class="custom-control-label" for="all">
                                </label>
                              </div>
                              <!--<label for="all"><input type="checkbox" name="allChecked" id="all"></label>-->
                            </th>
                            <th scope="col">ユーザ名
                            </th>
                          </tr>
                        </thead>
                        <tbody id="boxes">
                          <?php foreach($rows5 as $row5): ?>
                          <tr>
                            <td>
                              <div class="custom-control custom-switch">
                                <?php if(isset($rows6)): ?>
                                <?php foreach($rows6 as $row6): ?>
                                <?php if($row5['id'] === $row6['user_id']): ?>
                                <input type="checkbox" class="custom-control-input" id="<?= $row5['id'] ?>" name="chk[]" checked>
                                <?php else: ?>
                                <input type="checkbox" class="custom-control-input" id="<?= $row5['id'] ?>" name="chk[]">
                                <?php endif; ?>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <input type="checkbox" class="custom-control-input" id="<?= $row5['id'] ?>" name="chk[]">
                                <?php endif; ?>
                                <label class="custom-control-label" for="<?= $row5['id'] ?>">
                                </label>
                              </div>
                              <!--<label><input type="checkbox" name="chk[]"></label>-->
                            </td>
                            <td>
                              <?php if(trim($row5['image_path']) !== ''): ?>
                              <img src="image/users/<?= $row5['image_path'] ?>" class="rounded-circle z-depth-0" alt="avatar image" height="50">
                              <?php elseif(trim($row5['image_path']) === ''): ?>
                              <img src="image/users/noimage.jpg" class="rounded-circle z-depth-0" alt="avatar image" height="50">
                              <?php else: ?>
                              <?php endif; ?>
                              <input type="hidden" value="<?= $row5['id']?>">
                              <?= $row5['name']?>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="modal-footer justify-content-center">
                  <button class="btn btn-info my-4" data-toggle="modal" data-target="#modal_comment" data-dismiss="modal" type="submit" id="btnCategory">＜＜　コメントの入力へ戻る
                  </button>
                  <?php if(isset($_POST['recipe_id'])): ?>
                  <button class="btn btn-primary my-4 registration" id="update" type="submit" name="action" value="update">更新
                  </button>
                  <input id="recipe_id" type="hidden" name="recipe_id" value="<?= $_POST['recipe_id'] ?>">
                  <?php else: ?>
                  <button class="btn btn-primary my-4 registration" id="insert" type="submit" name="action" value="insert">登録
                  </button>
                  <input id="recipe_id" type="hidden" name="recipe_id" value="">
                  <?php endif; ?>
                  <!--<input class="btn btn-info my-4 btn_modal" id="insert" type="submit" name="action" value="insert">-->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
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
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js">
    </script>
    <script>
      //入力された材料、作り方を取得
      $(document).ready(function() {
        //カテゴリ　ドロップダウンリスト
        var $ddl_categy;
        $('#ddl_categy').hierarchySelect({
          width: 'auto',
          initialValueSet: true,
          onChange: function(value) {
            console.log('ddl_categy: ' + value);
            $ddl_categy = value;
            //btnTitle 活性・非活性
            if(value===0) {
              $('#btnTitle').prop('disabled', true);
            }
            else {
              $('#btnTitle').prop('disabled', false);
            }
          }
        }
                                        );
        //材料　ドロップダウンリスト
        $('#ddl_matelials').hierarchySelect({
          hierarchy: false,
          search: false,
          width: 200,
          initialValueSet: true,
          onChange: function (value) {
            console.log('ddl_matelials: "' + value + '"');
            $ddl_matelials = value;
          }
        }
                                           );
        //登録　更新ボタン押下時
        $('.registration').click(function() {
          //更新処理の場合　recipe_idを取得
          var recipe_id = document.getElementById('recipe_id').value;
          if (recipe_id === "" || recipe_id === null || recipe_id === undefined) {
            recipe_id = 0;
          }
          //modal_material
          var materials = [];
          var tr = $("#table tr");
          for (var i = 0, l = tr.length; i < l; i++) {
            var cells = tr.eq(i).children();
            for (var j = 0, m = cells.length; j < m; j++) {
              if (typeof materials[i] == "undefined")
                materials[i] = [];
              //materials[i][j] = cells.eq(j).text();
              materials[i][j] = cells.eq(j).find('input').val();
            }
          }
          //modal_step
          var steps = [];
          var tr2 = $("#tb_step tr");
          for (var i = 0, l = tr2.length; i < l; i++) {
            var cells = tr2.eq(i).children();
            if (typeof steps[i] == "undefined")
              steps[i] = [];
            steps[i][0] = cells.eq(0).find('input').val();
          }
          //modal_access
          var access = [];
          var tr3 = $("#tb_access tr");
          for (var i = 0, l = tr3.length; i < l; i++) {
            var cells = tr3.eq(i).children();
            if (typeof access[i] == "undefined")
              access[i] = [];
            if(cells.eq(0).find('input:checked').val() == "on"){
              access[i][0] = "on";
              access[i][1] = cells.eq(1).find('input').val();
            }
            else{
              access[i][0] = "off";
              access[i][1] = cells.eq(1).find('input').val();
            }
          }
          //postメソッドで送るデータを定義 var data = {パラメータ名 : 値};
          var data = {
            data_materials: materials,
            data_steps: steps,
            data_ddl_categy: $ddl_categy,
            data_recipe_id: recipe_id,
            data_access: access,
            //data_ddl_matelials: $ddl_matelials
          };
          $.ajax({
            type: "post",
            url: "register.php",
            data: data,
            //Ajax通信が成功した場合
            success: function(data, dataType) {
              //PHPから返ってきたデータの表示
              //alert(data);
              //送信完了後フォームの内容をリセット
              if (data == "送信が完了しました") {
                //alert(data);
              }
              else {
              }
            }
            ,
            //Ajax通信が失敗した場合のメッセージ
            error: function() {
              //alert('送信が失敗しました。');
            }
          }
                );
          //return false;
        }
                                );
      }
                       );
    </script>
    <!-- Hierarchy Select core JavaScript 
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>-->
    <script src="SELECT/dist/hierarchy-select.min.js">
    </script>
    </body>
</html>
