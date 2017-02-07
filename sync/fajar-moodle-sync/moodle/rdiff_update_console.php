<?php
session_start();
session_unset ();
require('lib_main.php'); 
if (file_exists('main_varset.php')){
  require('main_varset.php');
}
if (file_exists($directory.'/local/fajar-moodle-sync/course_list.php')){
  require($directory.'/local/fajar-moodle-sync/course_list.php');
}
?>

<html>
<head>
<title>Moodle Backup Rdiff Synchronization</title>
<link rel="stylesheet" type="text/css" href="main.css"/>
</head>
<body>

<?php 
$this_directory_owner = posix_getpwuid(fileowner(getcwd()));
switch ($this_directory_owner['name']){
  case "www-data":
    break;
  case "apache":
    break;
  default:
    echo "This directory's owner is nor www-data or apache, it may not work properly. Consider changing ownership (i.e. sudo chown -R www-data:www-data fajar-moodle-sync).";
}
?>

<h1>Moodle Backup Rdiff Synchronizer</h1>
<h3>Based on rdiff: a controlled rsync application.</h3>
<h3>If course doesn't exist, create a blank course manually at your moodle site.</h3>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
<select name="option">
  <?php foreach($my_course as $dir) {?>
    <option value ="<?php echo $dir;?>"><?php echo $dir;?></option>
  <?php } ?>
</select> 
<input type="submit" value="choose"> &nbsp; <a href="settings.php">Settings</a>&nbsp;<a href="../index.html"> Front Page</a>
</form>

<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  $option = $_POST['option'];
  while(current($my_course) != $option){
    next($my_course);
  } 
  $course_id = key($my_course);
  if (!file_exists($option)) {
    mkdir('./'.$option);
  }
  $_SESSION['option'] = $option;
  ?>
  <a href="http://<?php echo $moodle_url.'/backup/backup.php?id='."$course_id"; ?>">Backup the course manually and upload the backup file here!</a> <br>
  <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  <input type="hidden" name="option" value="<?php echo $option;?>">
  <input type="submit" name="cli-backup" value="cli-backup"> or click this button for Moodle cli to do automatically 
  </form><a href="http://<?php echo $moodle_url.'/admin/settings.php?section=backupgeneralsettings';?>">Backup Default Settings</a>
  <br><br>
  <form enctype="multipart/form-data" action="upload.php" method="POST">
  Insert backup (.mbz) file: <input name="userfile" type="file" />
  <input type="hidden" name="option" value="<?php echo $option;?>">
  <input type="submit" value="upload" />
  </form>

  <table border="1" style="width:30%">
    <tr>
      <td>File</td>
    </tr>
    <?php foreach(scandir($option) as $file_name) {?>
    <tr>
        <td><a href="<?php echo $file_name;?>"> <?php echo $file_name . ' ' . filesize($option . '/' . $file_name) . ' bytes' ?></a></td>
    </tr>
    <?php } ?>
  </table>
    
  <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  <input type="hidden" name="option" value="<?php echo $option;?>">
  <input type="submit" name="master" value="master">
  <select name="method">
    <option value="rdiff">rdiff</option>
    <option value="rdiffdir">rdiffdir</option>
  </select>
  <input type="submit" name="update" value="update">
  <input type="submit" name="undo" value="undo">
  <a href="settings.php"> Settings</a>
  </form>
  <a href="http://<?php echo $moodle_url.'/backup/restorefile.php?contextid=1'; ?>"> Restore Course Manually</a>
  <?php
  if(isset($_POST['cli-backup'])){
    if (file_exists($option.'/backup.mbz')) {
      rename($option.'/backup.mbz',$option.'/backup.mbz.backup');
    }
    $destination = '"'.realpath($option).'"';
    echo system("/usr/bin/php $directory/admin/cli/backup.php --courseid=$course_id --destination=$destination; mv $option/*.mbz $option/backup.mbz;");
    $thisdir = scandir($option);
    foreach($thisdir as $value){
      if(strpos($value,'moodle2') != NULL){
        echo "hello";
        rename($option.'/'.$value,$option.'/backup.mbz');
      }
    }
  }
  if(isset($_POST['update'])){
    require($option .'/varset.php');
    $method = $_POST['method'];
    $_SESSION['method'] = $method;
    $manual_backup = new backup($option, $method, $url);
    $manual_backup->make_signature();
    $manual_backup->send_signature();
  }
  if(isset($_POST['master'])){
    if (!copy('rdiff_make_patch.php', $option . '/rdiff_make_patch.php')) {
      echo 'failed to copy rdiff_make_patch.php...\n';
    }
      echo "\n give this url to slave: " . $_SERVER[HTTP_HOST] . str_replace('rdiff_update_console.php', $option, $_SERVER[REQUEST_URI]) . '/';
    }
  if(isset($_POST['undo'])){
    if (file_exists($option.'/backup.mbz.sig')) {
      unlink($option.'/backup.mbz.sig');
    }
    if (file_exists($option.'/backup.mbz.delta')) {
      unlink($option.'/backup.mbz.delta');
    }
    if (file_exists($option.'/backup.mbz.backup')) {
      rename($option.'/backup.mbz.backup',$option.'/backup.mbz');
    }
  }
}      
?>
