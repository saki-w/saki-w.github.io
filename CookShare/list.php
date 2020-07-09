<?php
//エラーログ
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
function h($s)
{
return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}
require_once('config.php');
session_start();
$id = h($_SESSION['ID']);
$email = h($_SESSION['EMAIL']);
$user_name = h($_SESSION['NAME']);
if (isset($_POST['data_list'])) {
$data_list = $_POST['data_list'];
for ($i=0; $i<count($data_list); $i++) {
for ($j=0; $j<3; $j++) {
//echo $data_list[$i][$j]. '<br>';
}
}
}
if (isset($_POST['data_list_count'])) {
$data_list_count = $_POST['data_list_count'];
//echo 'data_list_count:'.$data_list_count. '<br>';
}
if (isset($_POST['data_recipe_id'])) {
$data_recipe_id = $_POST['data_recipe_id'];
//echo 'data_recipe_id:'.$data_recipe_id. '<br>';
}
if (isset($_POST['data_unit_id'])) {
$data_unit_id = $_POST['data_unit_id'];
//echo 'data_unit_id:'.$data_unit_id. '----';
}
$count = 0;
try {
$dbh = new PDO(DSN, DB_USER, DB_PASS);
//ログインユーザー情    報　取得
$stmt_users = $dbh->prepare('SELECT * FROM users WHERE id = ?');
$stmt_users->execute([h($_SESSION['ID'])]);
$result_users = $stmt_users->fetch();
$alert = '';
/*
//自分で登録したレシピを買い物リストに登録する場合、DELETE→INSERT
if(isset($_POST['index']) || isset($_POST['favorites'])){
$stmt_DELETE_list_3 = $dbh->prepare('DELETE FROM list WHERE recipe_id IN (SELECT id FROM recipes WHERE user_id = ?) AND edit_flg = 1');
$stmt_DELETE_list_3->execute([h($_SESSION['ID'])]);
$stmt_INSERT_list2 = $dbh->prepare('INSERT INTO list( food_id, quantity, unit_id, recipe_id, category_id, user_id ) SELECT food_id, quantity, materials.unit_id, recipe_id, foods.category_id, :user_id FROM materials JOIN foods ON  materials.food_id = foods.id WHERE materials.recipe_id IN( SELECT id FROM recipes WHERE user_id = :user_id )');
$stmt_INSERT_list2->bindParam(':user_id',$id, PDO::PARAM_INT);
$stmt_INSERT_list2->execute();
}
//メイン検索
$stmt_list = $dbh->prepare('SELECT list.id, foods.id AS foods_id, foods.name AS foods_name, list.unit_id, REPLACE(list.unit_id, 7, 6) AS unit_id_ddl, list.quantity, unit.name AS unit_name, list.recipe_id FROM list JOIN foods ON  list.food_id = foods.id JOIN unit ON  foods.unit_id = unit.id JOIN users ON  list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id JOIN access ON  list.recipe_id = access.recipe_id AND list.user_id = access.user_id WHERE users.id = :user_id UNION SELECT list.id, foods.id AS foods_id, foods.name, list.unit_id, REPLACE(list.unit_id, 7, 6) AS unit_id_ddl, list.quantity, unit.name, list.recipe_id FROM list JOIN foods ON  list.food_id = foods.id JOIN unit ON  foods.unit_id = unit.id JOIN users ON  list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id WHERE users.id = :user_id AND recipes.delete_flg = 0 ORDER BY recipe_id, id');
//集計した表
$stmt_list = $dbh->prepare('SELECT foods_id, foods_name, unit_id, unit_id_ddl, unit_name, SUM(quantity) AS quantity, flg FROM ( SELECT foods.id AS foods_id, foods.name AS foods_name, REPLACE(list.unit_id, 7, 6) AS unit_id, REPLACE(list.unit_id, 7, 6) AS unit_id_ddl, list.quantity, unit.name AS unit_name, (CASE REPLACE(list.unit_id, 7, 6) WHEN 6 THEN 6 ELSE 0 END) AS flg FROM list JOIN foods ON  list.food_id = foods.id JOIN unit ON  foods.unit_id = unit.id JOIN users ON  list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id JOIN access ON  list.recipe_id = access.recipe_id AND list.user_id = access.user_id WHERE users.id = :user_id UNION ALL SELECT foods.id AS foods_id, foods.name, REPLACE(list.unit_id, 7, 6) AS unit_id, REPLACE(list.unit_id, 7, 6) AS unit_id_ddl, unit.name, list.quantity, (CASE REPLACE(list.unit_id, 7, 6) WHEN 6 THEN 6 ELSE 0 END) AS flg FROM list JOIN foods ON  list.food_id = foods.id JOIN unit ON  foods.unit_id = unit.id JOIN users ON  list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id WHERE users.id = :user_id AND recipes.delete_flg = 0 ) AS main GROUP BY foods_id, foods_name, unit_id, unit_id_ddl, unit_name, flg');
*/
if (isset($_SESSION['INDEX']) || isset($_SESSION['FAVORITES']) || isset($_SESSION['FLG'])) {
unset($_SESSION['FLG']);
$stmt_DELETE_list2 = $dbh->prepare('DELETE FROM list2 WHERE user_id = ? AND original_flg = 0');
$stmt_DELETE_list2->execute([h($_SESSION['ID'])]);
//INSERT list2
$stmt_INSERT_list2 = $dbh->prepare('INSERT INTO list2( foods_id, unit_id, quantity, user_id, original_flg ) SELECT food_id, unit_id, quantity, :user_id, 0 FROM list WHERE user_id = :user_id');
$stmt_INSERT_list2->bindParam(':user_id', $id, PDO::PARAM_INT);
$stmt_INSERT_list2->execute();
}
//メイン検索
$stmt_list2 = $dbh->prepare('SELECT list2.foods_id, foods.name AS foods_name, list2.unit_id, unit.name AS unit_name, list2.quantity, list2.original_flg, food_category.id AS category_id, food_category.name AS category_name FROM list2 JOIN foods ON  list2.foods_id = foods.id JOIN unit ON  list2.unit_id = unit.id JOIN food_category ON  foods.category_id = food_category.id WHERE user_id = ? ORDER BY food_category.id');
$stmt_list2->execute([h($_SESSION['ID'])]);
foreach ($stmt_list2 as $row) {
$rows[] = $row;
}
//サムネイル表示
$stmt_list = $dbh->prepare('SELECT list.id, foods.id AS foods_id, foods.name AS foods_name, list.unit_id, list.quantity, unit.name AS unit_name, list.recipe_id, recipes.image_url, recipes.title FROM list JOIN foods ON  list.food_id = foods.id JOIN unit ON  foods.unit_id = unit.id JOIN users ON  list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id JOIN access ON  list.recipe_id = access.recipe_id AND list.user_id = access.user_id WHERE users.id = :user_id UNION SELECT list.id, foods.id AS foods_id, foods.name, list.unit_id, list.quantity, unit.name, list.recipe_id, recipes.image_url, recipes.title FROM list JOIN foods ON  list.food_id = foods.id JOIN unit ON  foods.unit_id = unit.id JOIN users ON  list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id WHERE users.id = :user_id AND recipes.delete_flg = 0 ORDER BY recipe_id, id');
$stmt_list->bindParam(':user_id', $id, PDO::PARAM_INT);
$stmt_list->execute();
foreach ($stmt_list as $row) {
$rows5[] = $row;
}
//更新されたレシピを取得
$stmt_edit_flg = $dbh->prepare('SELECT list.recipe_id, recipes.title, list.edit_flg FROM list JOIN users ON  list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id JOIN access ON  list.recipe_id = access.recipe_id AND list.user_id = access.user_id WHERE users.id = :user_id AND recipes.user_id <> :user_id UNION SELECT list.recipe_id, recipes.title, list.edit_flg FROM list JOIN users ON  list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id WHERE users.id = :user_id AND recipes.user_id = :user_id GROUP BY recipe_id, title, edit_flg ORDER BY recipe_id');
$stmt_edit_flg->bindParam(':user_id', $id, PDO::PARAM_INT);
$stmt_edit_flg->execute();
$edit_alert = '';
$edit_alert .= '下記のレシピが変更されました。買い物リストも変更しますか？<br>';
$edit_count = 0;
foreach ($stmt_edit_flg as $row) {
$rows6[] = $row;
}
if(isset($rows6)){
foreach ($rows6 as $row) {
if ($row['edit_flg'] === '1') {
$edit_alert .= '<br>・'.$row['title'];
$edit_count++;
}
}
}
//更新アラート　OKボタン
if (isset($_POST['action']) && $_POST['action'] == 'update') {
if(isset($rows6)){
foreach ($rows6 as $row) {
$stmt_DELETE_list = $dbh->prepare('DELETE FROM list WHERE user_id = ? AND recipe_id = ?');
$stmt_DELETE_list->execute([h($_SESSION['ID']), (int)$row['recipe_id']]);
$stmt_DELETE_list2 = $dbh->prepare('DELETE FROM list2 WHERE user_id = ? AND original_flg = 0');
$stmt_DELETE_list2->execute([h($_SESSION['ID'])]);
$stmt_INSERT_list2 = $dbh->prepare('INSERT INTO list( food_id, quantity, unit_id, recipe_id, category_id, user_id ) SELECT food_id, quantity, materials.unit_id, recipe_id, foods.category_id, ? FROM materials JOIN foods ON  materials.food_id = foods.id WHERE materials.recipe_id = ?');
$stmt_INSERT_list2->execute([h($_SESSION['ID']), (int)$row['recipe_id']]);
//$stmt_INSERT_list = $dbh->prepare('INSERT INTO list(recipe_id, name, quantity, category_id, user_id, edit_flg) SELECT recipe_id, name, quantity, 0, ?, 0 FROM materials WHERE materials.recipe_id = ?');
//$stmt_INSERT_list->execute([h($_SESSION['ID']), (int)$row['recipe_id']]);
}
}
$_SESSION['FLG'] = 1;
header("Location: " . $_SERVER['PHP_SELF']);
}
//非�    �開にされたレシピを取得
$stmt_access_flg = $dbh->prepare('SELECT list.recipe_id, recipes.title FROM list JOIN users ON  list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id LEFT OUTER JOIN access ON  list.recipe_id = access.recipe_id AND list.user_id = access.user_id WHERE users.id = :user_id AND access.recipe_id IS NULL AND recipes.user_id <> :user_id AND recipes.delete_flg = 0 GROUP BY list.recipe_id, recipes.title, list.edit_flg ORDER BY list.recipe_id');
$stmt_access_flg->bindParam(':user_id', $id, PDO::PARAM_INT);
$stmt_access_flg->execute();
$access_alert = '';
if ($stmt_access_flg->rowCount() !== 0) {
$access_alert .= '下記のレシピが非公開となっています。';
foreach ($stmt_access_flg as $row) {
$access_alert .= '<br>・'.$row['title'];
}
}
//材料ドロップダウンリスト作成
foreach ($dbh->query('SELECT food_category.id AS category_id, food_category.name AS category_name, foods.id AS foods_id, foods.category_id AS foods_category_id, foods.name AS foods_name, unit_id FROM food_category JOIN foods ON food_category.id = foods.category_id ORDER BY food_category.id') as $row3) {
$rows3[] = $row3;
}
//材料ドロップダウンリストに合わせて、数量の単位を変更
if (isset($data_unit_id)) {
$stmt_unit = $dbh->prepare('SELECT * FROM unit WHERE id = ?');
$stmt_unit->execute([(int) $data_unit_id]);
$result_unit = $stmt_unit->fetch();
echo $result_unit['id'].','.$result_unit['name'];
}
/* UPDATEしない方がいいかも？
if(isset($_POST['action']) && $_POST['action'] == 'cancel') {
$stmt_UPDATE_list = $dbh->prepare('UPDATE list SET edit_flg = 0');
$stmt_UPDATE_list->execute();
header("Location: " . $_SERVER['PHP_SELF']);
}
*/
//削除されたレシピを取得
$delete_alert = '';
$delete_count = 0;
$stmt_delete = $dbh->prepare('SELECT list.recipe_id, recipes.title FROM list JOIN recipes ON  list.recipe_id = recipes.id WHERE list.user_id = :user_id AND recipes.delete_flg = 1 GROUP BY list.recipe_id, recipes.title, list.edit_flg ORDER BY list.recipe_id');
$stmt_delete->bindParam(':user_id', $id, PDO::PARAM_INT);
$stmt_delete->execute();
$delete_alert .= '下記のレシピが削除されました。';
foreach ($stmt_delete as $row4) {
$rows4[] = $row4;
$delete_alert .= '<br>・'.$row4['title'];
$delete_count++;
}
//OKボタン押下時
if (isset($_POST['action']) && $_POST['action'] == 'delete2') {
foreach ($rows4 as $row4) {
$stmt_DELETE_list_2 = $dbh->prepare('DELETE FROM list WHERE user_id = ? AND recipe_id = ?');
$stmt_DELETE_list_2->execute([h($_SESSION['ID']), (int)$row4['recipe_id']]);   
}
$_SESSION['FLG'] = 1;
header("Location: " . $_SERVER['PHP_SELF']);
}
//更新ボタン押下
if (isset($data_list) || isset($data_list_count)) {
//全件削除
$stmt_DELETE_list2 = $dbh->prepare('DELETE FROM list2 WHERE user_id = ?');
$stmt_DELETE_list2->execute([h($_SESSION['ID'])]);
//全て行削除→更新　の場合はINSERTしない
if ($data_list_count !== '0') {
$count2 = 0;
for ($i=0; $i<count($data_list); $i++) {
//food_id　取得
$col1 = explode(',', $data_list[$i][0]);
$food_id = $col1[0];
//quantity　unit_id　取得
$col2 = explode(',', $data_list[$i][1]);
//quantity
$quantity = $col2[0];
//unit_id
$unit_id = $col2[1];
//${"stmt_INSERT_list{$i}"} = $dbh->prepare('INSERT INTO list (food_id, quantity, unit_id, recipe_id, category_id, user_id, edit_flg) VALUES (?, ?, ?, ?, (SELECT category_id FROM foods WHERE id = ?), ?, 0)');
//${"stmt_INSERT_list{$i}"}->execute([(int)$food_id, (int)$quantity, (int)$unit_id, $data_list[$i][2], (int)$food_id, h($_SESSION['ID'])]);
${"stmt_INSERT_list2{$i}"} = $dbh->prepare('INSERT INTO list2 ( foods_id, unit_id, quantity, user_id, original_flg ) VALUES (?, ?, ?, ?, ?)');
${"stmt_INSERT_list2{$i}"}->execute([(int)$food_id, (int)$unit_id, (int)$quantity, h($_SESSION['ID']), $data_list[$i][2]]);
if ($food_id !== '') {
$count2++;
}
}
}
//全て行削除→更新　の場合
if ($count2 === 0) {
$stmt_DELETE_list = $dbh->prepare('DELETE FROM list WHERE user_id = ?');
$stmt_DELETE_list->execute([h($_SESSION['ID'])]);
}
unset($_SESSION['INDEX']);
unset($_SESSION['FAVORITES']);
}
//全件削除
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
$stmt_DELETE_list = $dbh->prepare('DELETE FROM list WHERE user_id = ?');
$stmt_DELETE_list->execute([h($_SESSION['ID'])]);
$stmt_DELETE_list2 = $dbh->prepare('DELETE FROM list2 WHERE user_id = ?');
$stmt_DELETE_list2->execute([h($_SESSION['ID'])]);
unset($_SESSION['INDEX']);
unset($_SESSION['FAVORITES']);
unset($_SESSION['FLG']);
header("Location: " . $_SERVER['PHP_SELF']);
$alert = '全件削除しました。';
}
//サムネイルボタン押下　削除処理
if (isset($_POST['action']) && $_POST['action'] == 'delete_single') {
$stmt_DELETE_list = $dbh->prepare('DELETE FROM list WHERE user_id = ? AND recipe_id = ?');
$stmt_DELETE_list->execute([h($_SESSION['ID']), h($_POST['recipe_id'])]);
$_SESSION['FLG'] = 1;
header("Location: " . $_SERVER['PHP_SELF']);
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
    <!-- アラートメッセージ（削除されたレシピが存在した場合）-->
    <?php if($delete_count > 0) :?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle red-text">
      </i>
      <?= $delete_alert ?>
      <form method="post">
        <button class="btn btn-primary waves-effect waves-light my-4" id="delete2" type="submit" name="action" value="delete2">OK
        </button>
      </form>
    </div>
    <?php endif; ?>
    <!-- アラートメッセージ（公開から非公開に変更されたレシピが存在した場合）-->
    <?php if(trim($access_alert) !== '') :?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle red-text">
      </i>
      <?= $access_alert ?>
    </div>
    <?php endif; ?>
    <?php if(trim($alert) !== '') :?>
    <div class="alert alert-success" role="alert">
      <?= $alert ?>
    </div>
    <?php endif; ?>
    <!-- アラートメッセージ（更新されたレシピが存在した場合）-->
    <?php if($edit_count > 0) :?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle red-text">
      </i>
      <?= $edit_alert ?>
      <form method="post">
        <button class="btn btn-primary waves-effect waves-light my-4" id="update" type="submit" name="action" value="update">OK
        </button>
        <button type="button" class="btn btn-light" data-dismiss="alert" aria-label="Close">キャンセル
        </button>
      </form>
    </div>
    <?php endif; ?>
    <form method="post">
      <button class="btn btn-danger waves-effect waves-light my-4 registration" id="delete" type="submit" name="action" value="delete">
        全件削除
      </button>
      <button class="btn btn-primary waves-effect waves-light my-4 registration" id="insert" type="submit" name="action" value="insert">
        更新
      </button>
      <br>
      <div class="card">
        <h3 class="card-header text-center font-weight-bold text-uppercase py-4">買い物リスト
        </h3>
        <div class="card-body">
          <div id="table" class="table-editable">
            <!--買い物リストへ登録されたレシピのサムネイル表示-->
            <?php if(isset($rows5)): ?>
            <?php $old_recipe_id = '' ?>
            <div class="form-inline">
              <?php foreach ($rows5 as $row5): ?>
              <?php if ($old_recipe_id !== $row5['recipe_id']): ?>
              <form method="post">
                <button class="btn waves-effect waves-light my-4 registration" id="delete_single" type="submit" name="action" value="delete_single">
                  <?php if(trim($row5['image_url']) !== ''): ?>
                  <img src="image/recipes/<?= $row5['image_url']?>" height="100">
                  <?php elseif(trim($row5['image_url']) === ''): ?>
                  <img src="image/recipes/noimage.png" height="100">
                  <?php else: ?>
                  <?php endif; ?>
                </button>
                <?= $row5['title'] ?>
                <input type="hidden" name="recipe_id" value="<?= $row5['recipe_id'] ?>">
              </form>
              <?php endif; ?>
              <?php $old_recipe_id = $row5['recipe_id'] ?>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <br>
            <br>
            <span class="table-add float-right mb-3 mr-2">
              <a href="#!" class="text-success">
                <i
                   class="fas fa-plus fa-2x" aria-hidden="true">行を追加する
                </i>
              </a>
            </span>
            <table class="table table-bordered table-responsive-md table-striped text-center">
              <thead>
                <tr>
                  <th class="text-center">材料・調味料
                  </th>
                  <th class="text-center">分量
                  </th>
                  <th class="text-center">
                  </th>
                </tr>
              </thead>
              <tbody id="boxes">
                <!--編集モード-->
                <?php if(isset($rows)): ?>
                <?php $old_category_id2 = '' ?>
                <?php foreach($rows as $row): ?>
                <?php if ($old_category_id2 !== $row['category_id']): ?>
                <tr class="orange white-text tr_category">
                  <td colspan="3" class="text-left">
                    <?= $row['category_name']?>
                  </td>
                </tr>
                <?php endif; ?>
                <?php $old_category_id2 = $row['category_id'] ?>
                <tr>
                  <td class="pt-3-half">
                    <div class="dropdown hierarchy-select ddl_matelials" id="ddl_matelials<?= $count?>">
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
                          <?php foreach ($rows3 as $row3): ?>
                          <?php if ($old_category_id !== $row3['category_id']): ?>
                          <a class="dropdown-item no_link" data-value="<?= $row3['category_id']?>" data-level="1">
                            <?= $row3['category_name']?>
                          </a>
                          <a class="dropdown-item" data-value="<?= $row3['foods_id']?>,<?= $row3['unit_id']?>" data-level="2" href="#">
                            <?= $row3['foods_name']?>
                          </a>
                          <?php else: ?>
                          <a class="dropdown-item" data-value="<?= $row3['foods_id']?>,<?= $row3['unit_id']?>" data-level="2" href="#">
                            <?= $row3['foods_name']?>
                          </a>
                          <?php endif; ?>
                          <?php $old_category_id = $row3['category_id'] ?>
                          <?php endforeach; ?>
                        </div>
                      </div>
                      <input class="d-none input_name" id="input_name<?= $count?>" name="ddl_matelials<?= $count?>" readonly="readonly" aria-hidden="true" type="hidden" value="<?= $row['foods_id']?>,<?= $row['unit_id']?>">
                      <input type="hidden">
                    </div>
                  </td>
                  <td class="pt-3-half text-left">
                    <div class="spoonGroup" id="spoonGroup<?= $count?>">
                      <div class="custom-control custom-radio custom-control-inline">
                        <?php if(trim($row['unit_id']) === '6'): ?>
                        <input type="radio" class="custom-control-input rbTablespoon" id="rbTablespoon<?= $count?>" name="spoonGroup<?= $count?>" value="6" checked>
                        <?php elseif(trim($row['unit_id']) === '7'): ?>
                        <input type="radio" class="custom-control-input rbTablespoon" id="rbTablespoon<?= $count?>" name="spoonGroup<?= $count?>" value="6">
                        <?php else: ?>
                        <input type="radio" class="custom-control-input rbTablespoon" id="rbTablespoon<?= $count?>" name="spoonGroup<?= $count?>" value="6" checked>
                        <?php endif; ?>
                        <label class="custom-control-label lblTablespoon" for="rbTablespoon<?= $count?>">大さじ
                        </label>
                      </div>
                      <div class="custom-control custom-radio custom-control-inline">
                        <?php if(trim($row['unit_id']) === '6'): ?>
                        <input type="radio" class="custom-control-input rbTeaspoon" id="rbTeaspoon<?= $count?>" name="spoonGroup<?= $count?>" value="7">
                        <?php elseif(trim($row['unit_id']) === '7'): ?>
                        <input type="radio" class="custom-control-input rbTeaspoon" id="rbTeaspoon<?= $count?>" name="spoonGroup<?= $count?>" value="7" checked>
                        <?php else: ?>
                        <input type="radio" class="custom-control-input rbTeaspoon" id="rbTeaspoon<?= $count?>" name="spoonGroup<?= $count?>" value="7">
                        <?php endif; ?>
                        <label class="custom-control-label lblTeaspoon" for="rbTeaspoon<?= $count?>">小さじ
                        </label>
                      </div>
                    </div>
                    <input id="unit_id<?= $count?>" type="hidden" value="<?= $row['unit_id']?>" class="unit_id">
                    <div class="form-inline">
                      <?php if (trim($row['unit_id']) === '6' || trim($row['unit_id']) === '7'): ?>
                      <input type="number" id="txtQuantity<?= $count?>" name="txtQuantity<?= $count?>" class="form-control mb-4 txtQuantity" style="display: none;">
                      <?php else: ?>
                      <input type="number" id="txtQuantity<?= $count?>" name="txtQuantity<?= $count?>" class="form-control mb-4 txtQuantity" value="<?= $row['quantity']?>">
                      <?php endif; ?>
                      <p id="lblQuantity<?= $count?>" class="lblQuantity">
                        <?php if (isset($row['unit_id']) && isset($row['unit_name'])): ?>
                        <?php if (trim($row['unit_id']) === '6' || trim($row['unit_id']) === '7'): ?>
                        <!--杯-->
                        <?php else: ?>
                        <?= $row['unit_name']?>
                        <?php endif; ?>
                        <?php endif; ?>
                      </p>
                    </div>
                  </td>
                  <td>
                    <!--<input type="text" id="recipe_id" name="recipe_id" value="<?= $row['recipe_id']?>">-->
                    <input id="original_flg<?= $count?>" type="hidden" value="<?= $row['original_flg']?>" class="original_flg">
                    <span class="table-remove">
                      <button type="button" class="btn btn-danger btn-rounded btn-sm my-0">削除
                      </button>
                    </span>
                  </td>
                </tr>
                <?php $count++ ?>
                <?php endforeach; ?>
                <!--新規モード-->
                <?php else: ?>
                <tr class="hide">
                  <td class="pt-3-half">
                    <div class="dropdown hierarchy-select ddl_matelials" id="ddl_matelials0">
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
                          <?php foreach ($rows3 as $row3): ?>
                          <?php if ($old_category_id !== $row3['category_id']): ?>
                          <a class="dropdown-item no_link" data-value="<?= $row3['category_id']?>" data-level="1">
                            <?= $row3['category_name']?>
                          </a>
                          <a class="dropdown-item" data-value="<?= $row3['foods_id']?>,<?= $row3['unit_id']?>" data-level="2" href="#">
                            <?= $row3['foods_name']?>
                          </a>
                          <?php else: ?>
                          <a class="dropdown-item" data-value="<?= $row3['foods_id']?>,<?= $row3['unit_id']?>" data-level="2" href="#">
                            <?= $row3['foods_name']?>
                          </a>
                          <?php endif; ?>
                          <?php $old_category_id = $row3['category_id'] ?>
                          <?php endforeach; ?>
                        </div>
                      </div>
                      <input class="d-none input_name" id="input_name0" name="ddl_matelials0" readonly="readonly" aria-hidden="true" type="hidden">
                      <input type="hidden">
                    </div>
                  </td>
                  <td class="pt-3-half text-left">
                    <div class="spoonGroup" id="spoonGroup0">
                      <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input rbTablespoon" id="rbTablespoon0" name="spoonGroup0" value="6" checked>
                        <label class="custom-control-label lblTablespoon" for="rbTablespoon0">大さじ
                        </label>
                      </div>
                      <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input rbTeaspoon" id="rbTeaspoon0" name="spoonGroup0" value="7">
                        <label class="custom-control-label lblTeaspoon" for="rbTeaspoon0">小さじ
                        </label>
                      </div>
                    </div>
                    <?php if (isset($result_unit['id'])): ?>
                    <input id="unit_id0" type="hidden" value="<?= $result_unit['id']?>" class="unit_id">
                    <?php else: ?>
                    <input id="unit_id0" type="hidden" class="unit_id">
                    <?php endif; ?>
                    <div class="form-inline">
                      <input type="number" id="txtQuantity0" name="txtQuantity0" class="form-control mb-4 txtQuantity" disabled="true">
                      <p id="lblQuantity0" class="lblQuantity">
                        <?php if (isset($result_unit['id']) && isset($result_unit['name'])): ?>
                        <?php if (trim($result_unit['id']) === '6' || trim($result_unit['id']) === '7'): ?>
                        <!--杯-->
                        <?php else: ?>
                        <?= $result_unit['name']?>
                        <?php endif; ?>
                        <?php endif; ?>
                      </p>
                    </div>
                  </td>
                  <td> 
                    <span class="table-remove">
                      <input id="original_flg0" type="hidden" value="1" class="original_flg">
                      <button type="button"
                              class="btn btn-danger btn-rounded btn-sm my-0">削除
                      </button>
                    </span>
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!--
<ul class="list-group">
<label for="all"><input type="checkbox" name="allChecked" id="all">全選択</label>
<div id="boxes">
<?php foreach($stmt as $row): ?>
<li class="list-group-item">
<label><input type="checkbox" name="chk[]" value="<?= $row['id']?>"><?= $row['name']?> <?= $row['quantity']?></label>
</li>
<?php endforeach; ?>
</div>
</ul>
-->
      <!--消さない！！！ 保留　sort機能付きリスト
<ul id="sortable" class="list-group">
<?php foreach($rows as $row): ?>
<li class="list-group-item">
<div class="custom-control custom-checkbox">
<input type="checkbox" class="custom-control-input" id="<?= $row['id']?>">
<label class="custom-control-label" for="<?= $row['id']?>"><?= $row['name']?>  <?= $row['quantity']?></label>
</div>
</li>
<?php endforeach; ?>
</ul>
-->
    </form>
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
  <script type="text/javascript" src="list.js">
  </script>
  <script src="http://code.jquery.com/jquery-1.8.3.js">
  </script>
  <script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js">
  </script>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js">
  </script>
  <script>
    //入力された名称、数量を取得
    $(document).ready(function() {
      var tr_category = $(".tr_category").length;
      var tr = $("#table tr");
      for (var i = 0; i < tr.length-1; i++) {
        if($('#unit_id' + i).val() !== '6' && $('#unit_id' + i).val() !== '7'){
          $('#spoonGroup' + i).hide();
        }
        else{
          //$('#spoonGroup' + i).show();
          $('#spoonGroup' + i).hide();
        }
      }
      var $selected_ddl_matelials;
      $('.ddl_matelials').on('click', function(){
        $selected_ddl_matelials =  $(this).attr("id");
        $selected_ddl_matelials = $selected_ddl_matelials.replace('ddl_matelials', '');
      }
                            );
      var $selected_spoonGroup;
      $('.spoonGroup').on('click', function(){
        $selected_spoonGroup =  $(this).attr("id");
        $selected_spoonGroup = $selected_spoonGroup.replace('spoonGroup', '');
      }
                         );
      for (var i = 0; i < tr.length-1; i++) {
        //大さじ・小さじ　ラジオボタンが変更されたときに処理
        $('input[name="spoonGroup' + i + '"]:radio').change(function () {
          $('#unit_id' + $selected_spoonGroup).val($(this).val());
        }
                                                           );
      }
      for (var i = 0; i < tr.length-1; i++) {
        $('#ddl_matelials' + i).hierarchySelect({
          hierarchy: false,
          search: true,
          width: 200,
          initialValueSet: true,
          onChange: function (value) {
            var res = (String(value)).split(",");
            var data = {
              data_unit_id: res[1]
            };
            $.ajax({
              type: "post",
              url: "list.php",
              data: data,
              //Ajax通信が成功した場合
              success: function(data, dataType) {
                //PHPから返ってきたデータの表示
                //分量単位の変更
                var res = (String(data)).split("<!");
                res = (String(res[0])).split(",");
                //大さじ・小さじ　ラジオボタン　表示・非表示
                $('#unit_id' + $selected_ddl_matelials).val(res[0]);
                if(jQuery.trim(res[1]) !== '大さじ' && jQuery.trim(res[1]) !== '小さじ'){
                  $('#spoonGroup' + $selected_ddl_matelials).hide();
                  $('#lblQuantity' + $selected_ddl_matelials).text(res[1]);
                }
                else{
                  //$('#spoonGroup' + $selected_ddl_matelials).show();
                  $('#spoonGroup' + $selected_ddl_matelials).hide();
                  //$('#lblQuantity' + $selected_ddl_matelials).text('杯');
                  if(jQuery.trim(res[1]) === '大さじ' || jQuery.trim(res[1]) === '小さじ'){
                    $('#rbTablespoon' + $selected_ddl_matelials).prop('checked', true);
                  }
                }
                $('#txtQuantity' + $selected_ddl_matelials).val('');
                if(value === 0){
                  $('#txtQuantity' + $selected_ddl_matelials).prop('disabled', true);
                }
                else{
                  if($('#unit_id' + $selected_ddl_matelials).val() === '6'){
                    $('#txtQuantity' + $selected_ddl_matelials).hide();
                    $('#lblQuantity' + $selected_ddl_matelials).text('');
                  }
                  else{
                    $('#txtQuantity' + $selected_ddl_matelials).show();
                    $('#txtQuantity' + $selected_ddl_matelials).prop('disabled', false);
                  }
                }
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
                alert('送信が失敗しました。');
              }
            }
                  );
            //btnSteps 活性・非活性
            /*
            if(value===0) {
              $('#btnSteps').prop('disabled', true);
            }
            else {
              $('#btnSteps').prop('disabled', false);
            }
            */
          }
        }
                                               );
      }
      $('#insert').click(function() {
        //更新処理の場合　recipe_idを取得
        var recipe_id = $('#recipe_id').val();
        if (recipe_id === "" || recipe_id === null || recipe_id === undefined) {
          recipe_id = 0;
        }
        //modal_material
        var list = [];
        var tr = $("#table tr");
        for (var i = 0; i < tr.length-1; i++) {
          var cells = tr.eq(i+1).children();
          for (var j = 0; j < 3; j++) {
            if (typeof list[i] == "undefined")
              list[i] = [];
            if(j===0){
              list[i][j] = cells.eq(j).find('.d-none').val();
            }
            else if(j===1){
              list[i][j] = cells.eq(j).find('.txtQuantity').val() + ',' + cells.eq(j).find('.unit_id').val();
            }
            else{
              list[i][j] = cells.eq(j).find('.original_flg').val();
            }
          }
        }
        var list_count = list.length;
        //postメソッドで送るデータを定義 var data = {パラメータ名 : 値};
        var data = {
          data_recipe_id: recipe_id,
          data_list: list,
          data_list_count: list_count,
        };
        $.ajax({
          type: "post",
          url: "list.php",
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
            alert('送信が失敗しました。');
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
