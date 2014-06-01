<!DOCTYPE html>
<html>
<head>
   <title>SQL Test</title>
</head>
<body>
<?php

echo "Pkt 1<br> \n";


try{
   $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "Pkt 2 <br> \n";
$statement = $db->query("SELECT * FROM DKData");

while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
echo htmlentities("<br>FileID<" . $row['FileID'] . ">LinkAdr<" . $row['LinkAdr'] . ">Description<" . $row['Description'] . ">grp<" . $row['grp'] . ">");
echo "<br>\n";
}

echo "Done SQL<br>\n";
}
catch (PDOException $e) {
printf("<br>Husdon we had a problem: %s\n",$e->getMessage());
}

echo "<br>Pkt 3\n";

?>
</body>
</html>
