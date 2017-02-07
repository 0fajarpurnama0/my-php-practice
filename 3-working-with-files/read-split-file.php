<?php
$myfile = fopen("test.zip", "rw") or die("Unable to open file!");
$stream = fread($myfile,filesize("test.zip"));
$myfilecopy[0] = fopen("testsplit1.zip", "w") or die("Unable to open file!");
$myfilecopy[1] = fopen("testsplit2.zip", "w") or die("Unable to open file!");
chmod("testsplit1.zip", 0777);
chmod("testsplit2.zip", 0777);
$splitted_stream = str_split($stream, strlen($stream)/2);
fwrite($myfilecopy[0], $splitted_stream[0]);
fwrite($myfilecopy[1], $splitted_stream[1]);
fclose($myfile);
?>
