<?php
include_once 'includes/db_connect.php';
include_once 'includes/fmweb_func.php';

sec_session_start();
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html lang="da">
    <head>
        <title>Brandtesten - skriv sidenavn her</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <script>
           function enableNext()
           {
              document.getElementById("frame7").innerHTML = "<INPUT type=\"image\" id=\"myButton\" src=\"..\/green.png\" width=\"50\" height=\"50\">\n";
           }
        </script>

        <link rel="stylesheet" href="styles/newcss.css" type="text/css"/>
        <link href='http://fonts.googleapis.com/css?family=Ubuntu+Condensed,subset=latin,latin-ext,cyrillic-ext' rel='stylesheet' type='text/css'>
    </head>


    <body> <section id="container">

<?php
if (isset($_POST["testKode"],
      $_POST["PageID"],
      $_POST["CorrectAnswers"])) {
   $TokenID = $_POST["testKode"];
   $CurrentTestPage = $_POST["PageID"];
   $CorrectAnswers = $_POST["CorrectAnswers"];
   $username="";
   $email="";
   $TestID="";
   $Resultat="";
   $foundTest = 0;
   try {
      $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $statement = $db->query("SELECT * FROM TokenDB2 WHERE Token=$TokenID");
         if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $username = $row['username'];
            $email = $row['email'];
            $TestID = $row['TestID'];
            $Resultat = $row['Resultat'];
            $foundTest = 1;
         }
         else
         {
            echo("<p>Test not found - <a href=\"http://danskerne.se/FMWeb/start.html\">return to main page</a>\n");
            echo("</body></html>\n");
         }
      } catch (PDOException $e) {
         printf("<br>Huston 2 we had a problem: %s\n",$e->getMessage());
         echo("</body></html>\n");
         exit();
      }
   if ($foundTest == 1)
   {
   try {
      $db = new PDO('mysql:host=danskerne.se.mysql;dbname=danskerne_se', 'danskerne_se', $password);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         $statement = $db->query("SELECT * FROM UdTest WHERE TestID=$TestID AND PageID=$CurrentTestPage");
         $row = $statement->fetch(PDO::FETCH_ASSOC);
         $ExplainText=$row['ExplainTXT'];
         $QuestionText=$row['QuestionTXT'];
         $Q1Text=$row['Q1'];
         $Q2Text=$row['Q2'];
         $Q3Text=$row['Q3'];
         $QSvar=$row['QSvar'];
         $TotalPages=$row['TotalID'];
         $PictName=$row['Pict'];
         write_static_form($CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text);
      } catch (PDOException $e) {
         printf("<br>Huston 2 we had a problem: %s\n",$e->getMessage());
         echo("</body></html>\n");
         exit();
      }

   }


?>

<!-- Menu starts here -->
<div id="menu">

<!-- List starts here -->
<ul class="menu"><li><a href="#">Hjem</a>
</li>

<li><a href="#">Start testen</a>
</li>

<li><a href="#">Hvad er Brandtest</a>

       <div class="dropdown-5columns">
        <div class="col-5">
                <img src="Test.jpeg.jpg" width="100" height="100" alt="" />
                <p>Brandtest er et online træning og test program som er skræddersyet til din bygning og hvor medarbejderne lærer at forebygge brand.<br> Brandtest tager hånd om den teoretiske del af brandoplæring på din arbejdspladsen.<br>
                <br>Systemet har statistik over hvem der har bestået prøven, og hvem som har været på brandøvelser.<br><br>
                Dette kan bruges mod brandvæsenet ved kontrol.<br>
                Fordelen ved at gennemføre uddannelse online er, at du er slipper at sende medarbejdere på kurser.<br><br>
                Dette er især nyttigt for virksomheder med stor rotation af personale. </p>
            </div>

            <div class="col-5"><h2></h2></div>

            <div class="col-1"><p><br> </p></div>

            <div class="col-1"><p class="italic"></p></div>



            <div class="col-1"><p></p></div>

            <div class="col-1"><p class="strong"></p></div>
        </div>
    </li>
    <li><a href="#">Brand dokumentation</a>
        <div class="dropdown-3columns">
            <div class="col-3">
                <h2>Værd at vide om brandsikkerhed</h2>
            </div>
            <div class="col-1">
                <ul class="grisbox">
                    <li><a href="#" title="Hvad siger loven?">Hvad siger loven?</a></li>
                    <li><a href="#" title="Drift & vedligeholdelse">Drift & vedligeholdelse</a></li>
                    <li><a href="#" title="Loven om gasser">Loven om gasser</a></li>
                    <li><a href="#" title="Loven om brandfarlig væsker">Loven om brandfarlig væsker</a></li>
                    <li><a href="#" title="Varmt arbejde">Varmt arbejde</a></li>
                </ul>
            </div>
            <div class="col-1">
                <p class="italic blackbox">Her kan du finde flere oplysninger om brand dokumentation. Du kan også hente div. skemaer og instrukser til brug på din arbejdsplads. </p>
            </div>
            <div class="col-1">
                <ul class="grisbox">
                    <li><a href="#" title="Forholdsregler ved brand">Forholdsregler ved brand</a></li>
                    <li><a href="#" title="Instrukser ved brand">Instrukser ved brand</a></li>
                    <li><a href="#" title="Instrukser ved varmt arbejde">Instrukser ved varmt arbejde</a></li>
                    <li><a href="#" title="112 & evakuering">112 & evakuering</a></li>
                </ul>
            </div>
        </div>
    </li>
	<li><a href="#">Om FMweb</a>
		<div class="dropdown-1column">
                <div class="col-demo">
                    <ul class="simple">
                        <li><a href="#" title="Hvem er vi?">Hvem er vi?</a></li>
                        <li><a href="#" title="Kontakt os">Kontakt os</a></li>
                    </ul>
                </div>
		</div>
	</li>

  </ul>

</div>
<div id="footer1"> </div>
</div>
</section>



        <div id="footer-1">  </div>

    </body>
</html>



