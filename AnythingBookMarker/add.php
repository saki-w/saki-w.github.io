 <?php

function sethtmlspecialchars($data){
  if(is_array($data)){
  return array_map("sethtmlspecialchars",$data);
} else {
  return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
}






if($_SERVER['REQUEST_METHOD'] === 'POST'){
  
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


  if(!$err){

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

  $sql = "INSERT INTO bookmark(title, url, memo)VALUES(?, ?, ?)";
  $stmt = $db->prepare($sql);
  $stmt->execute([$title, $url, $memo]);
  $id = $db->lastInsertId();

}catch(PDOException $e){
  echo $e->getMessage();

  exit;
}


}


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
              <h1 id="forms">ブックマークの追加</h1>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="well bs-component">
              <form class="form-horizontal" action="" method="POST">
                <fieldset>
                  <legend>ブックマーク</legend>


                  

                <?php if($err) :?>
                    <div class="alert alert-dismissible alert-warning">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <h4>エラー</h4>
                      <p>タイトルとURLは必ず入力してください。</p>
                    </div>
                  <?php endif; ?>

                  <?php if(isset($id)) :?>
                    <div class="alert alert-dismissble alert-success">
                      <h4>登録しました！</h4>
                    </div>
                  <?php endif; ?>






                  <div class="form-group">
                    <label for="inputTitle" class="col-lg-2 control-label">タイトル</label>
                    <div class="col-lg-10">
                      <input type="text" class="form-control" id="inputTitle" name="title" placeholder="タイトル" value="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputURL" class="col-lg-2 control-label">URL</label>
                    <div class="col-lg-10">
                      <input type="text" class="form-control" id="inputURL" name="url" placeholder="URL" value="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="textArea" class="col-lg-2 control-label">メモ</label>
                    <div class="col-lg-10">
                      <textarea class="form-control" rows="3" name="memo" id="textArea"></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-2">
                      <button type="reset" class="btn btn-default">キャンセル</button>
                      <button type="submit" class="btn btn-primary">登録</button>
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