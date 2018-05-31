<?php
error_reporting(E_ALL ^ E_NOTICE);

if(!isset($_COOKIE['id'] )){
	header('Location: login.php');
	exit();
}

#データベースへの接続
$db=mysqli_connect('localhost','root','root','bbs') or
die(mysqli_error($db));

#文字コードの設定
mysqli_set_charset($db,'utf-8');

if (!empty($_POST)) {

		if ($_POST['name']!= "") {
			$data['name'] = $_POST['name'];
			if(strlen($data['name'])>30){
				$error['name']="length";
			}else{
				#ニックネームがかぶってないか調べる
				$records=mysqli_query($db,"SELECT * FROM user_data WHERE NOT id='{$_COOKIE["id"]}';");

				while ($record = mysqli_fetch_assoc($records)){
					if(strcmp($record['nickname'],$data['name'])==0){
						$error['name'] = 'registered';
						break;
					}
				}
			}
		}else{
			$error['name']="blank";
		}

		if (isset($_POST['sex'])) {
			$data['sex'] = $_POST['sex'];
		}

		if (isset($_POST['age'])) {
			$data['age'] = $_POST['age'];
		}


		if (isset($_POST['content'])) {
			$data['content'] = $_POST['content'];
			if(strlen($data['content'])>200)
				$error['content']=true;
		}

		if ($_POST['url']!="") {
			$data['url'] = $_POST['url'];

			if (!preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/',$data['url'] )) {
    			$error['url']=true;
			}
		}

		if(isset($_FILES['image']['name'])){
			$fileName = $_FILES['image']['name'];
			if (!empty($fileName)) {
				$ext = substr($fileName, -3);
				#拡張子の確認
				if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
					$error['image'] = 'type';
				}else{
				#画像をアップロードする
					$image = date('YmdHis') . $_FILES['image']['name'];
					move_uploaded_file($_FILES['image']['tmp_name'], './picture/' . $image);
					$data['image']='./picture/'.$image;
				}
			}else {#ファイルをアップロードが押されたが変更がなかった時は同じものを使う
				$data['image']= $_POST['himg'];
			}
		}
		#変更ボタンが押された時（ファイルに書き込む）
		if(isset($_POST['change'])&& !isset($error)){

			$data['name']=htmlspecialchars($_POST['name']);
			$data['name']=str_replace(array("\r\n", "\r", "\n"),"<br>",$data['name']);
			$data['sex']=$_POST['sex'];
			$data['age']=$_POST['age'];
			$data['content']=htmlspecialchars($_POST['content']);
			$data['content']=str_replace(PHP_EOL,"<br>",$data['content']);
			$data['url']=htmlspecialchars($_POST['url']);
			$data['url']=str_replace(array("\r\n", "\r", "\n"),"<br>",$data['url']);
			$data['img']=htmlspecialchars($_POST['himg']);
			$data['img']=str_replace(array("\r\n", "\r", "\n"),"<br>",$data['img']);

			$_COOKIE['name'] = $data['name'];
			if (strlen($data['content'])==0){
				$data['content']=null;
			}

			if (empty($data['img'])){
				$data['img']=null;
			}

			if (empty($data['content'])){
				$data['content']=null;
			}

		#ニックネームが変わった時cookieを更新
		$records= mysqli_fetch_assoc(mysqli_query($db,"SELECT * FROM user_data WHERE id='{$_COOKIE["id"]}';"));

		if(strcmp($records['nickname'],$data['name'])!=0){
			setcookie('name', $_POST['name'], time()+60*60*24*14);
		}

		#DBのupdateを行う
		mysqli_query($db,"UPDATE user_data SET nickname='{$data["name"]}', gender='{$data["sex"]}', age='{$data["age"]}',self_introduction='{$data["content"]}', web_url='{$data["url"]}', img_url='{$data["img"]}' WHERE id='{$_COOKIE["id"]}';");
		
		#記入内容の確認のために個人ページに飛ぶ
		header("Location: personal.php?name={$data['name']}");
		exit();
	}

}else{#最初はファイルに登録されている情報を表示する

	#データベースへの接続
	$db=mysqli_connect('localhost','root','root','bbs') or
	die(mysqli_error($db));

	$records=mysqli_fetch_assoc(mysqli_query($db,"SELECT * FROM user_data where id='{$_COOKIE["id"]}';"));

	$data['name']=$records['nickname'];
	$data['sex']=$records['gender'];
	$data['age']=$records['age'];
	$data['department']="";
	$data['content']=str_replace("<br>",PHP_EOL,$records['self_introduction']);
	$data['url']=$records['web_url'];
	$data['image']=$records['img_url'];
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<link rel="stylesheet" type="text/css" href="./css/body.css">
<link rel="stylesheet" type="text/css" href="./css/table.css">

<title>編集ページ</title>
</head>
<body>

<!-- コンテナ開始 -->
<div id="container">

<!-- ヘッダ開始 -->
<div id="header">
<h1>編集ページ</h1>
</div>
<!-- ヘッダ終了 -->

<!-- メインカラム開始 -->
<div id="content">
<form method="POST" action="" enctype="multipart/form-data">
<table  align="center"  class="edit">
	<tr>
		<td class="title">ニックネーム（必須）<br><div class="caution">※全角15文字以内</div></td>
		<td class="content">
			<?php if($error['name']=="length") :?>
			<div class="error">*入力文字が多すぎます</div>
			<?php elseif($error['name']=="blank"):?>
			<div class="error">*必須項目です</div>
			<?php elseif($error['name']=="registered"):?>
			<div class="error">*すでに登録されているニックネームです</div>
			<?php endif;?>
			<input name="name" type="text" size="30" value="<?php if(isset($data['name'])) echo $data['name'];?>" id="name"></td>
	</tr>
	<tr>
		<td class="title">性別</td>
		<td class="content">
		<label for="genM"><input type="radio" id="genM" value="0" name="sex" <?php if($data['sex']==0) echo "checked";?> >男性</label>
		<label for="genF"><input type="radio" id="genF" value="1" name="sex" <?php if($data['sex']==1) echo "checked";?>>女性</label>
		<label for="genNone"><input type="radio" id="genNone" value="2" name="sex" <?php if($data['sex']==2) echo "checked";?>>非公開</label>
</td>
	</tr>
	<tr>
		<td class="title">年齢</td>
		<td class="content">
			<select name="age">
				<option value="0" selected>非公開</option>
				<option value="18" <?php if($data['age']==18) echo "selected";?>>18</option>
				<option value="19" <?php if($data['age']==19) echo "selected";?>>19</option>
				<option value="20" <?php if($data['age']==20) echo "selected";?>>20</option>
				<option value="21" <?php if($data['age']==21) echo "selected";?>>21</option>
				<option value="22" <?php if($data['age']==22) echo "selected";?>>22</option>
				<option value="23" <?php if($data['age']==23) echo "selected";?>>23歳以上</option>

			</select>
		</td>
	</tr>

	<tr>
		<td class="title">自己紹介<div class="caution">※全角100文字以内</div></td >
		<td class="content">
			<?php if(isset($error['content'])) :?>
			<div class="error">*入力文字が多すぎます</div>
			<?php endif;?>
			<textarea name="content" cols="40" rows="4" id="input3" value=""><?php if(isset($data['content'])) echo $data['content'];?></textarea>
		</td>
	</tr>

	<tr>
		<td class="title">サイト<div class="caution">(URL)</div></td>
		<td class="content">
		<?php if(isset($error['url'])) :?>
		<div class="error">*URLの形式が間違っています</div>
		<?php endif;?>
		<input name="url" type="text" size="30" value="<?php if(isset($data['url'])) echo $data['url'];?>" id="url">
		</td>
	</tr>
	<tr>
		<td class="title" valign="top">プロフィール画像</td>
		<td>
		<input name="image" type="file" size="20" class="file">
		<input name="imgup" type="submit" value="ファイルアップロード"><br>


		<img src="
			<?php
				if(strlen($data['image'])==0)
					echo "./picture/sample.png";
				else
					echo $data['image'];

			?>"
			alt="プロフィール画像" name="img_profile" border="0"  witdh="150" height="150"><br>
		<?php if(isset($data['image'])) echo ' <input type="hidden" name="himg" value="'.$data["image"].'">';?>
			<ul>
				<li>ファイル形式：JPG、GIF、PNG </li>
				<li>※「ファイルアップロード」ボタンを押すとアップロードされた画像が表示されます。</li>
			</ul>
	</td>
	</tr>
</table>
<Div Align="center"><input name="change" type="submit" value="プロフィールを変更する"></div>
</form><br>

</div>
<!-- メインカラム終了 -->

<!-- フッタ開始 -->
<div id="footer">
<Div Align="right"><a class="white" href="./index.php" target="_self">掲示板に戻る</a></div>
</div>
<!-- フッタ終了 -->


</div>
<!-- コンテナ終了 -->

</body>
</html>
