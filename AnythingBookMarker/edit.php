<?php
function sethtmlspecialchars($data){
  if(is_array($data)){
  return array_map("sethtmlspecialchars",$data);
} else {
  return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
}

require_once("dbConnect.php");
$mysqli = db_connect();

$_POST = sethtmlspecialchars($_POST);
 
if(empty($_POST)) {
  echo "<a href='list.php'>list.php</a>←こちらのページからどうぞ";
  exit();
}else{
  if (!isset($_POST['id'])  || !is_numeric($_POST['id']) ){
    echo "IDエラー";
    exit();
  }else{
    //プリペアドステートメント
    $stmt = $mysqli->prepare("SELECT * FROM bookmark WHERE id=?");
    if ($stmt) {
      //プレースホルダへ実際の値を設定する
      $stmt->bind_param('i', $id);
      $id = $_POST['id'];
      
      //クエリ実行
      $stmt->execute();
      
      //結果変数のバインド
      $stmt->bind_result($id,$title,$url,$memo);
      // 値の取得
      $stmt->fetch();
            
      //ステートメント切断
      $stmt->close();
    }else{
      echo $mysqli->errno . $mysqli->error;
    }
  }
}
 
// データベース切断
$mysqli->close();







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
            <h1 id="forms">ブックマークの変更</h1>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <div class="well bs-component">
            <form class="form-horizontal" action="list.php" method="POST">
              <fieldset>
                <legend>ブックマーク</legend>
                <input type="hidden" name="id" value="<?=$id?>">
                <div class="form-group">
                  <label for="inputTitle" class="col-lg-2 control-label">タイトル</label>
                  <div class="col-lg-10">
                    <input type="text" class="form-control" id="inputTitle" name="title" placeholder="タイトル" value="<?=$title?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputURL" class="col-lg-2 control-label">URL</label>
                  <div class="col-lg-10">
                    <input type="text" class="form-control" id="inputURL" name="url" placeholder="URL" value="<?=$url?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="textArea" class="col-lg-2 control-label">メモ</label>
                  <div class="col-lg-10">
                    <textarea class="form-control" rows="3" name="memo" id="textArea"><?=$memo?></textarea>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-lg-10 col-lg-offset-2">
                    <button type="reset" class="btn btn-default">キャンセル</button>
                    <button type="submit" name="update" class="btn btn-primary">変更</button>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-lg-1 col-lg-offset-11">
                    <button type="submit" name="delete" class="btn btn-danger">削除</button>
                  </div>
                </div>
              </fieldset>
            </form>
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