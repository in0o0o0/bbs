<?php
#入力された時の処理(cookieで自動入力された時も)
if (!empty($_POST)) {
	#すべて記入されている時
	if ($_POST['id'] != '' && $_POST['password'] != '') {
		
		$id=$_POST['id'];
		$pw=$_POST['password'];
		$flag=false; #パスワードが一致したかどうか	
		
		#データベースへの接続	
		$db=mysqli_connect('localhost','root','root','bbs') or
		die(mysqli_error($db));
	
		#文字コードの設定
		mysqli_set_charset($db,'utf-8');

		$records=mysqli_fetch_assoc(mysqli_query($db,"SELECT * FROM user_data WHERE id='{$id}';"));

		#パスワードの比較	
		if (strcmp($records['password'],hash("SHA512",$pw))==0){
			$flag=true;//idとパスワードの組み合わせが一致した		
		}

		#ログイン成功
		if($flag){
			setcookie('name',$records['nickname'], time()+60*60*24*14);
			setcookie('id', $records['id'], time()+60*60*24*14);
			header('Location: index.php');
		}else{
			$error['login'] = 'failed';
		}
		
	}else {
		if($_POST['id']=='')
			$error['id']='blank';
		if($_POST['password']=='')
			$error['password']='blank';	
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./css/login_regist.css" />
<title>ログインする</title>
</head>
<body>
	<div id="login">

	 <br><h1>Log in</h1>
	<form action="login.php" method="post">
	<input type="text" name="id" size="50" maxlength="255" placeholder="id"  
	value="<?php echo htmlspecialchars($_POST['id']); ?>" />
	
	<?php 
		if ($error['id'] == 'blank')
			echo '<p class="error">*IDを記入してください</p>';
	?>
	
	<input type="password" name="password" size="50" maxlength="255" 
	 placeholder="Password" value="<?php echo htmlspecialchars($_POST['password']); ?>"/>
 
	<?php
		if ($error['password'] == 'blank')
			echo '<p class="error">* パスワードを記入してください</p>';
		if($error['login'] == 'failed')
			echo '<p class="error">* ログインに失敗しました。正しくご記入ください</p>';
	?>
	<br>	
	<div><input type="submit" value="ログインする" /></div><br>
	 <p>&raquo;<a href="regist.php">新規登録はこちら</a></p>
	 </form>
</div>
</body>
</html>
