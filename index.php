<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

$u_id = $_SESSION['user_id'];


debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'マイページ';
require('head.php'); 
?>

  <body class="page-mypage page-3colum">
    <style>
      #main{
        border: none !important;
      }
    </style>
    
    <?php
      require('header.php'); 
    ?>
    
    <p id="js-show-msg" style="display:none;" class="msg-slide">
      <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <div id="contents">
      
      <h1 class="page-title">深圳GUIDE(仮)</h1>

      <section id="main" class="flex">
        <article>
          <h2>自己紹介</h2>
          <a href="board_introduce.php"><img src="img/china01.jpg" alt=""></a>
        </article>
        <article>
          <h2>イベント告知</h2>
          <a href="board_event.php"><img src="img/china02.jpg" alt=""></a>
        </article>
        <article>
          <h2>質問部屋</h2>
          <a href="board_question.php"><img src="img/china04.jpg" alt=""></a>
        </article>
        <article>
          <h2>お勧め日本料理店</h2>
          <a href="board_japanesefood.php"><img src="img/china03.jpg" alt=""></a>
        </article>
        <article>
          <h2>中国語学習</h2>
          <a href="board_chineseLang.php"><img src="img/china05.jpg" alt=""></a>
        </article>
        <article>
          <h2>休日の過ごし方</h2>
          <a href="board_holiday.php"><img src="img/china06.jpg" alt=""></a>
        </article>
      
      </section>

    </div>

    <?php
      require('footer.php'); 
    ?> 