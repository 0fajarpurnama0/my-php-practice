<?php
session_start();
if (file_exists($_SESSION['option'] . '/varset.php')) {
  require($_SESSION['option'] . '/varset.php');
}
?>

<html>
<head>
<title>rdiff console settings</title>
</head>
<body>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
Url: <input type="text" name="url" value="<?php echo $url; ?>">
<input type="submit" value="change">
<input type="submit" name="check_dependencies" value="check dependencies"><a href="rdiff_update_console.php">Return</a>

</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $myfile = fopen($_SESSION['option'] . '/varset.php', 'w') or die('Unable to open file!');
  $txt = '
  <?php
  $url ="'.$_POST["url"].'" ; ?>
  ';
  fwrite($myfile, $txt);
  fclose($myfile);
  if(isset($_POST['check_dependencies'])){
    $package = array('rdiff','php-curl','duplicity');
    foreach($package as $value){
      if(strpos(shell_exec("dpkg -s $value"), 'Status: install ok installed') != NULL){
        echo "$value installed <br>";
      } else {
        echo "$value not installed <br>";
      }
    }
  }
  //header("Refresh:0");
}

?>

</body>
</html>
