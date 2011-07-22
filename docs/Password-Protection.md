# Password Protecting Boris

Here's an example of how you could password-protect boris on a server:

    <?php
            session_start();
 
            // login
            $login = array(
                array('u' => 'username1', 'p' => 'password1'),
                array('u' => 'username2', 'p' => 'password2'),
            );
 
            //if log-in is successful, redirect to main menu
            if (isset($_POST) && count($_POST)>0) {
                foreach($login as $user) {
                    if ($_POST['u'] == $user['u'] && $_POST['p'] == $user['p']) {
                        $_SESSION['u']=$_POST['u'];
                        $_SESSION['p']=$_POST['p'];
                        header("Location: /");
                        exit();
                    }
                }
            }   

        //if logged in already, redirect to main menu
        foreach($login as $user) {
                if ($_SESSION['u'] == $user['u'] && $_SESSION['p'] == $user['p']) {
                 $loggedin = true;
 
                    // set the location of the main boris directory 
                    define("INCLUDE_PATH", "./_boris");

                     // set the root location, for indexing
                    define("INDEX_ROOT_PATH", "../");
 
                    // set the tab index path
                    define("TAB_PATH", "./"); 

                    // include (and start) boris localhost browser
                    include_once(INCLUDE_PATH."/index.php");
            }
        } 

    if ($loggedin !== true) {
    ?>      
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
     <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
            <title>root</title>     
            <meta http-equiv="imagetoolbar" content="no" />
            <meta name="MSSmartTagsPreventParsing" content="true" />        
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <meta name="robots" content="noindex,nofollow" />
 
            <style media="all" type="text/css">                
                    body { 
                            font : 12px/1.5em Arial, sans-serif;
                    }
                
                    legend { 
                            display : none;
                    }
                
                    fieldset {
                             border : 0px;
                    }
                
                    form label {
                            float: left;
                            width: 80px;
                            margin-top: 3px;
                    }
 
                    form input.button {
                            border: 1px solid #000;
                            background-color : #ccc;
                            color : #000;
                            padding: 1px 5px;
                            margin-left : 10px;
                    }
 
                    .inputcontainer { 
                            margin-bottom : 5px;
                    } 
            </style>
 
    </head>
    <body>        <div id="horizon">
                    <div id="wrapper">
                            <div id="contentwrap">
                                    <h1>root</h1>
                            <form method="post" action="">
                                    <fieldset>
                                            <legend>Login</legend>
                                            <div class="inputcontainer">
                                                    <label for="u">Username</label>
                                                    <input type="text" id="u" name="u" />
                                            </div>
                                            <div class="inputcontainer">
                                                    <label for="p">Password</label>
                                                    <input type="text" id="p" name="p" />
                                                    <input class="button" type="submit" name="login" id="button" value="Log in" />
                                            </div>
                                    </fieldset>
                            </form>
                            </div>
                    </div>
            </div>
    </body>
    </html>
    <?php
    }
    ?>
