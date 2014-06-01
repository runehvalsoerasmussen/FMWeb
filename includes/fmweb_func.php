<?php
include_once 'fmweb_config.php';

function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = SECURE;
    // This stops JavaScript being able to access the session id.
    $httponly = true;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"],
        $cookieParams["domain"],
        $secure,
        $httponly);
    // Sets the session name to the one set above.
    session_name($session_name);
    session_start();            // Start the PHP session
    session_regenerate_id();    // regenerated the session, delete the old one.
}

function login($email, $pwd, $mysqli) {
    // Using prepared statements means that SQL injection is not possible.
    if ($stmt = $mysqli->prepare("SELECT id, username, slvl, password, salt
        FROM UserDB
        WHERE email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();

        // get variables from result.
        $stmt->bind_result($user_id, $username, $slvl, $db_password, $salt);
        $stmt->fetch();

        // hash the password with the unique salt.
        $pwd = hash('sha512', $pwd . $salt);
        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts

            if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked
                // Send an email to user saying their account is locked
                return 0;
            } else {
                // Check if the password in the database matches
                // the password the user submitted.
                if ($db_password == $pwd) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/",
                                                                "",
                                                                $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512',
                              $pwd . $user_browser);
                    // Login successful - return the security level for this user, 0=login failed, 1=admin, 2=user
                    return $slvl;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $mysqli->query("INSERT INTO LoginDB(user_id, time)
                                    VALUES ('$user_id', '$now')");
                    return 0;
                }
            }
        } else {
            // No user exists.
            return 0;
        }
    }
}

function checkbrute($user_id, $mysqli) {
    // Get timestamp of current time
    $now = time();

    // All login attempts are counted from the past 2 hours.
    $valid_attempts = $now - (2 * 60 * 60);

    if ($stmt = $mysqli->prepare("SELECT time
                             FROM LoginDB <code><pre>
                             WHERE user_id = ?
                            AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);

        // Execute the prepared query.
        $stmt->execute();
        $stmt->store_result();

        // If there have been more than 5 failed logins
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    }
}

function login_check($mysqli) {
    // Check if all session variables are set
    if (isset($_SESSION['user_id'],
              $_SESSION['username'],
              $_SESSION['login_string'])) {

        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];

        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        if ($stmt = $mysqli->prepare("SELECT password
                                      FROM UserDB
                                      WHERE id = ? LIMIT 1")) {
            // Bind "$user_id" to parameter.
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($pwd);
                $stmt->fetch();
                $login_check = hash('sha512', $pwd . $user_browser);

                if ($login_check == $login_string) {
                    // Logged In!!!!
                    return true;
                } else {
                    // Not logged in
echo "<p>ln 144</p>\n";
                    return false;
                }
            } else {
                // Not logged in
echo "<p>ln 149</p>\n";
                return false;
            }
        } else {
            // Not logged in
echo "<p>ln 154</p>\n";
printf("user_id=%s, username=%s, login_string=%s.\n", $_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string']);
            return false;
        }
    } else {
        // Not logged in
echo "<p>ln 160</p>\n";
        return false;
    }
}

function esc_url($url) {

    if ('' == $url) {
        return $url;
    }

    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;

    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }

    $url = str_replace(';//', '://', $url);

    $url = htmlentities($url);

    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);

    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}

