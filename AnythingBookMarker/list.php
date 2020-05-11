<?php

//エラーログ
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);



//DB接続
/*
$db_name = 'anythingbookmarker';
$pdo_dsn = 'mysql:dbhost=localhost;dbname='.$db_name.';charset=utf8;';
$db_user = 'dbuser';
$db_password = 'MvA7FP1mznDs5U3b';
$db_option = [
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_EMULATE_PREPARES => false,
];

try{
	$db = new PDO($pdo_dsn, $db_user, $db_password, $db_option);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "SELECT * FROM bookmark";
	$stmt = $db->query($sql);

}catch(PDOException $e){
	echo $e->getMessage();
	exit;
}
*/

require_once("dbConnect.php");
$mysqli = db_connect();
 
$sql = "SELECT * FROM bookmark";
 
$result = $mysqli -> query($sql);
 
//クエリ失敗
if(!$result) {
	echo $mysqli->error;
	exit();
}
 
//連想配列で取得
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$rows[] = $row;
}
 
//結果セットを解放
$result->free();
 
// データベース切断
$mysqli->close();








//更新処理　削除処理
if(isset($_POST['update']) || isset($_POST['delete'])){
	function sethtmlspecialchars($data){
  if(is_array($data)){
  return array_map("sethtmlspecialchars",$data);
} else {
  return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
}

  $mysqli = db_connect();

  $err = false;

  $_POST = sethtmlspecialchars($_POST);
 
  $title = trim($_POST["title"]);
  if(empty($title)){
  $err = true;
}

  $url = trim($_POST["url"]);
  if(empty($url)){
  $err = true;
}

  $memo = trim($_POST["memo"]);

  $id = trim($_POST["id"]);

  //更新処理　プリペアドステートメント
  if(isset($_POST['update'])){
  	$stmt = $mysqli->prepare("UPDATE bookmark SET title=?, url=?, memo=? WHERE id=?");
  	if ($stmt) {
  		//プレースホルダへ実際の値を設定する
      $stmt->bind_param('sssi', $title, $url, $memo, $id);
      $title = $_POST['title'];
      $url = $_POST['url'];
      $memo = $_POST['memo'];
      $id = $_POST['id'];
  	}
  }

  //削除処理　プリペアドステートメント
  if(isset($_POST['delete'])){
  	$stmt = $mysqli->prepare("DELETE FROM bookmark WHERE id=?");
  	if ($stmt) {
  	//プレースホルダへ実際の値を設定する
      $stmt->bind_param('i', $id);
      $id = $_POST['id'];
  }
  }

  if ($stmt) {
      //クエリ実行
      $stmt->execute();
      //ステートメント切断
      $stmt->close();
    }else{
      echo $mysqli->errno . $mysqli->error;
    }


    header("Location: " . $_SERVER['PHP_SELF']);
}

 ?>






<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>AnythingBookMaker</title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
  <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
        <a class="navbar-brand" href="list.php">AnythingBookMaker</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
        <div class="collapse navbar-collapse" id="navbar">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item">
              <a class="nav-link" href="list.php">一覧</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="add.php">追加</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
  <div class="container">
    <div class="bs-docs-section" style="margin-top: 0px;">
      <div class="row">
        <div class="col-lg-12">
          <div class="page-header">
            <h1 id="tables">ブックマーク一覧</h1>
          </div>
          <div class="bs-component">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>No</th>
                  <th>タイトル</th>
                  <th>メモ</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>

              	<!--
              	<?php $count = 1 ?>
              	<?php foreach($stmt as $i => $row) : ?>
              		<tr>
              			<td><?= $count ?><input type="hidden" name="id" value="<?= $count ?>"></td>
              			<td><a href="<?= $row['url']?>" target="_blank"><?= $row['title']?></a></td>
              			<td><?= $row['memo']?></td>
              			<td>
              				<a href="edit.php?id=<?=$row['id']?>&title=<?=$row['title']?>&url=<?= $row['url']?>&memo=<?=$row['memo']?>" class="btn btn-warning">変更</a>
              			</td>
              		</tr>
              	<?php $count++;
                    endforeach; ?>
                -->



                <?php if(isset($rows)): ?>



                <?php foreach($rows as $row): ?>
                	<tr>
                		<td><?= $row['id']?></td>
                		<td><a href="<?= $row['url']?>" target="_blank"><?= $row['title']?></a></td>
                		<td><?= $row['memo']?></td>
                		<td>
              				<form action="edit.php" method="POST">
	              				<input type="submit" value="変更する" class="btn btn-warning">
	              				<input type="hidden" name="id" value="<?=$row['id']?>">
              				</form>
              			</td>
                	</tr>
                <?php endforeach; ?>

                <?php else : ?>
                	<tr><td>データはありません。</td></tr>

            <?php endif; ?>




              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="./js/bootstrap.min.js"></script>

  <script type="text/javascript">
    $('.bs-component [data-toggle="popover"]').popover();
    $('.bs-component [data-toggle="tooltip"]').tooltip();
  </script>
</body>

</html>