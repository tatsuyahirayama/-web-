<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

// 画面処理
$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：'.print_r($dbFormData,true));

if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  $username = $_POST['username'];
  $email = $_POST['email'];
  $pic = ( !empty($_FILES['pic']['name']) ) ? uploadImg($_FILES['pic'],'pic') : '';
  $pic = ( empty($pic) && !empty($dbFormData['pic']) ) ? $dbFormData['pic'] : $pic;

  if($dbFormData['username'] !== $username){
    //名前の最大文字数チェック
    validMaxLen($username, 'username');
  }

  if($dbFormData['email'] !== $email){
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    if(empty($err_msg['email'])){
      //emailの重複チェック
      validEmailDup($email);
    }
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの未入力チェック
    validRequired($email, 'email');
  }

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    try {
      $dbh = dbConnect();
      $sql = 'UPDATE users  SET username = :u_name,  email = :email, pic = :pic WHERE id = :u_id';
      $data = array(':u_name' => $username , ':email' => $email, ':pic' => $pic, ':u_id' => $dbFormData['id']);
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        $_SESSION['msg_success'] = SUC02;
        debug('マイページへ遷移します。');
        header("Location:index.php"); 
      }

    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'プロフィール編集';
require('head.php'); 
?>

<body class="page-profEdit page-logined">

  <?php
  require('header.php'); 
  ?>

  <div id="contents" class="site-width">

    <section id="main" >
      <div class="form-container">
        <form action="" method="post" class="form" enctype="multipart/form-data">
          <div class="flex">
            <div>
              <div class="area-msg">
                <?php 
                if(!empty($err_msg['common'])) echo $err_msg['common'];
                ?>
              </div>
              <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
                ニックネーム
                <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
              </label>
              <div class="area-msg">
                <?php 
                if(!empty($err_msg['username'])) echo $err_msg['username'];
                ?>
              </div>
              <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                Email
                <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
              </label>
              <div class="area-msg">
                <?php 
                if(!empty($err_msg['email'])) echo $err_msg['email'];
                ?>
              </div>
            </div>
            <div>
              プロフィール画像
              <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>" style="height:300px;line-height:100px;width:300px;">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic" class="input-file" style="height:100px;">
                <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
                  ドラッグ＆ドロップ
              </label>
              <div class="area-msg">
                <?php 
                if(!empty($err_msg['pic'])) echo $err_msg['pic'];
                ?>
              </div>

            </div>
          </div>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="変更する">
          </div>
        </form>
      </div>
    </section>
    <div> 
    <a class="withdraw" href="withdraw.php">退会</a>
    </div>
  </div>

  <?php
  require('footer.php'); 
  ?>