function write_static_form($CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text) {
   echo "<form action=\"readnext.php\" method=\"post\">\n";
   if ($CurrentTestPage < $TotalPages) {
      printf("<INPUT type=\"hidden\" name=\"PageID\" value=\"%s\">\n", $CurrentTestPage+1);
   } else {
      if ($CurrentTestPage == $TotalPages) {
         printf("<INPUT type=\"hidden\" name=\"PageID\" value=\"%s\">\n", 0);
      }
   }
   printf("<INPUT type=\"hidden\" name=\"TestID\" value=\"%s\">\n", $TestID);
   printf("<p>Side %s af totalt %s\n", $CurrentTestPage, $TotalPages);


   echo "        <!-- ydre ramme -->\n";
   echo "    <div id=\"frame1\"><!-- ydre ramme -->\n";

   echo "        <!-- Logo banner i toppen -->\n";
   echo "    <div id=\"logo\"></div>\n";

   echo "        <!-- Ramme rundt tekstindhold -->\n";
   echo "    <div id=\"frame2\"><p></p></div>\n";

   echo "        <!-- Indholdstekst/ den forklarende tekst -->\n";
   printf("    <div id=\"frame2a\"><p><span style=\"font-size:1.4em; color: #9F000F; font-style:italic \">%s\n", $ExplainText);

   echo "        </span></p></div>\n";

   echo "        <!-- Billede - box -->\n";
   echo "    <div id=\"frame3\">\n";
   printf("<img src=\"http://www.danskerne.se/FMWeb/images/%s\"\n>", $PictName);
   echo "    </div>\n";

   echo "        <!-- TEXT - Næste/ box -->\n";
   echo "    <div id=\"frame3a\">\n";
   echo "        <a href=\"#\" class=\"css_btn_class\"><span style=\"font-size:1.2em; color: #9F000F; font-style:italic \">\n";
   echo "<INPUT type=\"image\" id=\"myButton\" src=\"..\/green.png\" width=\"50\" height=\"50\">\n";
   echo "</span></a></div>\n";

   echo "        <!-- Overskrift /Spørgsmål -->                \n";
   printf("    <div id=\"frame4\"><span style=\"font-size:1.4em; color: #9F000F; font-style:italic \">%s\n", $QuestionText);
   echo "</span></div>\n";

   echo "        <!-- Spørgsmål 1 -->\n";
   echo "    <div id=\"frame4a\">\n";
   echo htmlentities($Q1Text);
   echo "</div>\n";

   echo "        <!-- Spørgsmål 2 -->\n";
   echo "    <div id=\"frame4b\">\n";
   echo htmlentities($Q2Text);
   echo "</div>\n";

   echo "        <!-- Spørgsmål 3 -->\n";
   echo "    <div id=\"frame4c\">\n";
   echo htmlentities($Q3Text);
   echo "</div>\n";

   echo "    <section title=\".squaredFour\">\n";
   echo "    <!-- Checkbox 1 -->\n";
   echo "    <div class=\"squaredFour\">\n";
   echo "      <input type=\"checkbox\" value=\"None\" id=\"squaredFour\" name=\"check\" checked />\n";
   echo "      <label for=\"squaredFour\"></label>\n";
   echo "    </div>\n";
   echo "    <!-- end Checkbox 1 -->\n";
   echo "  </section>\n";

   echo "     <section title=\".squaredFour1\">\n";
   echo "    <!-- Checkbox 2 -->\n";
   echo "    <div class=\"squaredFour1\">\n";
   echo "      <input type=\"checkbox\" value=\"None\" id=\"squaredFour1\" name=\"check\" checked />\n";
   echo "      <label for=\"squaredFour1\"></label>\n";
   echo "    </div>\n";
   echo "    <!-- end Checkbox 2 -->\n";
   echo "  </section>\n";

   echo " <section title=\".squaredFour2\">\n";
   echo "    <!-- Checkbox 3 -->\n";
   echo "    <div class=\"squaredFour2\">\n";
   echo "      <input type=\"checkbox\" value=\"None\" id=\"squaredFour2\" name=\"check\" checked />\n";
   echo "      <label for=\"squaredFour2\"></label>\n";
   echo "    </div>\n";
   echo "    <!-- end Checkbox 3 -->\n";
   echo "  </section>\n";
   echo "</p>\n";
   echo "</form>\n";
}

