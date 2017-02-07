<html>
<head>
<title>Practice PHP</title>
</head>
<body>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  Search: <input type="text" name="search"><br>
  <input type="submit">
</form>

<?php 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$search = $_POST['search'];
echo "You have search for $search";
}
?>

</body>
</html>
