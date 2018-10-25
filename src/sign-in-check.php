<?php
    session_start();

    header("Content-type: text/html; charset=utf-8");

	// CSRF対策のトークン判定
	if($_POST['token'] != $_SESSION['token']) {
		echo "不正アクセスの可能性あり";
		exit();
	}

	// クリックジャッキング対策
	header('X-FRAME-OPTIONS: SAMEORIGIN');

 	// DB接続
	require_once("db.php");
	$pdo = db_connect();

	// 前後にある半角全角スペースを削除する関数
	function spaceTrim($str) {
		// 行頭
		$str = preg_replace('/^[ 　]+/u', '', $str);
		// 行末
		$str = preg_replace('/[ 　]+$/u', '', $str);
		return $str;
	}

	// エラーメッセージの初期化
	$errors = array();

	if(empty($_POST)) {
		header("Location:sign-in-check.php");
		exit();
	} else {
        // POSTされたデータを各変数に入れる
		$handle_name = isset($_POST['handle-name']) ? $_POST['handle-name'] : NULL;
		$password = isset($_POST['password']) ? $_POST['password'] : NULL;

		// 前後にある半角全角スペースを削除
		$handle_name = spaceTrim($handle_name);
		$password = spaceTrim($password);
	}

	try {
		// 例外処理を投げるようにする
		$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// ハンドルネームで検索
		$statement = $pdo -> prepare("SELECT * FROM member WHERE handle_name=(:handle_name)");
		$statement -> bindValue(':handle_name', $handle_name, PDO::PARAM_STR);
		$statement -> execute();

		if ($row = $statement -> fetch()) {
			$password_db = $row['password'];

			if ($password == $password_db) {
				// セッションハイジャック対策
				session_regenerate_id(true);

				$_SESSION['handle_name'] = $handle_name;
				header("Location: home.php");
				exit();
			} else {
				$errors['password'] = "アカウント及びパスワードが一致しません。";
			}
		} else {
			$errors['handle_name'] = "アカウント及びパスワードが一致しません。";
		}

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
                        <a class="mdl-navigation__link" href="sign-in.php">Sign in</a>
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

                    <!-- エラーがないとき -->
					<?php if (count($errors) === 0): ?>

                    <!-- エラーがあるとき -->
					<?php elseif (count($errors) > 0): ?>
                    <?php
                    	foreach($errors as $value) {
                    		echo "<p>".$value."</p>";
                    	}
                    ?>
                    <input type="button" value="戻る" class="mdl-button mdl-js-button mdl-button--raised mdl-button--accent" onClick="history.back()">

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