function write_static_form($CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text) {
   echo "<div id=\"frame1\">\n";
   echo "<form action=\"readnext.php\" method=\"post\">\n";
   if ($CurrentTestPage < $TotalPages) {
      printf("<INPUT type=\"hidden\" name=\"PageID\" value=\"%s\">\n", $CurrentTestPage+1);
   } else {
      if ($CurrentTestPage == $TotalPages) {
         printf("<INPUT type=\"hidden\" name=\"PageID\" value=\"%s\">\n", 0);
      }
   }
   printf("<INPUT type=\"hidden\" name=\"TestID\" value=\"%s\">\n", $TestID);
   printf("<p>Side %s af totalt %s\n", $CurrentTestPage, $TotalPages);
   echo "<div id=\"frame2\">\n";
   echo htmlentities($ExplainText);
   echo "</div>\n";
   echo "<div id=\"frame3\">\n";
   printf("<img src=\"http://www.danskerne.se/FMWeb/images//%s\" width=\"%s\" height=\"%s\" />\n", $PictName, "100%", "100%");
   echo "</div>\n";
   echo "<div id=\"frame3b\">\n";
   echo htmlentities($QuestionText);
   echo "</div>\n";
   echo "<div id=\"frame4\">\n";
   echo "<input type=\"radio\" onclick=\"enableNext()\" name=\"CorrectAnswers\"";
   if ($QSvar == 1){
      printf("value=\"%s\">\n", ($CorrectAnswers + 1));
   } else {
      printf("value=\"%s\">\n", $CorrectAnswers);
   }
   echo htmlentities($Q1Text);
   echo "</div>\n";
   echo "<div id=\"frame5\">\n";
   echo "<input type=\"radio\" onclick=\"enableNext()\" name=\"CorrectAnswers\"";
   if ($QSvar == 2){
      printf("value=\"%s\">\n", ($CorrectAnswers + 1));
   } else {
      printf("value=\"%s\">\n", $CorrectAnswers);
   }
   echo htmlentities($Q2Text);
   echo "<br>\n";
   echo "</div>\n";
   echo "<div id=\"frame6\">\n";
   echo "<input type=\"radio\" onclick=\"enableNext()\" name=\"CorrectAnswers\"";
   if ($QSvar == 3){
      printf("value=\"%s\">\n", ($CorrectAnswers + 1));
   } else {
      printf("value=\"%s\">\n", $CorrectAnswers);
   }
   echo htmlentities($Q3Text);
   echo "<br>\n";
   echo "</div>\n";
   echo "<div id=\"frame7\">\n";
   echo "</div>\n";
   echo "</form>\n";
   echo "</div>\n";
}

function old_write_header_html() {
   echo "<!DOCTYPE html>\n";
   echo "<html>\n";
   echo "<head>\n";
   echo "<title>Brandtest forsiden</title>\n";
   echo "<style>\n";
   echo "table, th, td\n";
   echo "{\n";
   echo "border:1px solid black;\n";
   echo "}\n";
   echo "</style>\n";
//   echo "   <script>";
//   echo "      function enableNext()";
//   echo "      {";
//   echo "      document.getElementById(\"frame7\").innerHTML = \"<INPUT type=\"image\" id=\"myButton\" src=\"..\/green.png\" width=\"50\" height=\"50\">\n";";
//   echo "      }";
//   echo "   </script>";
   echo "</head>\n";
   echo "<body>\n";
}

function write_header_html() {
   echo "<!DOCTYPE html>\n";
   echo "<html lang=\"da\">\n";
   echo "    <head>\n";
   echo "        <title>Brandtesten - skriv sidenavn her</title>\n";
   echo "        <meta charset=\"UTF-8\">\n";
   echo "        <meta name=\"viewport\" content=\"width=device-width\">\n";
   echo "       <link rel=\"stylesheet\" href=\"http://www.danskerne.se/FMWeb/styles/newcss.css\" type=\"text/css\"/>\n";
   echo "       <link href='http://fonts.googleapis.com/css?family=Ubuntu+Condensed,subset=latin,latin-ext,cyrillic-ext' rel='stylesheet' type='text/css'>\n";
   echo "    </head>\n";
   echo "    <body> <section id=\"container\">\n";
}

