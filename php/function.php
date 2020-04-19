<?php
//================================
// ログ
//================================
ini_set('log_errors','on');
ini_set('error_log','php.log');

//================================
// デバッグ
//================================
$debug_flg = true;
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//================================
// セッション
//================================
session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime ', 60*60*24*30);
session_start();
session_regenerate_id();

//================================
// 画面表示処理開始時のログ
//================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug( 'ログイン期限日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );
  }
}

//================================
// エラーメッセージ
//================================
define('MSG01','入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03','パスワード（再入力）が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','256文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('MSG14', '文字で入力してください');
define('MSG15', '正しくありません');
define('MSG16', '有効期限が切れています');
define('MSG17', '半角数字のみご利用いただけます');
define('SUC01', 'パスワードを変更しました');
define('SUC02', 'プロフィールを変更しました');


//================================
// エラーメッセージ
//================================
$err_msg = array();

//================================
// バリデーション
//================================

//未入力チェック
function validRequired($str, $key){
  if($str === ''){ 
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}
//mail形式チェック
function validEmail($str, $key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
//Email重複チェック
function validEmailDup($email){
  global $err_msg;
  try {
    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG08;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
//同値チェック
function validMatch($str1, $str2, $key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}
//最小文字数チェック
function validMinLen($str, $key, $min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}
//最大文字数チェック
function validMaxLen($str, $key, $max = 256){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}
//半角チェック
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}
//半角数字チェック
function validNumber($str, $key){
  if(!preg_match("/^[0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG17;
  }
}
//固定長チェック
function validLength($str, $key, $len = 8){
  if( mb_strlen($str) !== $len ){
    global $err_msg;
    $err_msg[$key] = $len . MSG14;
  }
}
//パスワードチェック
function validPass($str, $key){
  //半角英数字チェック
  validHalf($str, $key);
  //最大文字数チェック
  validMaxLen($str, $key);
  //最小文字数チェック
  validMinLen($str, $key);
}

//エラーメッセージの表示
function getErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return $err_msg[$key];
  }
}

//================================
// ログイン認証
//================================
function isLogin(){
  if( !empty($_SESSION['login_date']) ){
    debug('ログイン済みユーザーです。');

    if( ($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
      debug('ログイン有効期限オーバーです。');

      session_destroy();
      return false;
    }else{
      debug('ログイン有効期限以内です。');
      return true;
    }

  }else{
    debug('未ログインユーザーです。');
    return false;
  }
}

//================================
// データベース
//================================
function dbConnect(){
  $dsn = 'mysql:dbname=portfolio;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}
function queryPost($dbh, $sql, $data){
  $stmt = $dbh->prepare($sql);
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました。');
    debug('失敗したSQL：'.print_r($stmt,true));
    $err_msg['common'] = MSG07;
    return 0;
  }
  debug('クエリ成功。');
  return $stmt;
}
function getUser($u_id){
  debug('ユーザー情報を取得します。');
  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM users  WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
    
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}


//================================
// その他
//================================
// サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}
// フォーム入力保持
function getFormData($str, $flg = false){
  if($flg){
    $method = $_GET;
  }else{
    $method = $_POST;
  }
  global $dbFormData;
  if(!empty($dbFormData)){
    if(!empty($err_msg[$str])){
      if(isset($method[$str])){
        return sanitize($method[$str]);
      }else{
        return sanitize($dbFormData[$str]);
      }
    }else{
      if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
        return sanitize($method[$str]);
      }else{
        return sanitize($dbFormData[$str]);
      }
    }
  }else{
    if(isset($method[$str])){
      return sanitize($method[$str]);
    }
  }
}
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}
//認証キー
function makeRandKey($length = 8) {
    static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ($i = 0; $i < $length; ++$i) {
        $str .= $chars[mt_rand(0, 61)];
    }
    return $str;
}
// 画像処理
function uploadImg($file, $key){
  debug('画像アップロード処理開始');
  debug('FILE情報：'.print_r($file,true));
  
  if (isset($file['error']) && is_int($file['error'])) {
    try {
      switch ($file['error']) {
          case UPLOAD_ERR_OK:
              break;
          case UPLOAD_ERR_NO_FILE:  
              throw new RuntimeException('ファイルが選択されていません');
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
              throw new RuntimeException('ファイルサイズが大きすぎます');
          default:
              throw new RuntimeException('その他のエラーが発生しました');
      }
      

      $type = @exif_imagetype($file['tmp_name']);
      if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) { // 第三引数にはtrueを設定すると厳密にチェックしてくれるので必ずつける
          throw new RuntimeException('画像形式が未対応です');
      }


      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
      if (!move_uploaded_file($file['tmp_name'], $path)) {
          throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }
 
      chmod($path, 0644);
      
      debug('ファイルは正常にアップロードされました');
      debug('ファイルパス：'.$path);
      return $path;

    } catch (RuntimeException $e) {

      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();

    }
  }
}
//画像表示用関数
function showImg($path){
  if(empty($path)){
    return 'img/sample-img.png';
  }else{
    return $path;
  }
}
