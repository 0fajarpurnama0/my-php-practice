<?php 
 if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if (strcmp($_POST['option'],'moodle')==0){
    header("Location: moodle/rdiff_update_console.php");
  } else {
    header("Location: manual/rdiff_update_console.php");
  }  
 }      
?>
