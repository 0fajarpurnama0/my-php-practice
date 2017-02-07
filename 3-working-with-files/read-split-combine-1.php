<?php
$split = 5;
$myfile = fopen("backup.mbz.delta", "rw") or die("Unable to open file!");
$stream = fread($myfile,filesize("backup.mbz.delta"));
echo $splitter = (strlen($stream)/$split).'<br>';

$splitted_stream = str_split($stream, $splitter);

sizeof($splitted_stream); 


while (sizeof($splitted_stream) > $split) {
  echo sizeof($splitted_stream).'<br>';
  $splitter = $splitter + 1;
  $splitted_stream = str_split($stream, $splitter);
}
echo sizeof($splitted_stream);

echo sizeof($splitted_stream);
for ($i = 0; $i < $split; $i++){
  $myfilecopy[$i] = fopen("backup.mbz.delta$i", "w") or die("Unable to open file!");
  chmod("backup.mbz.delta$i", 0777);
  fwrite($myfilecopy[$i], $splitted_stream[$i]);
  fclose($myfilecopy[$i]);
}
fclose($myfile);

$myfilemerge = fopen("testbackup.mbz.delta", "w") or die("Unable to open file!");
chmod("testbackup.mbz.delta", 0777);
for ($i = 0; $i < $split; $i++){
  $myfilecopy[$i] = fopen("backup.mbz.delta$i", "r") or die("Unable to open file!");
  $stream = fread($myfilecopy[$i],filesize("backup.mbz.delta$i"));
  fwrite($myfilemerge, $stream);
  fclose($myfilecopy[$i]);
}
fclose($myfilemerge);
?>
