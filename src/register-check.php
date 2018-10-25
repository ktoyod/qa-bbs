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
		header("Location:index.php");
		exit();
	} else {
        // POSTされたデータを各変数に入れる
		$name = isset($_POST['name']) ? $_POST['name'] : NULL;
		$handle_name = isset($_POST['handle-name']) ? $_POST['handle-name'] : NULL;
		$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;
		$password = isset($_POST['password']) ? $_POST['password'] : NULL;

		// 前後にある半角全角スペースを削除
		$name = spaceTrim($name);
		$handle_name = spaceTrim($handle_name);
		$mail = spaceTrim($mail);
		$password = spaceTrim($password);

		try {
			// 例外処理を投げる
			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// flagが0の未登録者・仮登録日から24時間以内
			$statement_handle = $pdo -> prepare("SELECT COUNT(*) FROM member WHERE handle_name=(:handle_name)");
			$statement_handle -> bindValue(':handle_name', $handle_name, PDO::PARAM_STR);
			$statement_handle -> execute();

			$count = $statement_handle -> fetchColumn();

			if ($count > 0) {
				$errors['handle_name'] = "そのハンドルネームはすでに存在します。";
			}
		} catch (PDOException $e) {
			print('Error:'.$e -> getMessage());
			die();
		}
		try {
			// 例外処理を投げる
			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// flagが0の未登録者・仮登録日から24時間以内
			$statement_mail = $pdo -> prepare("SELECT COUNT(*) FROM member WHERE mail=(:mail)");
			$statement_mail -> bindValue(':mail', $mail, PDO::PARAM_STR);
			$statement_mail -> execute();

			$count = $statement_mail -> fetchColumn();

			if ($count > 0) {
				$errors['mail'] = "そのメールアドレスはすでに存在します。";
			}

			// データベース接続切断
			$pdo = null;
		} catch (PDOException $e) {
			print('Error:'.$e -> getMessage());
			die();
		}
	}

	// エラーがなければセッションに登録
	if (count($errors) === 0) {
		$_SESSION['name'] = $name;
		$_SESSION['handle_name'] = $handle_name;
		$_SESSION['mail'] = $mail;
		$_SESSION['password'] = $password;
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
            .sign-up-card {
                margin-top: 64px;
                margin-right: auto;
                margin-left: auto;
                text-align: center;
            }
            .sign-up-card.mdl-card {
                max-width: 900px;
                width: 100%;
            }
            .sign-up-card > .mdl-card__title {
                text-align: center;
                height: auto;
                color: #646464;
            }
            .mdl-card__title-text {
                margin-right: auto;
                margin-left: auto;
            }
            .mdl-card__supporting-text {
                margin-left: auto;
                margin-right: auto;
            }
            .mdl-textfield {
                margin-left: auto;
                margin-right: auto;
                width: 90%;
            }
            .mdl-textfield__input {
                width: 100%;
            }
            .mdl-button {
                margin-right: 4px;
                margin-left: 4px;
            }
            .page-content {
                text-align: center;
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
                        <a class="mdl-navigation__link" href="sign-in.php">Sign In</a>
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
                    <div class="sign-up-card mdl-card mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">SIGN IN</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            <form action="register.php" method="post">
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <!-- 氏名 -->
									<input class="mdl-textfield__input" type="text" name="name" id="name" value=<?= $name ?> disabled>
                                    <label class="mdl-textfield__label" for="name">Name</label>
                                </div>
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <!-- ハンドルネーム -->
									<input class="mdl-textfield__input" type="text" name="handle-name" id="handle-name" value=<?= $handle_name ?> disabled>
                                    <label class="mdl-textfield__label" for="handle-name">Handle Name</label>
                                </div>
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <!-- メールアドレス -->
									<input class="mdl-textfield__input" type="text" name="mail" id="mail" value=<?= $mail ?> disabled>
                                    <label class="mdl-textfield__label" for="mail">Mail</label>
                                </div>
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <!-- パスワード -->
									<input class="mdl-textfield__input" type="password" name="password" id="password" value=<?= $password ?> disabled>
                                    <label class="mdl-textfield__label" for="password">Password</label>
                                </div>
                                <input type="hidden" name="token" value="<?=$_POST['token']?>">
                                <div>
                                    <!-- 登録用ボタン -->
									<button onClick="history.back()" class="mdl-button mdl-js-button mdl-button--raised">
                                        Back
                                    </button>
                                    <!-- 登録用ボタン -->
                                    <button type="submit" class="mdl-button mdl-js-button mdl-button--raised mdl-button--accent">
                                        Confirm
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

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
