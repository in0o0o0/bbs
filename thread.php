<html>
<head>
	<title>掲示板</title>
	<meta charset = "utf-8">
	<body bgcolor="#efefef">
</head>
<body>
<?php
header("Content-type: text/html; charset=utf-8");
 
#データベースへの接続	
$db=mysqli_connect('localhost','root','root','bbs') or
die(mysqli_error($db));

#文字コードの設定
mysqli_set_charset($db,'utf-8');

#表示するスレッドを取り出す
$thread_name=$_GET['thread'];

 #パラメータが設定されていない
if( !array_key_exists( 'thread',$_GET ) ){
    
   echo "threadパラメータが指定されてません";

#パラメータが指定されている
}else{
	#スレッドが存在するか調べる
	$result=mysqli_fetch_assoc(mysqli_query($db,"SELECT COUNT(*) AS num FROM thread_list WHERE thread_title='{$thread_name}';"));

	#スレッドが存在しないとき
	if($result['num']==0){
		echo '<table width="100%" border="1" cellspacing="0" cellpadding="10"><tr><td><b>ERROR!!<br><br>指定されたスレッドは存在しません</b></td></tr></table>';
	
	#スレッドが存在する場合
	}else{
		#sizeが正しい形式か調べる
		if((preg_match('/^l[0-9]*$/',$_GET['size']) ||preg_match(' /^[0-9]*-[0-9]*$/',$_GET['size']) ||preg_match(' /^[0-9]+$/',$_GET['size'])) || !isset($_GET['size'])){

			$data=array(); #書き込みをいれる
			$count=0;      #書き込み数をいれる
		
			#スレッドからデータを取り出す
			$records=mysqli_query($db,"SELECT * FROM {$_GET['thread']};");

			while ($record = mysqli_fetch_assoc($records)){
				$tmp=trim($record['id']);
				$name=mysqli_fetch_assoc(mysqli_query($db,"SELECT * FROM user_data WHERE id='{$tmp}';"));
				array_push($data,array('id'=>$record['id'],'nickname'=>$name['nickname'],'write_time'=>$record['write_time'],'content'=>$record['content']));
			}
			
			
			#書き込みの数を調べる
			$cnt = count($data);

			#表示するレスの数がパラメータで指定されているか
			if(isset($_GET['size']))
				$size=$_GET['size'];

			#一つ目を別に表示するかどうか（レスが多いとき用）
			$one=false;
			$start=0;
			$end=0;

			#すべて表示するとき
			if(!isset($_GET['size']) || preg_match('/^-$/',$size)){
				$end=$cnt;
			
			#最新○○個パターン
			}else if(substr($size,0,1)=='l'){#頭文字がlになっている
				$x=substr($size,1);
				if($x>=$cnt)	//要求以下のレスしかない（すべて表示）
					$end=$cnt; 
				else{
					$one=true;
					$start=$cnt-$x;
					$end=$cnt;
				}
			#1-100パターン
			}else if(preg_match(' /^[0-9]+-[0-9]+$/',$size)){
				$count=explode("-",$size);
				#小、大の順にする
				if($count[0]>$count[1]){
					$tmp=$count[0];
					$count[0]=$count[1];
					$count[1]=$tmp;
				}	
				#5000-3000とかの時（両方範囲に入っていない）
				if($count[0]>$cnt){
					$start=0;
					$end=1;		
				}else{
					if($count[0]<=1)#0-?のとき
						$start=0;
					else{
						$one=true;
						$start=$count[0]-1;
					}
						
					if($count[1]>=$cnt)
						$end=$cnt;
					else
						$end=$count[1];
				}
			
			#-200パターン
			}else if(preg_match('/^-[0-9]+$/',$size)){
				$x=substr($size,1);
				if($x>=$cnt || $x==0){
					$start=0;
					$end=$cnt;
				}else {
					$start=0;
					$end=$x;
				}
				
			#200-パターン
			}else if(preg_match('/^[0-9]+-$/',$size)){
				$x=substr($size,0,-1);
				if($x>=$cnt){#スタート要求が最後よりも大きい
					$one=true;
					$start=$cnt-1;
					$end=$cnt;
				}else{
					$one=true;
					if($x==0)
						$start=0;
					else
						$start=$x-1;
					$end=$cnt;
				}
			
			#200パターン
			}else if(preg_match('/^[0-9]+$/',$size)){
				if($size>$cnt)
					$error=detect;#存在しないレス番号を指定された
				else{
					$start=$size-1;
					$end=$size;
				}
			}
			
			#以下前100と次100を作る処理
			
			#前100を決める
			if($start-1<=0){
				$bs=1;
				$be=1;	
			}else if($start-100<=0){
				$bs=1;
				$be=$start;
			}else{
				$bs=$start-99;
				$be=$start;		
			}
			
			#次100を決める
			if($end+1>=$cnt){
				$ns=$end;
				$ne=$end;
			}else if($end+100>=$cnt){
				$ns=$end+1;
				$ne=$cnt;
			}else{
				$ns=$end+1;
				$ne=$end+100;
			}

			#エラーがないなら表示処理に入る
			if(!isset($error)){
				#タイトルを表示
				echo "<h1 class='thread-title'>".$thread_name."</h1>";
				
				#idとニックネームの関連を調べる
				$records=mysqli_query($db,"SELECT * FROM user_data;");
				
				$id_to_nickname = array();#idがキーでvaludeがニックネーム
				
				#連想配列に入れる
				while ($record = mysqli_fetch_assoc($records)){
					$id_to_nickname["{$record['id']}"] = "{$record['nickname']}";
				}

				#一つ目を別に表示しないといけない時
				if($one){	
					echo "<p>".sprintf('%03d',1).": <strong><a href='personal.php?name={$data[0]["nickname"]}' target='_top'>{$data[0]["nickname"]}</a></strong>: ";
					echo $data[0]['write_time']."<br><dd>".$data[0]['content']."</dd></p>\n";
				}

				#最初から順番に最後まで表示すればいいとき
				for($i=$start;$i<$end;$i++){
					echo "<p>".sprintf('%03d',$i+1).": <strong><a href='personal.php?name={$data[$i]["nickname"]}' target='_top'>{$data[$i]['nickname']}</a></strong>：{$data[$i]['write_time']}<br><dd>{$data[$i]['content']}</dd></p>\n";
				}	
			}#if(!isset($error))	
		}else{#sizeが正しい形式か調べる
			$error=detect;
		}
?>
<?php if(!isset($error)): ?>
<p><form method="POST" action="./post.php" target="_top">
<input type="submit" value="書き込む"><input type="hidden" name="name" value="<?php echo $_COOKIE['id'];?>"><br>
<textarea name="content" rows="5" cols="50"></textarea><br>
<input type="hidden" name="thread" value="<?php echo $_GET['thread'];?>">
</form>
</p><hr>
<?php elseif(isset($error)) : ?>
	<table width="100%" border="1" cellspacing="0" cellpadding="10"><tr><td><b>ERROR!!<br><br>レス番号が正しくありません</b></td></tr></table>
<?php endif; 
		
		if(!isset($error)){
			if(isset( $_GET["flag"])){//トップページに表示する時
				echo '<a href="thread.php?thread='.$_GET["thread"].'" target="_top"> 全部読む</a> &nbsp;'; 
				echo '<a href="thread.php?thread='. $_GET["thread"].'&size=l50"  target="_top">最新50 </a> &nbsp;';
				echo '<a href="thread.php?thread='.$_GET["thread"].'&size=1-100"  target="_top">1-100 </a> &nbsp;';
			}else{//別ウィンドウでスレッドを表示する時
				echo '<a href="index.php" target="_top">■掲示板に戻る■</a> &nbsp;'; 
				echo '<a href="thread.php?thread='. $_GET["thread"].'" target="_top">全部</a> &nbsp;';
				echo '<a href="thread.php?thread='.$_GET["thread"].'&size='.$bs.'-'.$be.'"  target="_top">前100:</a> &nbsp;';
				echo '<a href="thread.php?thread='.$_GET["thread"].'&size='.$ns.'-'.$ne.'"  target="_top">次100:</a> &nbsp;';
				echo '<a href="thread.php?thread='.$_GET["thread"].'&size=l50"  target="_top">最新50</a>';
			}
		}
	}#スレッドが存在する場合
}#パラメータが指定されている
?>
</div>
</body>
</html> 

