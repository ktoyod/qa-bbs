<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>create or delete table</title>
    </head>
    <body>
        <h2>answerテーブル作成/削除</h2>
        <form action="" method="POST">
			<button name="sub" type="submit" value="CREATE">CREATE</button><br>
			<button name="sub" type="submit" value="DELETE">DELETE</button><br>
        </form>
        <hr>           

        <?php
            /* DB関連変数 & 接続 */
            $dsn = getenv('DB_NAME');
            $user = getenv('DB_USER');
	        $password = getenv('DB_PASSWORD');
        	$pdo = new PDO($dsn, $user, $password);
        
			/* 押されたボタンによって処理 */
			if (isset($_POST["sub"])) {
				if ($_POST["sub"] === "CREATE") {
	                $sql = "
                        CREATE TABLE answer (
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        comment TEXT NOT NULL,
                        good INT DEFAULT 0 NOT NULL,
                        bad INT DEFAULT 0 NOT NULL,
                        user_id INT NOT NULL,
                        handle_name VARCHAR(128) NOT NULL,
                        question_id INT NOT NULL,
                        date DATETIME NOT NULL
                        )ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
	                	";
	                $stmt = $pdo -> query($sql);
					$stmt -> closeCursor();
				} else if ($_POST["sub"] === "DELETE") {
        	        $sql = "DROP TABLE answer";
        	        $stmt = $pdo -> query($sql);
					$stmt -> closeCursor();
				}
			}

			/* テーブル確認 */
			echo "<h2>テーブル名</h2>";
			$sql_show = "SHOW TABLES";
			$result = $pdo -> query($sql_show);
			foreach ($result as $row) {
				echo $row[0]."<br>";
			}
			$result -> closeCursor();
			echo "<hr>";

			/* テーブルの中身確認 */
			echo "<h2>answerテーブル</h2>";
	        $sql = 'SELECT * FROM answer';
	        $result = $pdo -> query($sql);
			echo "<table border='1'>";
			echo "<tr>";
			echo "<th align='left'>id</th>";
			echo "<th align='left'>comment</th>";
			echo "<th align='left'>good</th>";
			echo "<th align='left'>bad</th>";
			echo "<th align='left'>user_id</th>";
			echo "<th align='left'>handle_name</th>";
			echo "<th align='left'>question_id</th>";
			echo "<th align='left'>date</th>";
			echo "</tr>";
	        foreach ($result as $row) {
			    echo "<tr>";
	        	echo "<td>".$row['id']."</td>";
	        	echo "<td>".$row['comment']."</td>";
	        	echo "<td>".$row['good']."</td>";
	        	echo "<td>".$row['bad']."</td>";
	        	echo "<td>".$row['user_id']."</td>";
	        	echo "<td>".$row['handle_name']."</td>";
	        	echo "<td>".$row['question_id']."</td>";
	        	echo "<td>".$row['date']."</td>";
			    echo "</tr>";
	        }
			echo "</table>";
			$result -> closeCursor();
        ?>
    </body>
</html>
