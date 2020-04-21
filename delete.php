<?php

require('function.php');


require('auth.php');

$id = $_GET['id'];

$dbh = dbConnect();
$sql = 'SELECT * FROM message WHERE  id= :id';
$data = array(':id' => $id);
$messages = queryPost($dbh, $sql, $data);
$message = $messages->fetch(PDO::FETCH_ASSOC);

switch($message['board_id']){
    case 0:
        $board_name ='board_introduce';
    break; 
    case 1:
        $board_name ='board_event';
    break;
    case 2:
        $board_name ='board_question';
    break;
    case 3:
        $board_name ='board_japanesefood';
    break;
    case 4:
        $board_name ='board_chineseLang';
    break;
    case 5:
        $board_name ='board_holiday';
    break;
}

if($message['user_id'] === $_SESSION['user_id']){
    $dbh = dbConnect();
    $sql = 'DELETE FROM message WHERE id= :id';
    $data = array(':id' => $id);
    queryPost($dbh, $sql, $data);

}
header("Location:$board_name.php");
?>