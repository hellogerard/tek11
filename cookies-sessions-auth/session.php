<?php

    session_start();


?><link href='//fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>
<body style="font-size: 2.0em; font-family: 'Ubuntu', Arial;">
<?php



    echo "Value of <code>session.save_path</code>: " . ini_get('session.save_path');


    if (! isset($_SESSION['counter']))
    {
        $_SESSION['counter'] = 0;
    }
    else if (isset($_GET['add']))
    {
        $_SESSION['counter']++;
    }
    else if (isset($_GET['subtract']))
    {
        $_SESSION['counter']--;
    }


    echo "<p>Value of <code>\$_SESSION['counter']</code>: {$_SESSION['counter']}</p>\n";
    echo "<p><a href=\"?add\">Increment</a> <a href=\"?subtract\">Decrement</a></p>";
