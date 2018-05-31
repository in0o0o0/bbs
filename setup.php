<?php
header("Content-type: text/html; charset=utf-8");

#データベースへの接続
$db=mysqli_connect('localhost','root','root','bbs') or
die(mysqli_error($db));

#文字コードの設定
mysqli_set_charset($db,'utf-8');

mysqli_query($db,"DROP TABLE user_data");

mysqli_query($db,"DROP TABLE thread_list");

#テーブルを作る
mysqli_query($db,"CREATE TABLE user_data(id CHAR(10),nickname CHAR(16),password CHAR(128),registration_date CHAR(11),gender INT,age INT, self_introduction text,web_url text,img_url text)");
mysqli_query($db,"CREATE TABLE thread_list (thread_title text,registration_time INT ,last_modified_time INT)");
?>
