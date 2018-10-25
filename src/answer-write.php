<?php
    session_start();
    
    header("Content-type: text/html; charset=utf-8");

	// ログイン状態のチェック
	if (!isset($_SESSION['handle_name'])) {
		header("Location: sign-in.php");
		exit();
	}

	$handle_name = $_SESSION[handle_name];

	$question_id = $_SESSION['question_id'];
	$id = isset($_POST['id']) ? $_POST['id'] : NULL;
	$comment = isset($_POST['comment']) ? $_POST['comment'] : NULL;

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
            .mdl-textfield {
                margin-right: auto;
                margin-left: auto;
                max-width: 900px;
                width: 100%;
            }
            .mdl-textfield_input {
                width: 100%;
            }
            .comment-buttons {
                text-align: center;
            }
            .comment-button {
                margin-right: 8px;
                margin-left: 8px;
            }
            .page-content {
                text-align: center;
            }
            .container {
                max-width: 900px;
                width: 100%;
                margin-right: auto;
                margin-left:auto;
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
                        <a class="mdl-navigation__link" href="sign-out.php">Sign Out</a>
                    </nav>
                </div>
            </header>

            <div class="mdl-layout__drawer">
                <span class="mdl-layout-title">TECH BBS</span>
                <nav class="mdl-navigation">
                    <a class="mdl-navigation__link" href="home.php">HOME</a>
                    <a class="mdl-navigation__link" href="mission1.php">mission1</a>
                    <a class="mdl-navigation__link" href="mission2.php">mission2</a>
                    <a class="mdl-navigation__link" href="mission3.php">mission3</a>
                    <a class="mdl-navigation__link" href="mission4.php">mission4</a>
                    <a class="mdl-navigation__link" href="others.php">Others</a>
                </nav>
            </div>
              
            <!-- ここからmain -->
            <main class="mdl-layout__content">
				<div class="page-content">
                    <div class="container">
                        <h4>WRITE YOUR COMMENT</h4>
                        <form action="answer-register.php" method="post">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
					    	    <textarea class="mdl-textfield__input" type="text" rows="10" name="comment" id="comment" value="<?= $comment ?>" required></textarea>
                                <label class="mdl-textfield__label" for="comment">Comment</label>
                            </div>
                            <input type="hidden" name="question_id" value="<?= $question_id ?>">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <div class="comment-buttons">
                                <!-- 登録用ボタン -->
					    		<button onClick="history.back()" class="comment-button mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect">
                                    Back
                                </button>
					    	    <button type="submit" class="comment-button mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">
                                    Submit
                                </button>
                            </div>
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
