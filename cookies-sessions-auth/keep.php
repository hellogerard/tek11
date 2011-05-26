<?php

    session_start();

    // if form has posted
    if (isset($_POST['submit']))
    {
        if ($_POST['username'] == 'oink' && $_POST['password'] == 'pug')
        {
            // if successful login
            $_SESSION['username'] = $_POST['username'];

            if (isset($_POST['keep']))
            {
                // hash the username, along with a random ID
                $rand = md5(uniqid(rand(), true));

                // this becomes the session token for this user
                $token = md5($_SESSION['username'] . $rand);

                // store the token in the DB
                $db = new SQLite3('sessions.db');
                $db->exec('CREATE TABLE IF NOT EXISTS sessions (username, token)');
                $db->exec("INSERT INTO sessions VALUES ('{$_SESSION['username']}', '$token')");

                // set the auth token cookie - expires in 1 week
                setcookie('auth_token', $token, time() + 604800, '/', $_SERVER['HTTP_HOST']);
            }
        }
        else
        {
            // if login failed
            $error = "Wrong username/password";
        }
    }
    else if (isset($_SESSION['username']))
    {
        // standard PHP session
    }
    else if (isset($_COOKIE['auth_token']))
    {
        // look for a valid sesion in DB
        $db = new SQLite3('sessions.db');
        $result = $db->querySingle("SELECT username FROM sessions WHERE token = '{$_COOKIE['auth_token']}'");

        // if a valid session is found
        if ($result)
        {
            // recreate session
            session_regenerate_id();

            // get username
            $_SESSION['username'] = $result;

            // recreate token
            $rand = md5(uniqid(rand(), true));
            $token = md5($_SESSION['username'] . $rand);
            $db->exec("DELETE FROM sessions");
            $db->exec("INSERT INTO sessions VALUES ('{$_SESSION['username']}', '$token')");
            setcookie('auth_token', $token, time() + 604800, '/', $_SERVER['HTTP_HOST']);
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
                <label for="keep">Keep Me Logged In:<label>
                <input type="checkbox" name="keep" id="keep" value="true" />
            </p>
            <p>
                <input type="submit" name="submit" id="submit" value="Login" />
            </p>
        </form>

        <?php
    }

