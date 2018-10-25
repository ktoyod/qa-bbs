<?php
    session_start();

    header("Content-type: text/html; charset=utf-8");

	// ログイン状態のチェック
	if (!isset($_SESSION['handle_name'])) {
		header("Location: sign-in.php");
		exit();
	}

    // データベース接続
    require_once("db.php");

	// クリックジャッキング対策
	header('X-FRAME-OPTIONS: SAMEORIGIN');

	if(empty($_POST)) {
		header("Location:index.php");
		exit();
	} else {
        // POSTされたデータを各変数に入れる
		$id = isset($_POST['id']) ? $_POST['id'] : NULL;
		$comment = isset($_POST['comment']) ? $_POST['comment'] : NULL;
		$question_id = isset($_POST['question_id']) ? $_POST['question_id'] : NULL;
	}

	// ここでデータベースに登録する
    if (empty($id)) {
    	try {
    		$user_id = $_SESSION['user_id'];
    		$handle_name = $_SESSION['handle_name'];
    
            $pdo = db_connect();
    		//例外処理を投げるようにする
    		$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    		// トランザクション開始
    		$pdo -> beginTransaction();
    
    		$statement = $pdo -> prepare("INSERT INTO answer (comment, user_id, handle_name, question_id, date) VALUES (:comment, :user_id, :handle_name, :question_id, now())");
    
    		// プレースホルダへ実際の値を設定する
    		$statement -> bindValue(':comment', $comment, PDO::PARAM_STR);
    		$statement -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
    		$statement -> bindValue(':handle_name', $handle_name, PDO::PARAM_STR);
    		$statement -> bindValue(':question_id', $question_id, PDO::PARAM_INT);
    		$statement -> execute();
    
    		// トランザクション完了
    		$pdo -> commit();
    
    		// データベース接続切り替え
    		$pdo = null;
    	} catch (PDOException $e) {
    		// トランザクション取り消し（ロールバック）
    		$pdo -> rollBack();
    		$errors['error'] = "もう一度やり直してください。";
    		print('Error:'.$e -> getMessage());
    	}
	} else {
    	try {
    		$user_id = $_SESSION['user_id'];
    		$handle_name = $_SESSION['handle_name'];
    
            $pdo = db_connect();
    		//例外処理を投げるようにする
    		$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    		// トランザクション開始
    		$pdo -> beginTransaction();
    
    		$statement = $pdo -> prepare("UPDATE answer SET comment=:comment, date=now() WHERE id=:id");
    
    		// プレースホルダへ実際の値を設定する
    		$statement -> bindValue(':comment', $comment, PDO::PARAM_STR);
    		$statement -> bindValue(':id', $id, PDO::PARAM_INT);
    		$statement -> execute();
    
    		// トランザクション完了
    		$pdo -> commit();
    
    		// データベース接続切り替え
    		$pdo = null;
    	} catch (PDOException $e) {
    		// トランザクション取り消し（ロールバック）
    		$pdo -> rollBack();
    		$errors['error'] = "もう一度やり直してください。";
    		print('Error:'.$e -> getMessage());
    	}
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
		<style>
            html > body {
                font-family: 'Roboto','Helvetica','Arial',sans-serif!important;
                background-color: #FAFAFA;
                color: #646464;
            }
            .page-content {
                text-align: center;
            }
        </style>
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
                        <a class="mdl-navigation__link" href="sign-out.php">Sign Out</a>
                    </nav>
                </div>
            </header>

            <div class="mdl-layout__drawer">
                <span class="mdl-layout-title">TECH BBS</span>
                <nav class="mdl-navigation">
                    <a class="mdl-navigation__link" href="">Link1</a>
                    <a class="mdl-navigation__link" href="">Link2</a>
                    <a class="mdl-navigation__link" href="">Link3</a>
                    <a class="mdl-navigation__link" href="">Link4</a>
                </nav>
            </div>
              
            <!-- ここからmain -->
            <main class="mdl-layout__content">
				<div class="page-content">
				    <h4>SUBMITTED!!</h4>
                    <?= $comment ?><br>
					<br>
                    <div>
                        <form method="post" action="thread.php">
                            <input type="hidden" name="question_id" value="<?= $question_id ?>">
				    	    <button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect">
                                GO TO THREAD PAGE
                            </button>
                        </form>
                    </div>
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
