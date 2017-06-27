<?php
#cookie情報の削除
setcookie('id','',time()-60*60*24*14);

header('Location: login.php');
exit();
?>
