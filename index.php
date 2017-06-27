<?php
// ログインしていない
if(!isset($_COOKIE['id'] )){
	header('Location: login.php');
	exit();
}
?>
<html>
<head>
	<title>掲示板</title>
	<meta charset = "utf-8">
	<style type="text/css">
	body {background-color:#fff}
	</style>
	<link rel="stylesheet" href="./css/body.css" type="text/css" media="all">
</head>
<body>
	<header>
		<Div Align="center"><h1>掲示板 </h1></div>
	</header>
	<Div Align="right">
	ようこそ<strong><?php echo $_COOKIE['name'];?></strong>さん&nbsp;&nbsp;&nbsp;&nbsp;<br>
	<a href="edit.php"  style="text-decoration: none">  登録内容変更</a>・
		<a href="logout.php"  style="text-decoration: none"> ログアウト</a>&nbsp;&nbsp;&nbsp;&nbsp;</div>
<hr>

<?php
header("Content-Type: text/html; charset=UTF-8");
error_reporting(E_ALL ^ E_NOTICE);
$max=10;//トップページに表示するスレッドの数

#データベースへの接続	
$db=mysqli_connect('localhost','root','root','bbs') or
die(mysqli_error($db));

#文字コードの設定
mysqli_set_charset($db,'utf-8');

#スレッド一覧を表示するための枠--ここから--
echo '<table border="1" cellspacing="11" cellpadding="2" width="80%" bgcolor="#CCFFCC" align="center"><tr><td>';
 

#作られているthreadの数を調べる
$results=mysqli_fetch_assoc(mysqli_query($db,"SELECT COUNT(*) AS num FROM thread_list;"));
$number_of_thread=$results['num'];

#threadが存在していない時
if($number_of_thread==0){
	echo "スレッドが存在しません";
	echo  " </td> </tr></table><br>";
	#スレッド一覧を表示するための枠--ここまで--

#threadが存在している時
}else{
	$thread_data=array();

	#threadの名前を最終更新時が新しいものから最大10個取り出す
	$records=mysqli_query($db,"SELECT thread_title FROM thread_list ORDER BY  last_modified_time DESC limit 10;");

	$cnt=0;

	while ($record = mysqli_fetch_assoc($records)){
		$each_thread_result=mysqli_fetch_assoc(mysqli_query($db,"SELECT COUNT(*) AS num FROM {$record["thread_title"]};"));
		
		#リンクの作成「1:test(5)」のような形式
		echo "<a href = thread.php?thread={$record['thread_title']}&size=l50>".($cnt+1)."</a>:<a href= '#{$cnt}'>{$record['thread_title']}({$each_thread_result['num']})</a> / ";

		#データを記録
		array_push($thread_data,array('thread_title'=>$record['thread_title'],'number'=>$each_thread_result['num']));#,$each_thread_result['num'];
		$cnt+=1;
	}

	echo  " </td> </tr></table><br>";
	#スレッド一覧を表示するための枠--ここまで--

	echo '<Div Align="center">';	

	#個々のthreadの内容表示 
	$cnt=0;#何番目のthreadか
	foreach($thread_data as $data){
		echo '<table border="1" cellspacing="11" cellpadding="2" bgcolor="#efefef" width="80%"  align="center"><tr><td>';
		echo "<a name={$cnt}></a>";//着地地点の設置
		echo "<IFRAME id={$cnt} onLoad='adjust_frame_css(this.id)'  width='100%' scrolling='no' style src='thread.php?thread={$data['thread_title']}&flag=1&size=l10'    frameborder='0'></IFRAME></td></tr></table><br>";
		echo  " </td> </tr></table><br></div>";

		$cnt+=1;
	}
}
?> 



<form method="POST" action="./create_thread.php">
			<input type="submit" value="新規スレッド作成">
		</form>

<!--iframeのサイズを調整する-->
<script type="text/javascript">
function adjust_frame_css(F){
  if(document.getElementById(F)) {
	var myF = document.getElementById(F);
	var myC = myF.contentWindow.document.documentElement;
	var myH = 100;
    if(document.all) {
      myH  = myC.scrollHeight;
    } else {
      myH = myC.offsetHeight;
    }
    myF.style.height = myH+"px";
  }
}
</script>
<!-- フッタ開始 -->
<div id="footer">
&nbsp;
</div>
<!-- フッタ終了 -->
</body>
</html> 
