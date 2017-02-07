<?php
$myfile = fopen("test.zip", "rw") or die("Unable to open file!");
$stream = fread($myfile,filesize("test.zip"));
$myfilecopy = fopen("testcopy.zip", "w") or die("Unable to open file!");
chmod("testcopy.zip", 0777);
fwrite($myfilecopy, $stream);
fclose($myfile);
?>
