<html>
<head>
<title>File rdiff synchronization</title>
</head>
<body>

<form enctype="multipart/form-data" action="upload.php" method="POST">
    Send this file: <input name="userfile" type="file" />
    <input type="submit" value="Send File" />
</form>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
<input type="submit">
</form>

<?php 
 if ($_SERVER["REQUEST_METHOD"] == "POST"){
 exec('rdiff signature backup.mbz backup.mbz.sig');
 $target_url = 'http://192.168.56.2/sync/rdiff_make_patch.php';
 $file_name_with_full_path = realpath('./backup.mbz.sig');
 $post = array('extra_info' => '123456','file_contents'=>new \CURLFile($file_name_with_full_path));
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL,$target_url);
 curl_setopt($ch, CURLOPT_POST,1);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
 $result=curl_exec ($ch);
 curl_close ($ch);
 echo $result;
}
?>
