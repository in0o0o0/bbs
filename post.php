<?php

#ログインしていない
if(!isset($_COOKIE['id'] )){
	header('Location: login.php');
	exit();
}
	#データベースへの接続	
	$db=mysqli_connect('localhost','root','root','bbs') or
	die(mysqli_error($db));
	
	#文字コードの設定
	mysqli_set_charset($db,'utf-8');

	date_default_timezone_set('Asia/Tokyo');
	
	$id=$_COOKIE["id"];
	$write_time=date("Y/m/j/ H:i:s");
	$content=htmlspecialchars($_POST["content"]);
	$content=str_replace(array("\r\n", "\r", "\n"),"<br>",$content);
	$update_time=time();

	#データの挿入
	mysqli_query($db,"INSERT INTO {$_POST['thread']} SET id='{$id}', write_time='{$write_time}', content='{$content}';");

	#thread_listの最終更新時間の更新
	mysqli_query($db,"UPDATE thread_list SET last_modified_time='{$update_time}' WHERE thread_title='{$_POST["thread"]}';");
	#echo "UPDATE thread_list SET last_modified_time='{$update_time} WHERE thread_title='{$_POST["thread"]}';";
	
?>

<html>
<head>
	<title>投稿を受け付けました</title>
	<meta charset="utf-8">
</head>
<body bgcolor="#efefef">
	<header>
		<h1>投稿内容</h1>
	</header>
<?php
	
	echo "投稿日時:<time>".$write_time."</time><br>"; 
	echo $content;
?>

	<hr>
	<p>
		<a href="index.php" target="_self">掲示板に戻る</a><br>
	</p>
	
</body>
</html>
