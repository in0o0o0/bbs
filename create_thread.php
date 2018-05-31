<?php
error_reporting(E_ALL ^ E_NOTICE);



if (!empty($_POST)) {
	#エラー項目の確認
	if ($_POST['title'] == '') {
		$error['title'] = 'blank';
	}

	if ($_POST['content'] == '') {
		$error['content'] = 'blank';
	}

	#すべて記入されている
	if (empty($error)) {

		#データベースへの接続
		$db=mysqli_connect('localhost','root','root','bbs') or
		die(mysqli_error($db));

		#文字コードの設定
		mysqli_set_charset($db,'utf-8');
		date_default_timezone_set('Asia/Tokyo');

		$file_name=$_POST['title'];


		#同じスレッドが存在していないか調べる
		$result=mysqli_fetch_assoc(mysqli_query($db,"SELECT COUNT(*) AS num FROM thread_list WHERE thread_title='{$_POST["title"]}';"));

		#同じスレッドが存在していなかったとき
		if ($result['num']==0){
			$name=htmlspecialchars($_POST["name"]);
		 	$timestamp=time();
		 	$time=date("Y/m/j/ H:i:s");
			$content=htmlspecialchars($_POST["content"]);
			$content=preg_replace('/\n|\r|\r\n/',"<br>",$content);#改行文字の処理

			#スレッドリストへの挿入
			mysqli_query($db,"INSERT INTO thread_list SET thread_title='{$_POST["title"]}',registration_time='{$timestamp}',last_modified_time='{$timestamp}';");

			#テーブルの作成
			mysqli_query($db,"CREATE TABLE {$_POST['title']} (id char(10),write_time char(20),content text);");

			#データの挿入
			mysqli_query($db,"INSERT INTO {$_POST["title"]} SET id=' {$_COOKIE["id"]}',write_time='{$time}', content='{$content}';");

			header("Location: ./thread.php?thread=${file_name}");
		}else{
    		#すで同じスレッドが存在するとき
    		echo('※すでに同じスレッドが存在しています');

  		}
	}
}
?>

<html>
<head>
<title>スレッド作成</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

</head>
<body bgcolor="#efefef">

<table border="1" cellspacing="11" cellpadding="2" width="90%" bgcolor="#CCFFCC" align="center"><tr><td>
<table border="0" width="100%">
 <form method="POST" action="">
  <tr>
   <td nowrap ALIGN="right">タイトル：</TD>
   <td>
    <input type="text" name="title" size="40">
    <input type=submit value="スレッド作成" name="submit">
   </td>
  </tr>
  <tr>
   <td nowrap align="right" valign="top">内容：</td>
   <td>
    <textarea rows="5" cols="60" wrap="OFF" name="content"></textarea>
    <input type="hidden" name="name" value="<?php echo $_COOKIE['id'];?>">
   </td>
  </tr>
 </form>
</table>
</td></tr></table><br>
<a href="./index.php" >掲示板に戻る</a>
</body>
</html>
