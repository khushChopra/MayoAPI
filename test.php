<?php
echo $_GET['phoneNumber'];
echo "<br>";
echo hash('sha256',$_GET['password']);
?>