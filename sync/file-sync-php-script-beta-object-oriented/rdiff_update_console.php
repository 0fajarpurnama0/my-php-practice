<?php
session_start();
require('lib_main.php'); 
?>

<html>
<head>
<title>Moodle Backup Rdiff Synchronization</title>
<link rel="stylesheet" type="text/css" href="main.css"/>
</head>
<body>

<h1>Moodle Backup Rdiff Synchronizer</h1>
<h3>Based on rdiff: a controlled rsync application.</h3>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
<select name="option">
  <option value="create_new_profile">create new profile</option>
  <?php foreach(glob('./*', GLOB_ONLYDIR) as $dir) {?>
    <option value ="<?php echo substr($dir,2);?>"><?php echo substr($dir,2);?></option>
  <?php } ?>
</select>
<input type="submit" value="choose">
</form><a href="../main_console.php"> Main Console</a>

<?php 
 if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if (strcmp($_POST['option'],'create_new_profile')==0){
    ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
    new profile: <input type="text" name="new_profile">
    <input type="hidden" name="option" value="create_new_profile">
    <input type="submit" value="create profile">
    <?php
    $new_profile = $_POST['new_profile'];
    if (file_exists($new_profile)) {
      echo 'Profile exist, make another name';
    } else {
      mkdir('./'.$new_profile);
    }
  } else {
    $option = $_POST['option'];
    $_SESSION['option'] = $option;
    ?>

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
    <?php
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
 }      
?>