function write_footer_html() {
   echo "<!-- Menu starts here -->\n";
   echo "<div id=\"menu\">\n";

   echo "<!-- List starts here -->\n";
   echo "<ul class=\"menu\"><li><a href=\"#\">Hjem</a>\n";
   echo "</li>\n";

   echo "<li><a href=\"#\">Start testen</a>\n";
   echo "</li>\n";

   echo "<li><a href=\"#\">Hvad er Brandtest</a>\n";

   echo "       <div class=\"dropdown-5columns\">\n";
   echo "        <div class=\"col-5\">\n";
   echo "                <img src=\"Test.jpeg.jpg\" width=\"100\" height=\"100\" alt=\"\" />\n";
   echo "                <p>Brandtest er et online træning og test program som er skræddersyet til din bygning og hvor medarbejderne lærer at forebygge brand.<br> Brandtest tager hånd om den teoretiske del af brandoplæring på din arbejdspladsen.<br>\n";
   echo "                <br>Systemet har statistik over hvem der har bestået prøven, og hvem som har været på brandøvelser.<br><br>\n";
   echo "                Dette kan bruges mod brandvæsenet ved kontrol.<br>\n";
   echo "                Fordelen ved at gennemføre uddannelse online er, at du er slipper at sende medarbejdere på kurser.<br><br>\n";
   echo "                Dette er især nyttigt for virksomheder med stor rotation af personale. </p>\n";
   echo "            </div>\n";

   echo "            <div class=\"col-5\"><h2></h2></div>\n";

   echo "            <div class=\"col-1\"><p><br> </p></div>\n";

   echo "            <div class=\"col-1\"><p class=\"italic\"></p></div>\n";

   echo "            <div class=\"col-1\"><p></p></div>\n";

   echo "            <div class=\"col-1\"><p class=\"strong\"></p></div>\n";
   echo "        </div>\n";
   echo "    </li>\n";
   echo "    <li><a href=\"#\">Brand dokumentation</a>\n";
   echo "        <div class=\"dropdown-3columns\">\n";
   echo "            <div class=\"col-3\">\n";
   echo "                <h2>Værd at vide om brandsikkerhed</h2>\n";
   echo "            </div>\n";
   echo "            <div class=\"col-1\">\n";
   echo "                <ul class=\"grisbox\">\n";
   echo "                    <li><a href=\"#\" title=\"Hvad siger loven?\">Hvad siger loven?</a></li>\n";
   echo "                    <li><a href=\"#\" title=\"Drift & vedligeholdelse\">Drift & vedligeholdelse</a></li>\n";
   echo "                    <li><a href=\"#\" title=\"Loven om gasser\">Loven om gasser</a></li>\n";
   echo "                    <li><a href=\"#\" title=\"Loven om brandfarlig væsker\">Loven om brandfarlig væsker</a></li>\n";
   echo "                    <li><a href=\"#\" title=\"Varmt arbejde\">Varmt arbejde</a></li>\n";
   echo "                </ul>\n";
   echo "            </div>\n";
   echo "            <div class=\"col-1\">\n";
   echo "                <p class=\"italic blackbox\">Her kan du finde flere oplysninger om brand dokumentation. Du kan også hente div. skemaer og instrukser til brug på din arbejdsplads. </p>\n";
   echo "            </div>\n";
   echo "            <div class=\"col-1\">\n";
   echo "                <ul class=\"grisbox\">\n";
   echo "                    <li><a href=\"#\" title=\"Forholdsregler ved brand\">Forholdsregler ved brand</a></li>\n";
   echo "                    <li><a href=\"#\" title=\"Instrukser ved brand\">Instrukser ved brand</a></li>\n";
   echo "                    <li><a href=\"#\" title=\"Instrukser ved varmt arbejde\">Instrukser ved varmt arbejde</a></li>\n";
   echo "                    <li><a href=\"#\" title=\"112 & evakuering\">112 & evakuering</a></li>\n";
   echo "                </ul>\n";
   echo "            </div>        \n";
   echo "        </div>\n";
   echo "    </li>\n";
   echo "	<li><a href=\"#\">Om FMweb</a>\n";
   echo "		<div class=\"dropdown-1column\">\n";
   echo "                <div class=\"col-demo\">\n";
   echo "                    <ul class=\"simple\">\n";
   echo "                        <li><a href=\"#\" title=\"Hvem er vi?\">Hvem er vi?</a></li>\n";
   echo "                        <li><a href=\"#\" title=\"Kontakt os\">Kontakt os</a></li>\n";
   echo "                    </ul>\n";
   echo "                </div>\n";
   echo "		</div>\n";
   echo "	</li>\n";

   echo "  </ul>\n";

   echo "</div>\n";
   echo "<div id=\"footer1\"> </div>\n";
   echo "</div>\n";
   echo "</section>\n";

   echo "        <div id=\"footer-1\">  </div>\n";

    echo "   </body>\n";
   echo "</html>\n";
}

