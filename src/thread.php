<?php
    session_start();
    
    header("Content-type: text/html; charset=utf-8");

	// ログイン状態のチェック
	if (!isset($_SESSION['handle_name'])) {
		header("Location: sign-in.php");
		exit();
	}

	$handle_name = $_SESSION[handle_name];
	$user_id = $_SESSION['user_id'];

	$question_id = isset($_POST['question_id']) ? $_POST['question_id'] : $_SESSION['question_id'];
	$_SESSION['question_id'] = $question_id;

	// DB接続
	require_once("db.php");

	// エラーメッセージの初期化
	$errors = array();

	// 質問
	try {
		// 例外処理を投げるようにする
	    $pdo = db_connect();
		$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$question_statement = $pdo -> prepare("SELECT * FROM question WHERE id=(:question_id)");
		$question_statement -> bindValue(':question_id', $question_id, PDO::PARAM_INT);
		$question_statement -> execute();
		$thread_question = $question_statement -> fetch();

		$pdo = null;

	} catch (PDOException $e) {
		print('Error:'.$e -> getMessage());
		die();
	}

	// 質問に対するコメント
	try {
		// 例外処理を投げるようにする
	    $pdo = db_connect();
		$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$thread_comments = $pdo -> prepare("SELECT * FROM answer WHERE question_id=(:question_id) ORDER BY id ASC");
		$thread_comments -> bindValue(':question_id', $question_id, PDO::PARAM_INT);
		$thread_comments -> execute();

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
                margin-bottom: 32px;
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
            .comment-card {
                margin-right: auto;
                margin-left: auto;
                margin-bottom: 16px;
                min-height: 32px;
                height: 100%;
            }
            .comment-card.mdl-card {
                max-width: 900px;
                width: 100%;
            }
            .comment-card > .mdl-card__title {
                height: 0px;
                color: #646464;
            }
            .comment-card > .mdl-card_subtitle-text {
                text-align: right;
            }
			.comment {
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
			.write-comment {
                margin: 16px;
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
                    <h4>Question</h4>
                    <div class="question">
					    <div class="question-card mdl-card mdl-shadow--2dp">
                            <div class="mdl-card__title">
					    	    <h2 class="mdl-card__title-text"><?= $thread_question['title'] ?></h2>
					    	    <h4 class="mdl-card__subtitle-text"><?= '@'.$thread_question['mission'] ?></h4>
                                <i class="mdl-card_subtitle-text material-icons">perm_identity</i>
					    	    <h4 class="mdl-card__subtitle-text"><?= $thread_question['handle_name'] ?></h4>
                            </div>
                            <div class="mdl-card__supporting-text">
                                <?= $thread_question['content'] ?>
                            </div>
					        <?php if ($thread_question['user_id'] == $user_id): ?>
								<div class="mdl-card__menu">
                                    <form action="question-write.php" method="POST">
                                        <input type="hidden" name="question_id" value="<?= $question_id ?>"
                                        <input type="hidden" name="title" value="<?= $thread_question['title'] ?>">
                                        <input type="hidden" name="mission" value="<?= $thread_question['mission'] ?>">
                                        <input type="hidden" name="content" value="<?= $thread_question['content'] ?>">
                                        <button type="submit" class="mdl-button mdl-js-button mdl-js-ripple-effect">
                                            <i class="material-icons">edit</i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
	                
				    <h4>Comments</h4>
					<?php if ($thread_comments): ?>
					    <?php foreach ($thread_comments as $thread_comment): ?>
					        <div class="comment">
					            <div class="comment-card mdl-card mdl-shadow--2dp comment-card">
                                    <div class="mdl-card__supporting-text">
					        		    <?= $thread_comment['comment'] ?>
                                        <i class="mdl-card_subtitle-text material-icons">perm_identity</i><?= $thread_comment['handle_name'] ?>
                                    </div>
					                <?php if ($thread_comment['user_id'] == $user_id): ?>
                                        <div class="mdl-card__menu">
                                            <form action="answer-write.php" method="POST">
                                                <input type="hidden" name="question_id" value="<?= $question_id ?>">
                                                <input type="hidden" name="id" value="<?= $thread_comment['id'] ?>">
                                                <input type="hidden" name="comment" value="<?= $thread_comment['comment'] ?>">
                                                <button type="submit" class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect">
                                                    <i class="material-icons">edit</i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
	                <?php else: ?>
						No Comment
                    <?php endif; ?>
                    <div class="write-comment">
                        <form method="post" action="answer-write.php">
                            <input type="hidden" name="question_id" value="<?= $question_id ?>">
                            <button type="submit" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                                WRITE YOUR COMMENT
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
