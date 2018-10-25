<?php
    session_start();
    
    header("Content-type: text/html; charset=utf-8");

	// ログイン状態のチェック
	if (!isset($_SESSION['handle_name'])) {
		header("Location: sign-in.php");
		exit();
	}

    $_SESSION['question_id'] = '';

	$handle_name = $_SESSION['handle_name'];
	$mission = 'Mission3';

	// DB接続
	require_once("db.php");

	// エラーメッセージの初期化
	$errors = array();

	$user_id = $_SESSION['user_id'];

	// 人気のスレッド
	try {
		// 例外処理を投げるようにする
	    $pdo = db_connect();
		$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// 新しい順 or 人気順で記事を検索
		$display_threads = $pdo -> prepare("SELECT * FROM question WHERE mission=:mission");
		$display_threads -> bindValue(':mission', $mission, PDO::PARAM_STR);
		$display_threads -> execute();

		// $display_threads = $statement -> fetch();

		$pdo = null;

	} catch (PDOException $e) {
		print('Error:'.$e -> getMessage());
		die();
	}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>TECH BBS</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css">
        <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
        <!-- Wide card with share menu button -->
        <!-- ここはcssファイルで切り出すべき -->
		<style>
            html > body {
                font-family: 'Roboto','Helvetica','Arial',sans-serif!important;
                background-color: #FAFAFA;
                color: #646464;
            }
            .question-card {
                margin-right: auto;
                margin-left: auto;
                margin-bottom: 16px;
            }
            .question-card.mdl-card {
                max-width: 900px;
                width: 100%;
            }
            .question-card > .mdl-card__title {
                height: auto;
                color: #646464;
            }
            .question-card > .mdl-card_subtitle-text {
                text-align: right;
            }
            .page-content {
                text-align: center;
            }
			.question {
              margin: 4px;
            }
			.mdl-card__supporting-text{
	            word-wrap: break-word;			
                width: 90%;
                height: auto;
                padding-right: 0px;
                padding-left: 0px;
                margin-right: auto;
                margin-left: auto;
            }
        </style>
        <!-- タグでわかるけど一応ここまで -->
    </head>
    <body>
        <!-- Uses a header that scrolls with the text, rather than staying
          locked at the top -->
        <div class="mdl-layout mdl-js-layout">
            <!-- ここからheader -->
            <header class="mdl-layout__header mdl-layout__header--scroll">
                <div class="mdl-layout__header-row">
                    <!-- Title -->
                    <span class="mdl-layout-title">TECH BBS</span>
                    <!-- Add spacer, to align navigation to the right -->
                    <div class="mdl-layout-spacer"></div>
                    <!-- Navigation -->
                    <nav class="mdl-navigation">
                        <a class="mdl-navigation__link" href="question-write.php">Submit Question</a>
                        <a class="mdl-navigation__link" href="sign-out.php">Sign Out</a>
                    </nav>
                </div>
            </header>

            <div class="mdl-layout__drawer">
                <span class="mdl-layout-title">TECH BBS</span>
                <nav class="mdl-navigation">
                    <a class="mdl-navigation__link" href="home.php">HOME</a>
                    <a class="mdl-navigation__link" href="mission1.php">Mission1</a>
                    <a class="mdl-navigation__link" href="mission2.php">Mission2</a>
                    <a class="mdl-navigation__link" href="mission3.php">Mission3</a>
                    <a class="mdl-navigation__link" href="mission4.php">Mission4</a>
                    <a class="mdl-navigation__link" href="others.php">Others</a>
                </nav>
            </div>
              
            <!-- ここからmain -->
            <main class="mdl-layout__content">
				<div class="page-content">
					<h3>Mission3</h3>
	                
	                <!-- 新しい or 人気なスレッドを表示 -->
					<?php if ($display_threads): ?>
					<?php foreach ($display_threads as $display_thread): ?>
					<div class="question">
					    <form action="thread.php" method="POST">
                            <input type="hidden" name="question_id" value="<?= $display_thread['id'] ?>">
					        <div class="question-card mdl-card mdl-shadow--2dp">
                                <div class="mdl-card__title">
					        	    <h2 class="mdl-card__title-text"><?= $display_thread['title'] ?></h2>
					        	    <h4 class="mdl-card__subtitle-text"><?= '@'.$display_thread['mission'] ?></h4>
                                    <i class="mdl-card_subtitle-text material-icons">perm_identity</i>
					        	    <h4 class="mdl-card__subtitle-text"><?= $display_thread['handle_name'] ?></h4>
                                </div>
                                <div class="mdl-card__supporting-text">
                                    <?= $display_thread['content'] ?>
                                </div>
                                <div class="mdl-card__actions mdl-card--border">
                                    <button type="submit" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                                        SHOW THIS QUESTION
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endforeach; ?>
	                <?php else: ?>
						まだスレッドはありません。
                    <?php endif; ?>
                </div>
            </main>
            
            <!-- ここからfooter -->
            <footer class="mdl-mega-footer">
                <div class="mdl-mega-footer__middle-section">
            
                    <div class="mdl-mega-footer__drop-down-section">
                        <input class="mdl-mega-footer__heading-checkbox" type="checkbox" checked>
                        <h1 class="mdl-mega-footer__heading">Features</h1>
                        <ul class="mdl-mega-footer__link-list">
                            <li><a href="#">About</a></li>
                            <li><a href="#">Terms</a></li>
                            <li><a href="#">Partners</a></li>
                            <li><a href="#">Updates</a></li>
                        </ul>
                    </div>
            
                    <div class="mdl-mega-footer__drop-down-section">
                        <input class="mdl-mega-footer__heading-checkbox" type="checkbox" checked>
                        <h1 class="mdl-mega-footer__heading">Details</h1>
                        <ul class="mdl-mega-footer__link-list">
                            <li><a href="#">Specs</a></li>
                            <li><a href="#">Tools</a></li>
                            <li><a href="#">Resources</a></li>
                        </ul>
                    </div>
            
                    <div class="mdl-mega-footer__drop-down-section">
                        <input class="mdl-mega-footer__heading-checkbox" type="checkbox" checked>
                        <h1 class="mdl-mega-footer__heading">Technology</h1>
                        <ul class="mdl-mega-footer__link-list">
                            <li><a href="#">How it works</a></li>
                            <li><a href="#">Patterns</a></li>
                            <li><a href="#">Usage</a></li>
                            <li><a href="#">Products</a></li>
                            <li><a href="#">Contracts</a></li>
                        </ul>
                    </div>
            
                    <div class="mdl-mega-footer__drop-down-section">
                        <input class="mdl-mega-footer__heading-checkbox" type="checkbox" checked>
                        <h1 class="mdl-mega-footer__heading">FAQ</h1>
                        <ul class="mdl-mega-footer__link-list">
                            <li><a href="#">Questions</a></li>
                            <li><a href="#">Answers</a></li>
                            <li><a href="#">Contact us</a></li>
                        </ul>
                    </div>
            
                </div>
            
                <div class="mdl-mega-footer__bottom-section">
                    <div class="mdl-logo">Title</div>
                    <ul class="mdl-mega-footer__link-list">
                        <li><a href="#">Help</a></li>
                        <li><a href="#">Privacy & Terms</a></li>
                    </ul>
                </div>
            
            </footer>
        </div>
    </body>
</html>
