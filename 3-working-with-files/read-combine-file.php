<?php
$myfilemerge = fopen("testmerge.zip", "w") or die("Unable to open file!");
chmod("testmerge.zip", 0777);
$myfilecopy[0] = fopen("testsplit1.zip", "rw") or die("Unable to open file!");
$stream = fread($myfilecopy[0],filesize("testsplit1.zip"));
fwrite($myfilemerge, $stream);
$myfilecopy[1] = fopen("testsplit2.zip", "rw") or die("Unable to open file!");
$stream = fread($myfilecopy[1],filesize("testsplit2.zip"));
fwrite($myfilemerge, $stream);
fclose($myfile);
fclose($myfilecopy[0]);
fclose($myfilecopy[1]);
?>