function old_write_footer_html() {
   echo "</body>\n";
   echo "</html>\n";
}

function write_edit_form($CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text) {
   echo "<form action=\"writetest.php\" method=\"post\">\n";
   printf("<INPUT type=\"hidden\" name=\"TestID\" value=\"%s\">\n", $TestID);
   printf("<INPUT type=\"hidden\" name=\"TotalID\" value=\"%s\">\n", $TotalPages);
   printf("<INPUT type=\"hidden\" name=\"PageID\" value=\"%s\">\n", $CurrentTestPage);

   echo "        <!-- ydre ramme -->\n";
   echo "    <div id=\"frame1\"><!-- ydre ramme -->\n";

   echo "        <!-- Logo banner i toppen -->\n";
   echo "    <div id=\"logo\"></div>\n";

   echo "        <!-- Ramme rundt tekstindhold -->\n";
   echo "    <div id=\"frame2\"><p></p></div>\n";

   echo "        <!-- Indholdstekst/ den forklarende tekst -->\n";
   echo "    <div id=\"frame2a\"><p><span style=\"font-size:1.4em; color: #9F000F; font-style:italic \">\n";
   printf("<textarea name=\"ExplainText\">%s\n", $ExplainText);
   echo "</textarea>\n";

   echo "        </span></p></div>\n";

   echo "        <!-- Billede - box -->\n";
   echo "    <div id=\"frame3\">\n";
   printf("<img src=\"http://www.danskerne.se/FMWeb/images/%s\"\n>", $PictName);
   printf("<br>New pict name: <input type=\"text\" name=\"PictName\" value=\"%s\">\n", $PictName);
   echo "    </div>\n";

   echo "        <!-- TEXT - Næste/ box -->\n";
   echo "    <div id=\"frame3a\">\n";
   echo "        <a href=\"#\" class=\"css_btn_class\"><span style=\"font-size:1.2em; color: #9F000F; font-style:italic \">\n";
   echo "<INPUT type=\"image\" id=\"myButton\" src=\"..\/green.png\" width=\"50\" height=\"50\">\n";
   echo "</span></a></div>\n";

   echo "        <!-- Overskrift /Spørgsmål -->                \n";
   echo "    <div id=\"frame4\"><span style=\"font-size:1.4em; color: #9F000F; font-style:italic \">\n";
   printf("<textarea name=\"QuestionText\">%s\n", $QuestionText);
   echo "</textarea>\n";
   echo "</span></div>\n";

   echo "        <!-- Spørgsmål 1 -->\n";
   echo "    <div id=\"frame4a\">\n";
   echo htmlentities($Q1Text);
   printf("<textarea name=\"Q1Text\">%s\n", $Q1Text);
   echo "</textarea>\n";
   echo "</div>\n";

   echo "        <!-- Spørgsmål 2 -->\n";
   echo "    <div id=\"frame4b\">\n";
   echo htmlentities($Q2Text);
   printf("<textarea name=\"Q1Text\">%s\n", $Q2Text);
   echo "</textarea>\n";
   echo "</div>\n";

   echo "        <!-- Spørgsmål 3 -->\n";
   echo "    <div id=\"frame4c\">\n";
   echo htmlentities($Q3Text);
   printf("<textarea name=\"Q1Text\">%s\n", $Q3Text);
   echo "</textarea>\n";
   echo "</div>\n";

   echo "    <section title=\".squaredFour\">\n";
   echo "    <!-- Checkbox 1 -->\n";
   echo "    <div class=\"squaredFour\">\n";
   echo "      <input type=\"checkbox\" value=\"None\" id=\"squaredFour\" name=\"check\" checked />\n";
   echo "      <label for=\"squaredFour\"></label>\n";
   echo "    </div>\n";
   echo "    <!-- end Checkbox 1 -->\n";
   echo "  </section>\n";

   echo "     <section title=\".squaredFour1\">\n";
   echo "    <!-- Checkbox 2 -->\n";
   echo "    <div class=\"squaredFour1\">\n";
   echo "      <input type=\"checkbox\" value=\"None\" id=\"squaredFour1\" name=\"check\" checked />\n";
   echo "      <label for=\"squaredFour1\"></label>\n";
   echo "    </div>\n";
   echo "    <!-- end Checkbox 2 -->\n";
   echo "  </section>\n";

   echo " <section title=\".squaredFour2\">\n";
   echo "    <!-- Checkbox 3 -->\n";
   echo "    <div class=\"squaredFour2\">\n";
   echo "      <input type=\"checkbox\" value=\"None\" id=\"squaredFour2\" name=\"check\" checked />\n";
   echo "      <label for=\"squaredFour2\"></label>\n";
   echo "    </div>\n";
   echo "    <!-- end Checkbox 3 -->\n";
   echo "  </section>\n";
   echo "</p>\n";
   echo "</form>\n";
}

