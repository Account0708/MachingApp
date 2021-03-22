<?php
try {
    $db = new PDO('mysql:dbname=maching_app_db;host=localhost;port=8889;charset=utf8', 'root', 'root');
} catch (PDOException $e) {
    print( 'DB接続エラー: ' . $e->getMessage());
}
?>