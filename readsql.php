<!DOCTYPE html>
<html>
<head>
   <title>SQL Test</title>
</head>
<body>
<?php

echo "Pkt 1<br> \n";

   $grpID = $_POST["grpNbr"];

   echo $grpID;

#$sql = "SELECT * FROM `DKData` LIMIT 0, 30 ";

#echo $sql;

#echo "Pkt 1.5 <br> \n";

#$pdo = new PDO('mysql:host=example.com.mysql;dbname=database', 'user', 'password');
#$statement = $pdo->query("SELECT 'Hello, dear MySQL user!' AS _message FROM DUAL");
#$row = $statement->fetch(PDO::FETCH_ASSOC);
#echo htmlentities($row['_message']);

try{
   $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', 'S0mmarGr0nt');
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "Pkt 2 <br> \n";
$statement = $db->query("SELECT * FROM DKData WHERE grp=$grpID");
$row = $statement->fetch(PDO::FETCH_ASSOC);
echo htmlentities("<br>FileID<" . $row['FileID'] . ">LinkAdr<" . $row['LinkAdr'] . ">Description<" . $row['Description'] . ">grp<" . $row['grp'] . ">");

echo "Done SQL<br>\n";
}
catch (PDOException $e) {
printf("<br>Husdon we had a problem: %s\n",$e->getMessage());
}

echo "<br>Pkt 3\n";

?>
</body>
</html>
