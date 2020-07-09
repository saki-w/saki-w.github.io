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
$add_sql_where = '';
$add_sql_sort = '';
//$sort_font = 'sort_new';
$sort_flg = 0;
//------------------------------------------------------------◎検索ボタン押下時------------------------------------------------------------
if(isset($_POST['action']) && $_POST['action'] == 'filtering' && h($_POST['txtSearch']) !== '') {
$stmt_filtering = $dbh->prepare("SELECT recipes.id AS recipe_id FROM recipes JOIN materials ON  recipes.id = materials.recipe_id JOIN foods ON  materials.food_id = foods.id JOIN users ON  recipes.user_id = users.id WHERE recipes.title LIKE :name OR  foods.name LIKE :name OR  users.name LIKE :name GROUP BY recipes.id");
$name = "%". h($_POST['txtSearch']) . "%";
$stmt_filtering->bindParam(':name',$name, PDO::PARAM_STR);
$stmt_filtering->execute();
if($stmt_filtering->rowCount() == 0){
$add_sql_where = 'x';
}
else if($stmt_filtering->rowCount() == 1){
$results = $stmt_filtering->fetch();
$add_sql_where = 'AND recipes.id = '.$results['recipe_id'];
}
else{
$results = $stmt_filtering->fetchAll();
$add_sql_where = 'AND recipes.id in (';
foreach($results as $result){
$add_sql_where .= $result['recipe_id'].', ';
}
$add_sql_where = rtrim($add_sql_where, ', ').')';
}
//session_idを新しく生成し、置き換える
//session_regenerate_id(true); 
$_SESSION['SEARCH'] = $add_sql_where;
$_SESSION['SEARCH_WORD'] = $_POST['txtSearch'];
}
else if(isset($_GET['data_sort']) && $_GET['data_sort'] === "new") {
//ソート　新着順
$add_sql_sort = '';
//$sort_font = 'sort_new';
$sort_flg = 1;
if(isset($_SESSION['SEARCH'])){
$add_sql_where = h($_SESSION['SEARCH']);
}
}
else if(isset($_GET['data_sort']) && $_GET['data_sort'] === "popularity") {
//ソート　人気順
$add_sql_sort = 'IfNull(C.favorites_count, 0) DESC,';
//$sort_font = 'sort_popularity';
$sort_flg = 1;
if(isset($_SESSION['SEARCH'])){
$add_sql_where = h($_SESSION['SEARCH']);
}
}
else{
//$_SESSION['SEARCH']消す
unset($_SESSION['SEARCH']);
unset($_SESSION['SEARCH_WORD']);
}
//------------------------------------------------------------◎メイン検索------------------------------------------------------------
$sql = 'SELECT recipes.id, recipes.title, recipes.image_url, recipes.user_id, recipes.comment, users.name, users.image_path, IfNull(A.user_id, 0) AS favorites_flg, IfNull(B.user_id, 0) AS list_flg, IfNull(C.favorites_count, 0) AS favorites_count, IfNull(D.user_id, 0) AS access_flg, ( SELECT CASE WHEN TIMESTAMPDIFF(DAY, recipes.updated_at, CURRENT_TIMESTAMP()) < 1 THEN( CASE WHEN TIMESTAMPDIFF(HOUR, recipes.updated_at, CURRENT_TIMESTAMP()) < 1 THEN CONCAT(TIMESTAMPDIFF(MINUTE,(recipes.updated_at), CURRENT_TIMESTAMP()), "分前") ELSE CONCAT(TIMESTAMPDIFF(HOUR, recipes.updated_at, CURRENT_TIMESTAMP()), "時間前") END ) ELSE CONCAT(TIMESTAMPDIFF(DAY, recipes.updated_at, CURRENT_TIMESTAMP()), "日前") END ) AS update_time, recipes.number_of_persons FROM recipes JOIN users ON  recipes.user_id = users.id LEFT OUTER JOIN ( SELECT recipes.id, favorites.user_id FROM recipes LEFT OUTER JOIN favorites ON  recipes.id = favorites.recipe_id WHERE favorites.user_id = :user_id ) A ON  recipes.id = A.id LEFT OUTER JOIN ( SELECT recipes.id, list.user_id FROM recipes LEFT OUTER JOIN list ON  recipes.id = list.recipe_id WHERE list.user_id = :user_id GROUP BY list.recipe_id, list.user_id ) B ON  recipes.id = B.id LEFT OUTER JOIN ( SELECT recipe_id, COUNT(recipe_id) AS favorites_count FROM favorites GROUP BY recipe_id ) C ON  recipes.id = C.recipe_id LEFT OUTER JOIN ( SELECT recipe_id, user_id FROM access WHERE access.user_id = :user_id ) D ON  recipes.id = D.recipe_id WHERE recipes.delete_flg = 0 AND ( IfNull(D.user_id, 0) <> 0 OR  recipes.user_id = :user_id ) '.$add_sql_where.' ORDER BY '.$add_sql_sort.' recipes.id DESC';
//------------------------------------------------------------◎結果取得------------------------------------------------------------
$stmt_main = $dbh->prepare($sql);
$stmt_main->bindParam(':user_id',$id, PDO::PARAM_INT);
$stmt_main->execute();
$stmt_main_count=$stmt_main->rowCount();
if($stmt_main_count > 0){
foreach($stmt_main as $row){
$rows[] = $row;
}
//------------------------------------------------------------材料取得------------------------------------------------------------
foreach($dbh->query('SELECT recipes.id, foods.name AS materials_name, materials.quantity, unit.id AS unit_id, unit.name AS unit_name FROM materials JOIN recipes ON  materials.recipe_id = recipes.id JOIN foods ON  materials.food_id = foods.id LEFT JOIN unit ON materials.unit_id = unit.id') as $row1) {
$rows1[] = $row1;
}
//------------------------------------------------------------作り方取得------------------------------------------------------------
foreach($dbh->query('SELECT recipes.id, steps.no, steps.content FROM recipes JOIN steps ON recipes.id = steps.recipe_id') as $row2) {
$rows2[] = $row2;
}
}
$alert = '';
$alert_flg = 0;
//------------------------------------------------------------お気に入りボタン押下時------------------------------------------------------------
if(isset($_POST['action']) && $_POST['action'] == 'insert_favorites') {
$stmt_INSERT_favorites = $dbh->prepare('INSERT INTO favorites(user_id, recipe_id) VALUES(?, ?)');
$stmt_INSERT_favorites->execute([h($_SESSION['ID']), h($_POST["recipe_id"])]);
header("Location: " . $_SERVER['PHP_SELF']);
$alert = 'お気に入り登録しました';
$alert_flg = 1;
header( "Location: favorites.php" ) ;
}
//------------------------------------------------------------お気に済みボタン押下時------------------------------------------------------------
if(isset($_POST['action']) && $_POST['action'] == 'delete_favorites') {
$stmt_DELETE_favorites = $dbh->prepare('DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?');
$stmt_DELETE_favorites->execute([h($_SESSION['ID']), h($_POST["recipe_id"])]);
header("Location: " . $_SERVER['PHP_SELF']);
$alert = 'お気に入り削除しました';
$alert_flg = 0;
}
//------------------------------------------------------------レシピ削除ボタン押下時------------------------------------------------------------
if(isset($_POST['action']) && $_POST['action'] == 'delete_recipes') {
//recipes
//$stmt_DELETE_recipes = $dbh->prepare('DELETE FROM recipes WHERE id = ? AND user_id = ?');
//$stmt_DELETE_recipes->execute([h($_POST["recipe_id"]), h($_SESSION['ID'])]);
$stmt_DELETE_recipes = $dbh->prepare('UPDATE recipes SET delete_flg = 1 WHERE id = ?');
$stmt_DELETE_recipes->execute([h($_POST["recipe_id"])]);
//materials
$stmt_DELETE_materials = $dbh->prepare('DELETE FROM materials WHERE recipe_id = ?');
$stmt_DELETE_materials->execute([h($_POST["recipe_id"])]);
//steps
$stmt_DELETE_steps = $dbh->prepare('DELETE FROM steps WHERE recipe_id = ?');
$stmt_DELETE_steps->execute([h($_POST["recipe_id"])]);
//favorites
//$stmt_DELETE_favorites = $dbh->prepare('DELETE FROM favorites WHERE recipe_id = ?');
//$stmt_DELETE_favorites->execute([h($_POST["recipe_id"])]);
//list
//$stmt_DELETE_list = $dbh->prepare('DELETE FROM list WHERE recipe_id = ?');
//$stmt_DELETE_list->execute([h($_POST["recipe_id"])]);
//access
$stmt_DELETE_access = $dbh->prepare('DELETE FROM access WHERE recipe_id = ?');
$stmt_DELETE_access->execute([h($_POST["recipe_id"])]);
header("Location: " . $_SERVER['PHP_SELF']);
$alert = 'レシピを削除しました';
$alert_flg = 0;
}
//------------------------------------------------------------買い物リスト作成ボタン押下時------------------------------------------------------------
if(isset($_POST['action']) && $_POST['action'] == 'list') {
$stmt_list = $dbh->prepare('INSERT INTO list( food_id, quantity, unit_id, recipe_id, category_id, user_id ) SELECT food_id, quantity, materials.unit_id, recipe_id, foods.category_id, ? FROM materials JOIN foods ON  materials.food_id = foods.id WHERE materials.recipe_id = ?');
$stmt_list->execute([h($_SESSION['ID']), h($_POST["recipe_id"])]);
$alert = '買い物リストを作成しました。';
$_SESSION['INDEX'] = $_POST['index'];
header( "Location: list.php") ;
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
    <?php if($sort_flg === 0): ?>
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
              <li class="nav-item active">
                <a class="nav-link" href="./index.php">トップ 
                  <span class="sr-only">(current)
                  </span>
                </a>
              </li>
            </ul>
            <form class="form-inline" method="post">
              <?php if(isset($_SESSION['SEARCH']) && isset($_SESSION['SEARCH_WORD'])): ?>
              <input class="form-control" type="text" id="txtSearch" name="txtSearch" placeholder="レシピを探す" aria-label="Search" value="<?= $_SESSION['SEARCH_WORD'] ?>">
              <?php else: ?>
              <input class="form-control" type="text" id="txtSearch" name="txtSearch" placeholder="レシピを探す" aria-label="Search">
              <?php endif; ?>
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
  <?php else: ?>
  <?php endif; ?>
  <?php if($sort_flg === 0): ?>
  <div class="container main">
    <?php else: ?>
    <div class="container" style="padding: 0">
      <?php endif; ?>
      <?php if($sort_flg === 0): ?>
      <h3 class="h3-responsive">レシピ一覧
      </h3>
      <?php else: ?>
      <?php endif; ?>
      <!-- アラートメッセージ -->
      <?php if(trim($alert) !== '') :?>
      <div class="alert alert-success" role="alert">
        <?= $alert ?>
      </div>
      <?php endif; ?>
      <?php if(isset($rows)): ?>
      <?php if($sort_flg === 0): ?>
      <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-primary form-check-label active">
          <input class="form-check-input sort" type="radio" name="new" id="option1" autocomplete="off" checked>
          新着順
        </label>
        <label class="btn btn-primary form-check-label">
          <input class="form-check-input sort" type="radio" name="popularity" id="option2" autocomplete="off">人気順
        </label>
      </div>
      <?php else: ?>
      <?php endif; ?>
      <!--ソート　リンク版
<div class="text-left">
<form method="post">
<button class="btn btn-link blue-text text-decoration-none" type="submit" name="action" value="sort_new">
<?php if($sort_font === "sort_new"): ?>
<h5 class="h5-responsive font-weight-bold">新着順
</h5>
<?php else: ?>
<h5 class="h5-responsive">新着順
</h5>
<?php endif; ?>
</button>
<button class="btn btn-link blue-text text-decoration-none" type="submit" name="action" value="sort_popularity">
<?php if($sort_font === "sort_popularity"): ?>
<h5 class="h5-responsive font-weight-bold">人気順
</h5>
<?php else: ?>
<h5 class="h5-responsive">人気順
</h5>
<?php endif; ?>
</button>
</form>
</div>
-->
      <div class="main_sort">
        <?php foreach($rows as $row): ?>
        <div class="row no-gutters position-relative">
          <div class="col-md-6 mb-md-0 p-md-4">
            <?php if(trim($row['image_url']) !== ''): ?>
            <img class="w-100" src="image/recipes/<?= $row['image_url']?>" alt="image" height="340">
            <?php else:?>
            <img class="w-100" src="image/recipes/noimage.png" alt="image" height="340">
            <?php endif; ?>
          </div>
          <div class="col-md-6 position-static p-4 pl-md-0">
            <h5 class="mt-0">
              <?= $row['title']?>
              <small style="padding-left: 20px">
                <i class="fas fa-heart mb-1 red-text">
                </i>
                <?= $row['favorites_count']?>
              </small>
            </h5>
            <br>
            <strong class="primary-font">cooked by
            </strong>
            <?php if(trim($row['image_path']) !== ''): ?>
            <img src="image/users/<?= $row['image_path'] ?>" class="rounded-circle z-depth-0" alt="avatar image" height="20">
            <?php elseif(trim($row['image_path']) === ''): ?>
            <img src="image/users/noimage.jpg" class="rounded-circle z-depth-0" alt="avatar image" height="20">
            <?php else: ?>
            <?php endif; ?>
            <a href="#" class="font-weight-bold pink-lighter-hover mb-3">
              <?= $row['name']?>
            </a>
            <p>
              <small class="pull-right">
                <?= $row['comment']?>
              </small>
            </p>
            <p>
              <small class="pull-right text-muted">
                <i class="far fa-clock">
                </i> 
                <?= $row['update_time']?>
              </small>
            </p>
            <button type="button" class="btn btn-outline-info waves-effect" data-toggle="modal" data-target="#detail_recipes<?= $row['id']?>">
              <i class="fas fa-chevron-circle-right">
              </i> 詳細を見る
            </button>
            <?php if($row['user_id'] === h($_SESSION['ID'])): ?>
            <form method="post" action="register.php">
              <button class="btn btn-outline-default waves-effect" type="submit" name="action">
                <i class="fas fa-edit">
                </i> 編集
              </button>
              <input type="hidden" name="recipe_id" value="<?= $row['id'] ?>">
            </form>
            <button type="button" class="btn btn-outline-danger waves-effect" data-toggle="modal" data-target="#delete_recipes<?= $row['id']?>">
              <i class="far fa-trash-alt">
              </i> 削除
            </button>
            <?php endif; ?>
          </div>
          <!-- Modal 詳細 -->
          <div class="modal fade" id="detail_recipes<?= $row['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">
            <div class="modal-dialog modal-notify modal-info modal-lg modal-dialog-scrollable" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <p class="heading lead">
                    <?= $row['title']?>
                  </p>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="white-text">×
                    </span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="container-fluid">
                    <div class="row">
                      <div class="col-md-5 col-example">
                        <?php if(trim($row['image_url']) !== ''): ?>
                        <img src="image/recipes/<?= $row['image_url']?>" class="img-fluid">
                        <?php else:?>
                        <img src="image/recipes/noimage.png" class="img-fluid">                    
                        <?php endif; ?>
                      </div>
                      <div class="col-md-7 col-example">
                        <ul class="list-group list-group-flush">
                          <h4 class="h4-responsive">材料（<?= $row['number_of_persons'] ?>人分）
                          </h4>
                          <?php foreach($rows1 as $row1): ?>
                          <?php if($row['id'] == $row1['id']): ?>
                          <li class="list-group-item">
                            <?= $row1['materials_name']?>
                            <?php if($row1['unit_id'] === '6' || $row1['unit_id'] === '7'): ?>
                            <?= $row1['unit_name']?>
                            <?= $row1['quantity']?>
                            杯
                            <?php else: ?>
                            <?= $row1['quantity']?>
                            <?= $row1['unit_name']?>
                            <?php endif; ?>
                          </li>
                          <?php endif; ?>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    </div>
                    <div class="row">
                      <ul class="list-group list-group-flush">
                        <h4 class="h4-responsive">作り方
                        </h4>
                        <?php foreach($rows2 as $row2): ?>
                        <?php if($row['id'] == $row2['id']): ?>
                        <li class="list-group-item">
                          <?= $row2['no']."．".$row2['content']?>
                        </li>
                        <?php endif; ?>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="modal-footer justify-content-center">
                  <form action="" method="post">
                    <?php if($row['favorites_flg'] == 0): ?>
                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="action" value="insert_favorites">
                      <i class="far fa-heart ml-1 text-white">
                      </i> お気に入りする
                    </button>
                    <?php else:?>
                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="action" value="delete_favorites">
                      <i class="fas fa-heart">
                      </i> お気に入りから外す
                    </button>
                    <?php endif; ?>
                    <input type="hidden" name="recipe_id" value="<?= $row['id'] ?>">
                  </form>
                  <?php if($row['list_flg'] == 0): ?>
                  <form action="" method="post">
                    <button class="btn btn-primary waves-effect waves-light" type="submit" name="action" value="list">
                      <i class="fas fa-clipboard-list">
                      </i> 買い物リスト作成
                      <input type="hidden" name="index" value="1">
                    </button>
                    <input type="hidden" name="recipe_id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="favorites_flg" value="<?= $row['favorites_flg'] ?>">
                    <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                  </form>
                  <?php else:?>
                  <form action="list.php" method="post">
                    <button class="btn btn-primary waves-effect waves-light" type="submit">
                      <i class="fas fa-clipboard-list">
                      </i> 買い物リスト確認
                    </button>
                    <input type="hidden" name="recipe_id" value="<?= $row['id'] ?>">
                  </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <!-- Modal レシピ削除ダイアログ -->
          <div class="modal fade" id="delete_recipes<?= $row['id']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">
                    <i class="fas fa-exclamation-circle red-text">
                    </i>以下の情報が削除されます。よろしいですか？
                  </h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;
                    </span>
                  </button>
                </div>
                <div class="modal-body">
                  ・レシピ情報
                  <br>
                  ・お気に入り情報
                  <br>
                  ・買い物リスト情報
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-light" data-dismiss="modal">キャンセル
                  </button>
                  <form method="post">
                    <button class="btn btn-primary" type="submit" name="action" value="delete_recipes">OK
                    </button>
                    <input type="hidden" name="recipe_id" value="<?= $row['id'] ?>">
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="alert alert-warning" role="alert">データはありません。
      </div>
      <?php endif; ?>
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
        $('.sort').click(function() {
          var sort = $(this).attr("name");
          //postメソッドで送るデータを定義 var data = {パラメータ名 : 値};
          var data = {
            data_sort: sort,
          };
          $.ajax({
            type: "get",
            url: "index.php",
            data: data,
            //Ajax通信が成功した場合
            success: function(data, dataType) {
              //PHPから返ってきたデータの表示
              //alert(data);
              $('.main_sort').html(data);
              //取得したHTMLを.resultに反映
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
      </script>
      <!-- Hierarchy Select core JavaScript 
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>-->
      <script src="SELECT/dist/hierarchy-select.min.js">
      </script>
      </body>
    </html>
