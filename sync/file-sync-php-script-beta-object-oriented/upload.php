<?php
session_start();
$option = $_POST['option'];
if (file_exists($option . "/backup.mbz")) {
  unlink($option . "/backup.mbz");
}
$uploaddir = getcwd().'/';
$uploadfile = $uploaddir . $option . "/" . basename($_FILES['userfile']['name']);
echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);

print "</pre>";
rename($uploadfile, $uploaddir . $option . "/backup.mbz");
//chmod($option . "/backup.mbz", 0777);
?>

<a href="rdiff_update_console.php">Return</a>
