<html>
<body>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
 <select name="file">
  <option value="test"><?php echo exec('ls -1');?></option>
 </select>
 <input type ="submit" name="execute" value="execute">
</form>

</body>
</html>
