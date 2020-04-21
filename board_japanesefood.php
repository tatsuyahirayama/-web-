<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　連絡掲示板ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

if(!empty($_POST)){
  debug('POST送信があります。');
  require('auth.php');
  
  $msg = (isset($_POST['msg'])) ? $_POST['msg'] : '';
  validMaxLen($msg, 'msg', 500);
  validRequired($msg, 'msg');
  
  if(empty($err_msg)){
    debug('バリデーションOKです。');
    try {
      $dbh = dbConnect();
      $sql = 'INSERT INTO message (message, user_id, board_id, created_date) VALUES (:message, :user_id, :board_id, :created_date)';
      $data = array(':message' => $msg, ':user_id' => $_SESSION['user_id'], ':board_id' => 3, ':created_date' => date('Y-m-d H:i:s'));
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        $_POST = array(); 
        debug('連絡掲示板へ遷移します。');
        header("Location: " . $_SERVER['PHP_SELF'] .'?m_id='.$m_id); 
      }

    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}

$dbh = dbConnect();
$posts = $dbh->query('SELECT u.username, u.pic, m.* FROM users u, message m WHERE u.id = m.user_id AND board_id = 3 ORDER BY m.created_date DESC');

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = '連絡掲示板';
require('head.php'); 
?>

  <body class="page-msg page-1colum">
    <style>
      .board-title{
        margin: 0 auto 20px;
        padding: 20px;
        width: 500px;
        border: 3px solid #f1f1f1;
        box-sizing: border-box;
        text-align:center;
      }
      .board-title h1{
        margin-bottom:10px;
      }
      .msg-info{
        background: #f6f5f4;
        padding: 15px;
        overflow: hidden;
        margin-bottom: 15px;
      }
      .msg-info .avatar{
        width: 80px;
        height: 80px;
        border-radius: 40px;
      }
      .msg-info .avatar-img{
        text-align: center;
        width: 100px;
        float: left;
      }
      .msg-info .avatar-info{
        float: left;
        padding-left: 15px;
        width: 500px;
      }
      .area-bord{
        height: 500px;
        overflow-y: scroll;
        background: #f6f5f4;
        padding: 15px;
      }
      .area-send-msg{
        background: #f6f5f4;
        padding: 15px;
        overflow: hidden;
      }
      .area-send-msg textarea{
        width:100%;
        background: white;
        height: 100px;
        padding: 15px;
      }
      .area-send-msg .btn-send{
        width: 150px;
        float: right;
        margin-top: 0;
      }
      .area-bord .msg-cnt{
        width: 90%;
        overflow: hidden;
        margin-bottom: 20px;
      }
      .area-bord .msg-cnt .avatar{
        width: 7%;
        overflow: hidden;
        float: left;
      }
      .area-bord .msg-cnt .avatar img{
        width: 40px;
        height: 40px;
        border-radius: 20px;
        float: left;
      }
      .area-bord .msg-cnt .msg-inrTxt{
        width: 85%;
        float: left;
        border-radius: 5px;
        padding: 10px;
        margin: 0 0 0 20px;
        position: relative;
      }
      .area-bord .msg-cnt.msg-left .msg-inrTxt{
        background: #f6e2df;
      }
      .area-bord .msg-cnt.msg-left .msg-inrTxt > .triangle{
        position: absolute;
        left: -20px;
        width: 0;
        height: 0;
        border-top: 10px solid transparent;
        border-right: 15px solid #f6e2df;
        border-left: 10px solid transparent;
        border-bottom: 10px solid transparent;
      }

    </style>

    <?php
      require('header.php'); 
    ?>
    

    <div id="contents" class="site-width">
      <section>
        <div class="board-title">
          <h1>おすすめ日本料理店</h1>
          <p>中国滞在が続くと日本料理が恋しくなるものです。</p>
          <p>おすすめの日本料理店を共有し合いましょう！</p>
        </div>
      </section>
      <section id="main" >
        <div class="area-send-msg">
          <form action="" method="post">
            <textarea name="msg" cols="30" rows="3"></textarea>
            <input type="submit" value="送信" class="btn btn-send">
          </form>
        </div>
        <div class="area-bord" id="js-scroll-bottom">
         <?php
              foreach($posts as $post): ?>
                    <span><?php echo $post['username']; ?></span>
                    <div class="msg-cnt msg-left">
                      <div class="avatar">
                        <img src="<?php echo $post['pic']; ?>" alt="" class="avatar">
                      </div>
                      <p class="msg-inrTxt">
                        <span class="triangle"></span>
                        <?php echo nl2br(htmlspecialchars($post['message'],ENT_QUOTES,'UTF-8')); ?>
                      </p>
                      <div style="font-size:0.8em;"><?php echo $post['created_date']; ?>
                      <?php if($_SESSION['user_id'] ===$post['user_id']) :?>
                      <a style="margin-left:0.2rem" href="delete.php?id=<?php echo $post['id'] ?>">削除</a>
                      <?php endif; ?>
                      </div>
                    </div>
        <?php endforeach; ?>
        </div>
      </section>
      
      <script src="js/vendor/jquery-3.4.1.min.js"></script>
      
      <script>
        $(function(){
          $('#js-scroll-bottom').animate({scrollTop: $('#js-scroll-bottom')[200].scrollHeight}, 'fast');
        });
      </script>

    </div>

    <?php
      require('footer.php'); 
    ?>
