<!DOCTYPE html>
<html>
<head>
   <title>SQL Test</title>
</head>
<body>
<?php

echo "Pkt 1<br> \n";

   $File_ID = $_POST["FileNbr"];
   $Link_Adr = $_POST["LinkTxt"];
   $Description_txt = $_POST["message"];
   $grpID = $_POST["GrpNbr"];


try{
   $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   echo "Pkt 2 <br> \n";
   $value_txt = "'" . $File_ID . "','" . $Link_Adr . "','" . $Description_txt . "','" . $grpID . "'";
   echo $value_txt;
   $statement = $db->query("INSERT INTO DKData (FileID, LinkAdr, Description, grp) VALUES ($value_txt)");
#   $row = $statement->fetch(PDO::FETCH_ASSOC);
#   echo htmlentities("<br>FileID<" . $File_ID . ">LinkAdr<" . $Link_Adr . ">Description<" . $Description_txt . ">grp<" . $grpID . ">");

   echo "Done SQL<br>\n";
   }
   catch (PDOException $e) {
   printf("<br>Husdon we had a problem: %s\n",$e->getMessage());
   }

echo "<br>Pkt 3\n";

?>
</body>
</html>
