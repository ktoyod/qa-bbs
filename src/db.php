<?php
    function db_connect() {
        /* DB接続用変数 */
		$dsn = getenv('DB_NAME');
        $user = getenv('DB_USER');
	    $password = getenv('DB_PASSWORD');

		try {
			$pdo = new PDO($dsn, $user, $password);
			return $pdo;
		} catch (PDOException $e) {
			print('Error: '.$e->getMessage());
			die();
		}
	}
?>
