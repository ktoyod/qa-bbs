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

	$name = $_SESSION['name'];
	$handle_name = $_SESSION['handle_name'];
	$mail = $_SESSION['mail'];

	// TODO: パスワードのハッシュ化
	$password_hash = $_SESSION['password'];
	// undefinedになっちゃうので原因究明 or 代替案
	// $password_hash = password_hash($_SESSION['password'], PASSWORD_DEFAULT);

	// ここでデータベースに登録する
	try {
		//例外処理を投げるようにする
		$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// トランザクション開始
		$pdo -> beginTransaction();

		$statement = $pdo -> prepare("INSERT INTO member (name, handle_name, mail, password, date) VALUES (:name, :handle_name, :mail, :password, now())");

		// プレースホルダへ実際の値を設定する
		$statement -> bindValue(':name', $name, PDO::PARAM_STR);
		$statement -> bindValue(':handle_name', $handle_name, PDO::PARAM_STR);
		$statement -> bindValue(':mail', $mail, PDO::PARAM_STR);
		$statement -> bindValue(':password', $password_hash, PDO::PARAM_STR);
		$statement -> execute();

		// トランザクション完了
		$pdo -> commit();

		// データベース接続切り替え
		$pdo = null;

		// セッション変数を全て解除
		$_SESSION = array();

		// セッションクッキーの削除・sessionidとの関係を探れ。つまりはじめのsessionidを名前でやる
		if (isset($_COOKIE["PHPSESSID"])) {
			setcookie("PHPSESSID", '', time() - 1800, '/');
		}

		// セッションを破棄する
		session_destroy();

	    // メールの宛先
	    $mailTo = $mail;

	    // TODO: Return-Pathに指定するメールアドレス
	    $returnMail = 'web@sample.com';

	    $page_name = "TECH BBS";
	    $mail = 'web@sample.com';
	    $subject = "【TECH BBS】会員登録用完了のお知らせ";

	    $body = "
        TECH BBSへの登録が完了しました！\n
        以下のリンクからサインインしてください。\n
        {$url}";
        
	    mb_language('ja');
	    mb_internal_encoding('UTF-8');

	    // Fromヘッダーを作成
	    $header = 'From: '.mb_encode_mimeheader($page_name).' <'.$mail.'>';

	    if (mb_send_mail($mailTo, $subject, $body, $header, '-f'.$returnMail)) {
	    } else {
	    	$errors['mail_error'] = "メールの送信に失敗しました。";
	    }

	    // サインイン用ページに遷移
	    header("Location: http://tt-351.99sv-coco.com/my_bbs/sign-in.php");
	    exit;
	} catch (PDOException $e) {
		// トランザクション取り消し（ロールバック）
		$pdo -> rollBack();
		$errors['error'] = "もう一度やり直してください。";
		print('Error:'.$e -> getMessage());
	}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>【TECH BBS】登録用ページ</title>
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
                    <div class="mdl-spinner mdl-js-spinner is-active"></div>
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
