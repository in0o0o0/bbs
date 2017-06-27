<?php
error_reporting(E_ALL ^ E_NOTICE);

#パラメータにニックネームがセットされている
if(isset($_GET['name'])){
	$name = $_GET['name'];

	#データベースへの接続	
	$db=mysqli_connect('localhost','root','root','bbs') or
	die(mysqli_error($db));

	#文字コードの設定
	mysqli_set_charset($db,'utf-8');

	#個人データの取り出し
	$records=mysqli_fetch_assoc(mysqli_query($db,"SELECT * FROM user_data WHERE nickname='{$name}';"));

	echo $records['sex'];

	$data['time']=$records['registration_date'];
	$data['name']=$records['nickname'];

	$data['department']=$content[4];
	$data['content']=$records['self_introduction'];
	$data['url']=$records['web_url'];
	$data['img']=$records['img_url'];


	switch($records['gender']){
		case 0: 
			$data['sex']="男性";break;
		case 1: 
			$data['sex']="女性";break;
		default:
			$data['sex']="非公開";break;
	}

	switch($records['age']){
		case 18:
			$data['age']="18歳"; break;
		case 19:
			$data['age']="19歳"; break;
		case 20:
			$data['age']="20歳"; break;
		case 21:
			$data['age']="21歳"; break;
		case 22:
			$data['age']="22歳"; break;
		case 23:
			$data['age']="23歳以上"; break;
		default:
		   $data['age']="非公開"; break;
	}
#パラメータにニックネームがセットされていない
}else{
	$error=true;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<link rel="stylesheet" type="text/css" href="./css/body.css">
<link rel="stylesheet" type="text/css" href="./css/table.css">
<title>個人ページ</title>

</head>
<body>

<!-- コンテナ開始 -->
<div id="container">

<?php if(isset($error)):?>
<div id="header">
<h1>指定されたページは存在しません</h1>
</div>
<?php else :?>
<!-- ヘッダ開始 -->
<div id="header">
<h1><?php echo $_GET['name'];?>さんのページ</h1>
</div>
<!-- ヘッダ終了 -->

<!-- ナビゲーション開始 -->
<div id="nav">
<div id="profileImg">
<img src="<?php if(isset($data['img']) && $data['img']!="") echo $data['img']; else echo 'sample.png';?>" alt="プロフィール画像" width="150" height="150">

</div>
</div>
<!-- ナビゲーション終了 -->

<!-- メインカラム開始 -->
<div id="content2">
<form method="POST" action="">
<table  align="center"  class="company">
	
	<tr><td class="title">参加日</td><td class="content"><?php echo $data['time'];?></td></tr>
	<tr><td class="title">性別</td><td class="content"><?php echo $data['sex'];?></td></tr>
	<tr><td class="title">年齢</td><td class="content"><?php echo $data['age'];?></td></tr>
	<tr><td class="title">自己紹介</td ><td class="content"><?php echo $data['content'];?></td></tr>
	<tr><td class="title">サイト</td><td class="content"><?php echo "<a href =".$data['url']." TARGET='_blank'>".$data['url']."</a>";?></td></tr>
</table>

</form>

</div>
<!-- メインカラム終了 -->

<!-- フッタ開始 -->
<div id="footer">
<Div Align="right"><a class="white" href="./index.php" target="_self">掲示板に戻る</a></div>
</div>
<!-- フッタ終了 -->

<?php endif ;?>
</div>
<!-- コンテナ終了 -->

</body>
</html>