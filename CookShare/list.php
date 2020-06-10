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
if (isset($_POST['data_list'])) {
$data_list = $_POST['data_list'];
for($i=1; $i<count($data_list)+1; $i++){
for($j=0; $j<3; $j++){
//echo $data_list[$i][$j]. '<br>';
}
}
}
if (isset($_POST['data_recipe_id'])) {
$data_recipe_id = $_POST['data_recipe_id'];
//echo 'data_recipe_id:'.$data_recipe_id. '<br>';
}
try {
$dbh = new PDO(DSN, DB_USER, DB_PASS);
//ログインユーザー情報　取得
$stmt_users = $dbh->prepare('SELECT * FROM users WHERE id = ?');
$stmt_users->execute([h($_SESSION['ID'])]);
$result_users = $stmt_users->fetch();
$alert = '';
//メイン検索
$stmt_list = $dbh->prepare('SELECT list.id, list.name, list.quantity, list.recipe_id FROM list JOIN users ON  list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id JOIN access ON  list.recipe_id = access.recipe_id AND list.user_id = access.user_id WHERE users.id = :user_id UNION SELECT list.id, list.name, list.quantity, list.recipe_id FROM list JOIN users ON  list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id WHERE users.id = :user_id AND recipes.delete_flg = 0 ORDER BY recipe_id, id');
$stmt_list->bindParam(':user_id',$id, PDO::PARAM_INT);
$stmt_list->execute();
foreach($stmt_list as $row){
$rows[] = $row;
}
//更新されたレシピを取得
$stmt_edit_flg = $dbh->prepare('SELECT list.recipe_id, recipes.title, list.edit_flg FROM list JOIN users ON list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id  JOIN access ON  list.recipe_id = access.recipe_id AND list.user_id = access.user_id WHERE users.id = :user_id AND recipes.user_id <> :user_id GROUP BY list.recipe_id, recipes.title, list.edit_flg ORDER BY list.recipe_id');
$stmt_edit_flg->bindParam(':user_id',$id, PDO::PARAM_INT);
$stmt_edit_flg->execute();
$edit_alert = '';
$edit_alert .= '下記のレシピが変更されました。買い物リストも変更しますか？<br>';
$edit_count = 0;
foreach($stmt_edit_flg as $row){
if($row['edit_flg'] === '1'){
$edit_alert .= '<br>・'.$row['title'];
$edit_count++;
}
}
//非公開にされたレシピを取得
$stmt_access_flg = $dbh->prepare('SELECT list.recipe_id, recipes.title FROM list JOIN users ON  list.user_id = users.id JOIN recipes ON  list.recipe_id = recipes.id LEFT OUTER JOIN access ON  list.recipe_id = access.recipe_id AND list.user_id = access.user_id WHERE users.id = :user_id AND access.recipe_id IS NULL AND recipes.user_id <> :user_id AND recipes.delete_flg = 0 GROUP BY list.recipe_id, recipes.title, list.edit_flg ORDER BY list.recipe_id');
$stmt_access_flg->bindParam(':user_id',$id, PDO::PARAM_INT);
$stmt_access_flg->execute();
$access_alert = '';
if($stmt_access_flg->rowCount() !== 0){
$access_alert .= '下記のレシピが非公開となっています。';
foreach($stmt_access_flg as $row){
$access_alert .= '<br>・'.$row['title'];
}
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
$stmt_delete = $dbh->prepare('SELECT list.recipe_id, recipes.title FROM list JOIN recipes ON  list.recipe_id = recipes.id WHERE list.user_id = :user_id AND recipes.delete_flg = 1 AND recipes.user_id <> :user_id GROUP BY list.recipe_id, recipes.title, list.edit_flg ORDER BY list.recipe_id');
$stmt_delete->bindParam(':user_id',$id, PDO::PARAM_INT);
$stmt_delete->execute();
$delete_alert .= '下記のレシピが削除されました。';
foreach($stmt_delete as $row4){
$rows4[] = $row4;
$delete_alert .= '<br>・'.$row4['title'];
$delete_count++;
}
//OKボタン押下時
if(isset($_POST['action']) && $_POST['action'] == 'delete2') {
foreach($rows4 as $row4){
$stmt_DELETE_list_2 = $dbh->prepare('DELETE FROM list WHERE user_id = ? AND recipe_id = ?');
$stmt_DELETE_list_2->execute([h($_SESSION['ID']), (int)$row4['recipe_id']]);
}
header("Location: " . $_SERVER['PHP_SELF']);
}
//登録
if(isset($data_list)){
//全件削除
$stmt_DELETE_list = $dbh->prepare('DELETE FROM list WHERE user_id = ?');
$stmt_DELETE_list->execute([h($_SESSION['ID'])]);
//materials INSERT
for($i=1; $i<count($data_list)+1; $i++){
//新規登録
${"stmt_INSERT_list{$i}"} = $dbh->prepare('INSERT INTO list (recipe_id, name, quantity, category_id, user_id, edit_flg) VALUES (?, ?, ?, 0, ?, 0)');
${"stmt_INSERT_list{$i}"}->execute([$data_list[$i][2], $data_list[$i][0], $data_list[$i][1], h($_SESSION['ID'])]);
}
header("Location: " . $_SERVER['PHP_SELF']);
$alert = '登録しました。';
}
//全件削除
if(isset($_POST['action']) && $_POST['action'] == 'delete') {
$stmt_DELETE_list = $dbh->prepare('DELETE FROM list WHERE user_id = ?');
$stmt_DELETE_list->execute([h($_SESSION['ID'])]);
header("Location: " . $_SERVER['PHP_SELF']);
$alert = '全件削除しました。';
}
//更新アラート　OKボタン
if(isset($_POST['action']) && $_POST['action'] == 'update') {
foreach($rows as $row){
$stmt_DELETE_list = $dbh->prepare('DELETE FROM list WHERE recipe_id = ?');
$stmt_DELETE_list->execute([(int)$row['recipe_id']]);
$stmt_INSERT_list = $dbh->prepare('INSERT INTO list(recipe_id, name, quantity, category_id, user_id, edit_flg) SELECT recipe_id, name, quantity, 0, ?, 0 FROM materials WHERE materials.recipe_id = ?');
$stmt_INSERT_list->execute([h($_SESSION['ID']), (int)$row['recipe_id']]);
}
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
      <button class="btn btn-danger waves-effect waves-light my-4 registration" id="delete" type="submit" name="action" value="delete" disabled="true">
        全件削除
      </button>
      <button class="btn btn-primary waves-effect waves-light my-4 registration" id="insert" type="submit" name="action" value="insert" disabled="true">
        更新
      </button>
      <div class="card">
        <h3 class="card-header text-center font-weight-bold text-uppercase py-4">買い物リスト
        </h3>
        <div class="card-body">
          <div id="table" class="table-editable">
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
                  <!--<th class="text-center"><label for="all"><input type="checkbox" name="allChecked" id="all"></label></th>-->
                  <th class="text-center">名称
                  </th>
                  <th class="text-center">数量
                  </th>
                  <th class="text-center">
                  </th>
                </tr>
              </thead>
              <tbody id="boxes">
                <?php if(isset($rows)): ?>
                <?php foreach($rows as $row): ?>
                <tr>
                  <!--
<td class="pt-3-half">
<label><input type="checkbox" name="chk[]"></label>
</td>
-->
                  <td class="pt-3-half" contenteditable="true">
                    <input type="text" class="form-control py-0" id="txtMaterial" name="txtMaterial" value="<?= $row['name']?>">
                  </td>
                  <td class="pt-3-half" contenteditable="true">
                    <input type="text" class="form-control py-0" id="txtQuantity" name="txtQuantity" value="<?= $row['quantity']?>">
                  </td>
                  <td>
                    <input type="hidden" id="recipe_id" name="recipe_id" value="<?= $row['recipe_id']?>">
                    <span class="table-remove">
                      <button type="button" class="btn btn-danger btn-rounded btn-sm my-0">削除
                      </button>
                    </span>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <?php endif; ?>
                <tr class="hide">
                  <!--
<td class="pt-3-half">
<label><input type="checkbox" name="chk[]"></label>
</td>
-->
                  <td class="pt-3-half" contenteditable="true">
                    <input type="text" class="form-control py-0" id="txtMaterial" name="txtMaterial">
                  </td>
                  <td class="pt-3-half" contenteditable="true">
                    <input type="text" class="form-control py-0" id="txtQuantity" name="txtQuantity">
                  </td>
                  <td>
                    <span class="table-remove">
                      <button type="button"
                              class="btn btn-danger btn-rounded btn-sm my-0">削除
                      </button>
                    </span>
                  </td>
                </tr>
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
      $('#insert').click(function() {
        var recipe_id = document.getElementById('recipe_id').value;
        if(recipe_id === "" || recipe_id === null || recipe_id === undefined){
          recipe_id = 0;
        }
        var list = [];
        var tr = $("#table tr");
        for (var i = 0, l = tr.length; i < l; i++) {
          var cells = tr.eq(i).children();
          for (var j = 0, m = cells.length; j < m; j++) {
            if (typeof list[i] == "undefined")
              list[i] = [];
            list[i][j] = cells.eq(j).find('input').val();
          }
        }
        //postメソッドで送るデータを定義 var data = {パラメータ名 : 値};
        var data = {
          data_list: list,
          data_recipe_id: recipe_id
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
