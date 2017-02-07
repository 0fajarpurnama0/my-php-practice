<?php
$split = 5;
$myfile = fopen("test.zip", "rw") or die("Unable to open file!");
$stream = fread($myfile,filesize("test.zip"));
for ($i = 0; $i < $split; $i++){
  $myfilecopy[$i] = fopen("testsplit$i.zip", "w") or die("Unable to open file!");
$myfilecopy[1] = fopen("testsplit1.zip", "w") or die("Unable to open file!");
chmod("testsplit$i.zip", 0777);
chmod("testsplit1.zip", 0777);
$splitted_stream = str_split($stream, strlen($stream)/2);
fwrite($myfilecopy[0], $splitted_stream[0]);
fwrite($myfilecopy[1], $splitted_stream[1]);

$myfilemerge = fopen("testmerge.zip", "w") or die("Unable to open file!");
chmod("testmerge.zip", 0777);
$stream = fread($myfilecopy[0],filesize("testsplit1.zip"));
fwrite($myfilemerge, $stream);
$stream = fread($myfilecopy[1],filesize("testsplit2.zip"));
fwrite($myfilemerge, $stream);
fclose($myfile);
fclose($myfilecopy[0]);
fclose($myfilecopy[1]);
?>
