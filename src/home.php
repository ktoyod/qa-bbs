<?php
    session_start();
    
    header("Content-type: text/html; charset=utf-8");

	// ログイン状態のチェック
	if (!isset($_SESSION['handle_name'])) {
		header("Location: sign-in.php");
		exit();
	}

	$handle_name = $_SESSION[handle_name];

	// DB接続
	require_once("db.php");

	// エラーメッセージの初期化
	$errors = array();

	try {
        $pdo = db_connect();
		//例外処理を投げるようにする
		$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$statement = $pdo -> prepare("SELECT id from member WHERE handle_name=(:handle_name)");

		// プレースホルダへ実際の値を設定する
		$statement -> bindValue(':handle_name', $_SESSION['handle_name'], PDO::PARAM_STR);
		$statement -> execute();

		$fetch_id = $statement -> fetch();
		$user_id = $fetch_id[0];

		$_SESSION['user_id'] = $user_id;

		// データベース接続切り替え
		$pdo = null;
	} catch (PDOException $e) {
		// トランザクション取り消し（ロールバック）
		$pdo -> rollBack();
		$errors['error'] = "もう一度やり直してください。";
		print('Error:'.$e -> getMessage());
	}

	// 自分の関わったスレッド
	try {
		// 例外処理を投げるようにする
	    $pdo = db_connect();
		$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// ハンドルネームで記事を検索
		$my_threads = $pdo -> prepare("SELECT * FROM question WHERE user_id=(:user_id) ORDER BY id DESC LIMIT 10");
		$my_threads -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$my_threads -> execute();

		// $my_threads = $statement -> fetch();

		$pdo = null;

	} catch (PDOException $e) {
		print('Error:'.$e -> getMessage());
		die();
	}

	// 新着のスレッド
	try {
		// 例外処理を投げるようにする
	    $pdo = db_connect();
		$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// 新しい順 or 人気順で記事を検索
		$display_threads = $pdo -> prepare("SELECT * FROM question ORDER BY id DESC LIMIT 10");
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
            <main class="mdl-layout__content mdl-layout__header--scroll">
				<div class="page-content">
					こんにちは、<?= $handle_name?>さん。<br>
                    あなたのIDは<?= $user_id?>です。<br>
                    <hr>
	                <!-- 自分が関わっているなスレッドを表示 -->
					<?php if ($my_threads): ?>
					<h3>Your Threads</h3>
					<?php foreach ($my_threads as $my_thread): ?>
					<div class="question">
					    <form action="thread.php" method="POST">
                            <input type="hidden" name="question_id" value="<?= $my_thread['id'] ?>">
					        <div class="question-card mdl-card mdl-shadow--2dp">
                                <div class="mdl-card__title">
					        	    <h2 class="mdl-card__title-text"><?= $my_thread['title'] ?></h2>
					        	    <h4 class="mdl-card__subtitle-text"><?= '@'.$my_thread['mission'] ?></h4>
                                    <i class="mdl-card_subtitle-text material-icons">perm_identity</i>
					        	    <h4 class="mdl-card__subtitle-text"><?= $my_thread['handle_name'] ?></h4>
                                </div>
                                <div class="mdl-card__supporting-text">
                                    <?= $my_thread['content'] ?>
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
                    <br>
	                <?php else: ?>
					    No Thread<br>
                    <?php endif; ?>
	                
	                <!-- 新しい or 人気なスレッドを表示 -->
					<?php if ($display_threads): ?>
					<h3>New Threads</h3>
					<?php foreach ($display_threads as $display_thread): ?>
					<div class="question">
					    <form action="thread.php" method="POST">
					        <div class="question-card mdl-card mdl-shadow--2dp">
                                <div class="mdl-card__title">
					        	    <h2 class="mdl-card__title-text"><?= $display_thread['title'] ?></h2>
					        	    <h4 class="mdl-card__title-text"><?= $display_thread['mission'] ?></h4>
                                    by
					        	    <h4 class="mdl-card__title-text"><?= $display_thread['handle_name'] ?></h4>
                                </div>
                                <div class="mdl-card__supporting-text">
                                    <form action="mail-check.php" method="post">
                                        <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                            <?= $display_thread['content'] ?>
                                        </div>
                                        <input type="hidden" name="token" value="<?=$token?>">
                                    </form>
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
					    No Thread
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
