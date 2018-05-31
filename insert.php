
<?php
header("Content-type: text/html; charset=utf-8");

#データベースへの接続
$db=mysqli_connect('localhost','root','root','bbs') or
die(mysqli_error($db));

#文字コードの設定
mysqli_set_charset($db,'utf-8');

$write_time=date("Y/m/j/ H:i:s");

#テーブルの作成
mysqli_query($db,"INSERT INTO thread_list SET thread_title='test2',registration_time='{$write_time}',last_modified_time='{$write_time}';");

mysqli_query($db,"CREATE TABLE test2 (id char(10),write_time char(20),content text);");


for($i=1;$i<=500;$i++){
	mysqli_query($db,"INSERT INTO test2 SET id='q1234567', write_time='{$write_time}', content='{$i}コメ';");
}
?>
