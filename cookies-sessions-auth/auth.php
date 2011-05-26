<?php

    session_start();

    // if form has posted
    if (isset($_POST['submit']))
    {
        if ($_POST['username'] == 'oink' && $_POST['password'] == 'pug')
        {
            // if successful login
            $_SESSION['username'] = $_POST['username'];
        }
        else
        {
            // if login failed
            $error = "Wrong username/password";
        }
    }


?><link href='//fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>
<style type="text/css">
    body, input { font-size: 2.0em; font-family: 'Ubuntu', Arial; }
    .error { color: #f00 }
</style>
<body>

<?php

    if (isset($_SESSION['username']))
    {
        // user is logged in
        echo "You are logged in as {$_SESSION['username']}\n";
    }
    else
    {
        // user is not logged in - show login form
        ?>

        <div class="error"><?php echo $error; ?></div>
        <form action="" method="POST">
            <p>
                <label for="username">Username:<label><br/>
                <input type="text" name="username" id="username" value="" />
            </p>
            <p>
                <label for="password">Password:<label><br/>
                <input type="password" name="password" id="password" value="" />
            </p>
            <p>
                <input type="submit" name="submit" id="submit" value="Login" />
            </p>
        </form>

        <?php
    }

