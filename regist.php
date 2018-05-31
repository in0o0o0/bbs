<?php

$error = array(
	"id" => "",
	"password" => "",
	"nickname" => ""
);

if (!empty($_POST)) {

	#データベースへの接続
	$db=mysqli_connect('localhost','root','root','bbs') or
	die(mysqli_error($db));

	#文字コードの設定
	mysqli_set_charset($db,'utf-8');
	$records=mysqli_query($db,"SELECT * FROM user_data;");


	//idのチェック
	if($_POST['id']==''){
		$error['id'] = 'blank';
	}else{
		$id=$_POST['id'];

		//IDが英字1文字＋数字7文字
		if(!preg_match('/^[a-z]{1}[0-9]{7}$/',$id)){
			$error['id']="failed";

		}else{//IDがかぶってないか調べる
			while ($data = mysqli_fetch_assoc($records)){
				if(strcmp($data['id'],$id)==0){
					$error['id'] = 'registered';
					break;
				}
			}
		}
	}

	//パスワードのチェック
	if($_POST['password'] == ''){
		$error['password']= 'blank';
	}else{
		$pw=$_POST['password'];
		//パスワードが８文字以上の半角文字かどうか
		if(!preg_match('/[!-~]{8}/',$pw))
			$error['password']="failed";
	}

	//ニックネームのチェック
	if($_POST['nickname'] == ''){
		$error['nickname']= 'blank';
	}else{
		$name=$_POST['nickname'];

		$records=mysqli_query($db,"SELECT * FROM user_data;");
		//ニックネームがかぶってないか調べる
		while ($data = mysqli_fetch_assoc($records)){
			if(strcmp($data['nickname'],$name)==0){
				$error['nickname'] = 'registered';
				break;
			}
		}
	}

	//登録成功
	if($error["id"] == "" and $error["password"] == "" and $error["nickname"] == ""){
		//登録日時を記録する
		date_default_timezone_set('Asia/Tokyo');

		$time=date("Y/m/j");

		$hashed_pw=hash("SHA512",$pw);

		#データの挿入
		mysqli_query($db,"INSERT INTO user_data SET id='{$id}', password='{$hashed_pw}', registration_date='{$time}', nickname='{$name}',img_url='./picture/sample.png';");

		setcookie('id',$id, time()+60*60*24*14);
		setcookie('name',$name, time()+60*60*24*14);
		header('Location:index.php');
	}
}else{
	$error['id'] = '';
	$error['password'] = '';
	$error['nickname'] = '';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="./css/login_regist.css" />
<title>登録する</title>
</head>
<body>
<div id="login">

<br><h1>register</h1>
<form action="" method="post">
<?php if($error['id'] == 'registered'):?>
<p class="error">* すでに登録されているIDです</p>
<?php elseif($error['id'] == 'failed'):?>
<p class="error">* IDは半角英字1文字＋数字7文字でご記入下さい</p>
<?php	elseif($error['id'] == 'blank'):?>
<p class="error">* IDを記入してください</p>
<?php endif;?>
<input type="text" name="id" size="35" maxlength="255"  placeholder="ID（半角英字1文字＋数字7文字）" value="<?php if (isset($_POST['id'])){ echo htmlspecialchars($_POST['id']); }?>"/>

<?php if($error['password'] == 'failed'):?>
<p class="error">* パスワードは半角文字8文字以上でご記入ください</p>
<?php elseif($error['password'] == 'blank'):?>
<p class="error">* パスワードを記入してください</p>
<?php endif;?>
<dd><input type="password" name="password" size="35" maxlength="255"  placeholder="Password（半角英数字8文字以上）" value="<?php if (isset($_POST['password'])){ echo htmlspecialchars($_POST['password']); }?>" /></dd>

<?php if($error['nickname'] == 'registered'):?>
<p class="error">* すでに登録されているニックネームです</p>
<?php elseif($error['nickname'] == 'blank'):?>
<p class="error">* ニックネームを記入してください</p>
<?php endif;?>
<dd><input type="text" name="nickname" size="35" maxlength="255"  placeholder="Nickname" value="<?php if (isset($_POST['nickname'])){ echo htmlspecialchars($_POST['nickname']);} ?>" /></dd><br>

<div><input type="submit" value="新規登録する" /></div>
</form>
</div>

</body>
</html>
