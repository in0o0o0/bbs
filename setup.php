<?php
try {
  $dbh = new PDO("mysql:host=localhost;","root", "root");
  $dbh->exec("CREATE DATABASE bbs;");

  $dbh->query("use bbs");

  $user_data__sql = "CREATE TABLE user_data(id CHAR(10),nickname CHAR(16),password CHAR(128),registration_date CHAR(11),gender INT,age INT, self_introduction text,web_url text,img_url text)";
  $thread_list_sql = "CREATE TABLE auth_thread_list (thread_title text,registration_time INT ,last_modified_time INT)";

  $dbh->exec($user_data__sql);
  $dbh->exec($thread_list_sql);

  $dbh = null;
} catch (PDOException $e) {
  print "エラー!: " . $e->getMessage() . "<br/>";
  die();
}
 ?>
