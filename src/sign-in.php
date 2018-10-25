<?php
    function random($length = 32) {
		return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $slength);
	}

    session_start();

    header("Content-type: text/html; charset=utf-8");

	// CSRF対策
	$_SESSION['token'] = base64_encode(random());
	$token = $_SESSION['token'];

	// クリックジャッキング対策
	header('X-FRAME-OPTIONS: SAMEORIGIN');
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
                        <a class="mdl-navigation__link" href="index.php">Sign Up</a>
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
                    <div class="sign-up-card mdl-card mdl-shadow--2dp">
                        <div class="mdl-card__title">
                            <h2 class="mdl-card__title-text">SIGN IN</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            <form action="sign-in-check.php" method="post">
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <!-- ハンドルネーム -->
                                    <input class="mdl-textfield__input" type="text" name="handle-name" id="handle-name" required>
                                    <label class="mdl-textfield__label" for="handle-name">Handle Name</label>
                                </div>
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <!-- パスワード -->
                                    <input class="mdl-textfield__input" type="password" name="password" id="password" required> 
                                    <label class="mdl-textfield__label" for="password">Password</label>
                                </div>
                                <div>
                                    <!-- 登録用ボタン -->
                                    <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--accent">
                                        Sign In
                                    </button>
                                </div>
                            </form>
                        </div>
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
