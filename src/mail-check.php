<?php
    session_start();
    
    header("Content-type: text/html; charset=utf-8");

	// CSRF対策のトークン判定
	if ($_POST['token'] != $_SESSION['token']) {
		echo "不正アクセスの可能性あり";
		exit();
	}

	// クリックジャッキング対策
	header('X-FRAME-OPTIONS: SAMEORIGIN');

	// データベース接続
	require_once("db.php");
	$pdo = db_connect();

	// エラーメッセージの初期化
	$errors = array();

	if (empty($_POST)) {
		header("Location: mail-check.php");
		exit();
	} else {
		// POSTされたデータを変数に入れる
		$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;

		// メール入力判定
		if ($mail == '') {
			$errors['mail'] = "メールが入力されていません。";
		} else {
			if (!preg_match("/^([a-zA-Z0-9])+([a-xA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)) {
				$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
			}

			/*
			 * TODO:
			 * ここで本登録用のmemberテーブルにすでに登録されているかどうかをチェックする。
			 * $errors['member_check'] = "このメールアドレスはすでに利用されております。";
			 */ 

		}
	}

	if (count($errors) === 0) {
		$urltoken = hash('sha256', uniqid(rand(), 1));
		$url = "http://tt-351.99sv-coco.com/my_bbs/sign-up.php"."?urltoken=".$urltoken;

		// ここでデータベースに登録する
		try {
			//例外処理を投げるようにする
			$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$statement = $pdo -> prepare("INSERT INTO pre_member (urltoken, mail, date) VALUES (:urltoken, :mail, now())");

			// プレースホルダへ実際の値を設定する
			$statement -> bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
			$statement -> bindValue(':mail', $mail, PDO::PARAM_STR);
			$statement -> execute();

			// データベース接続切り替え
			$pdo = null;
		} catch (PDOException $e) {
			print('Error:'.$e -> getMessage());
			die();
		}

		// メールの宛先
		$mailTo = $mail;

		// XXX: Return-Pathに指定するメールアドレス
		$returnMail = 'web@sample.com';

		$name = "TECH BBS";
		$mail = 'web@sample.com';
		$subject = "【TECH BBS】会員登録用URLのお知らせ";

		$body = "
        24時間以内に下記のURLからご登録ください。\n
        {$url}";
        
		mb_language('ja');
		mb_internal_encoding('UTF-8');

		// Fromヘッダーを作成
		$header = 'From: '.mb_encode_mimeheader($name).' <'.$mail.'>';

		if (mb_send_mail($mailTo, $subject, $body, $header, '-f'.$returnMail)) {
			// セッション変数を全て解除
			$_SESSION = array();

			// クッキーの削除
			if (isset($_COOKIE["PHPSESSID"])) {
				setcookie("PHPSESSID", '', time() - 1800, '/');
			}

			// セッションを破棄する
			session_destroy();

			$message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録ください。";
		} else {
			$errors['mail_error'] = "メールの送信に失敗しました。";
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
        <!-- Wide card with share menu button -->
        <!-- ここはcssファイルで切り出すべき -->
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
        <!-- タグでわかるけど一応ここまで -->
    </head>
    <body>
        <?php
            function random($length = 32) {
				return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $slength);
			}
            session_start();
            
			header("Content-type: text/html; charset=utf-8");

			// TODO: クロスサイトリクエストフォージェリ(CSRF)対策
			// $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
			// $token = $_SESSION['token'];
			$_SESSION['token'] = base64_encode(random());
			$token = $_SESSION['token'];

			// クリックジャッキング対策
			header('X-FRAME-OPTIONS: SAMEORIGIN');
		?>

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
                    <h2>メール確認画面</h2>
                     
                    <?php if (count($errors) === 0): ?>
                     
                    <p><?=$message?></p>
                     
                    <?php elseif(count($errors) > 0): ?>
                     
                    <?php
                        foreach($errors as $value){
                        	echo "<p>".$value."</p>";
                        }
                    ?>
                     
                    <input type="button" value="戻る" onClick="history.back()">
                     
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