function old_write_edit_form($CurrentTestPage, $TotalPages, $TestID, $ExplainText, $PictName, $QuestionText, $QSvar, $CorrectAnswers, $Q1Text, $Q2Text, $Q3Text) {
   echo "<form action=\"writetest.php\" method=\"post\">\n";
   printf("<INPUT type=\"hidden\" name=\"TestID\" value=\"%s\">\n", $TestID);
   printf("<INPUT type=\"hidden\" name=\"TotalID\" value=\"%s\">\n", $TotalPages);
   printf("<INPUT type=\"hidden\" name=\"PageID\" value=\"%s\">\n", $CurrentTestPage);

   echo "<table>\n";
   echo "<tr>\n";
   echo "<th>Forklaring:</th>\n";
   echo "<th>Billede</th>\n";
   echo "</tr>\n";
   echo "<tr>\n";
   echo "<td>\n";
   printf("<textarea name=\"ExplainText\">%s\n", $ExplainText);
   echo "</textarea>\n";
   echo "</td>\n";
   echo "<td>%\n";
   printf("<img src=\"http://www.danskerne.se/FMWeb/images/%s\"\n>", $PictName);
   printf("<br>New pict name: <input type=\"text\" name=\"PictName\" value=\"%s\">\n", $PictName);
   echo "</td>\n";
   echo "</tr>\n";
   echo "<tr>\n";
   echo "<th colspan\"2\">Sp&#248;rgsm�l:</th>\n";
   echo "</tr>\n";
   echo "<tr>\n";
   echo "<td colspan=\"2\">\n";
   printf("<textarea name=\"QuestionText\">%s\n", $QuestionText);
   echo "</textarea>\n";
   echo "</td>\n";
   echo "</tr>\n";
   echo "</table>\n";
   echo "<p>\n";
   if ($QSvar == 1){
      echo "<input type=\"radio\" name=\"QSvar\" value=\"1\" checked>\n";
   } else {
      echo "<input type=\"radio\" name=\"QSvar\" value=\"1\">\n";
   }
   echo htmlentities($Q1Text);
   printf("<textarea name=\"Q1Text\">%s\n", $Q1Text);
   echo "</textarea>\n";
   echo "<br>\n";
   if ($QSvar == 2){
      echo "<input type=\"radio\" name=\"QSvar\" value=\"2\" checked>\n";
   } else {
      echo "<input type=\"radio\" name=\"QSvar\" value=\"2\">\n";
   }
   echo htmlentities($Q2Text);
   printf("<textarea name=\"Q2Text\">%s\n", $Q2Text);
   echo "</textarea>\n";
   echo "<br>\n";
   if ($QSvar == 3){
      echo "<input type=\"radio\" name=\"QSvar\" value=\"3\" checked>\n";
   } else {
      echo "<input type=\"radio\" name=\"QSvar\" value=\"3\">\n";
   }
   echo htmlentities($Q3Text);
   printf("<textarea name=\"Q3Text\">%s\n", $Q3Text);
   echo "</textarea>\n";
   echo "<br>\n";
   echo "<INPUT type=\"image\" id=\"myButton\" src=\"..\/green.png\" width=\"50\" height=\"50\">\n";
   echo "</p>\n";
   echo "</form>\n";
}

?>
