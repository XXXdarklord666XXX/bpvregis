<?php
if (isset($_GET["StNummer"]) && $_GET["StNummer"] != "") {
    include("connect_db.php");

    $query = "SELECT * FROM `users` WHERE `id` = " . $_GET["StNummer"];
    $result = mysqli_query($conn, $query);
    $record = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if ($result) {
        if (is_numeric($_GET["StNummer"])) { 
            $emailaddress = $_GET["StNummer"]."@student.mboutrecht.nl";
        } else {
            $emailaddress = $_GET["StNummer"]."@.mboutrecht.nl";
        }
        $firstName = $record["firstname"];
        $infix = $record["infix"];
        $lastname = $record["lastname"];
        $subject = "Activatie account";
        $last_id = $_GET["StNummer"];
        $activate = $record["activate"];
        $password = $record["password"];

    if ($activate == 'false') {

            if ($password == "") {
            
            // Maak een random tijdelijk password en haal dit door een sha1 hash
            $first3OfFirstname = substr($firstName, 0, 3);
            $last4OfLastname = substr($lastname, (strlen($lastname) - 4), 4);
            $date = date("d-m-Y H:i:s");
            $tempPassword = $date." ".$first3OfFirstname." ".$last4OfLastname;
            $tempPassword = sha1($tempPassword);
            $query = "UPDATE `users` 
                        SET `password` = '" . $tempPassword . "'
                        WHERE `id` = " . $last_id;
            mysqli_query($conn, $query);
            $messageHtml = "<!DOCTYPE html>
                            <html>
                                <head>
                                    <title>Page Title</title>
                                    <style>
                                    body
                                    {
                                        font-family: Verdana, Arial;
                                        font-size: 1em;
                                        color: rgb(30, 30, 30);
                                    }
                                    </style>
                                </head>
                                <body>
                                <h3>Beste ".$firstName." ".$infix." ".$lastname.",</h3>".
                                    "<p>Bedankt voor het registreren, klik op onderstaande link<p>".
                                    "<p><a href='http://www.dennis.nl/index.php?content=activate&id=".
                                    $last_id."&pw=".$tempPassword."'>activatielink</a></p><p>om uw account te activeren</p>". 
                                    "<p>Met vriendelijke groet,</p>". 
                                    "<p>admin</p>
                                </body>
                            </html>";

            $headers = "Content-Type: text/html; charset=UTF-8"."\r\n";
            $headers .= "Cc: adruijter@fopmail.com, hans@testmail.com, frans@realmail.com"."\r\n";
            $headers .= "Bcc: rra@mboutrecht.nl"."\r\n";
            $headers .= "From: adruijter@gmail.com";             
            
        
            mail($emailaddress, $subject, $messageHtml, $headers);
            echo "succes";
            } else {
                echo "Activatiemail al gestuurd";
            }
        } else {
            echo "U account is al geactiveerd";
        }
    } else {
        echo "studentnummer niet bekent";
    }
} else {
    echo "geen studentnummer ingevuld";
}
?>