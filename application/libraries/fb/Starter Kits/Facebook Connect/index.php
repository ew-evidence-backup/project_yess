<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
       <title>Facebook Connect Demo web site</title>
       <meta name="description" content="Facebook Connet Graph API classes for PHP development." />
       <link rel="stylesheet" type="text/css" href="styles.css" />
    </head>
     <body>
        <?php
            require_once 'facebook.php';
            require_once 'faceconn/faceconn.php';
            UseGraphAPI();
        ?>
        <form id="form1">
             <center>
                <a href="http://faceconn.com" id="bannerLink" runat="server"><img src="images/banner.png" alt="Facebook ASP.NET Demo" /></a>
                <br />   
                 <?php
                    function ShowLoginButton()
                    {
                        $login = new LoginButton();
                        $login->SetPermissions("email");
                        $login->SetOnLoginSubmitForm("form1");
                        $login->Render();
                    }
                 
                    // create facebook object.
                    $facebook = new Facebook(AppConfig::GetKeyArray());                    

                    try
                    {
                        // create facebook user
                        $facebookUser = $facebook->getUser();

                        // check if user if connected
                        if ($facebookUser) {
                            // get user data
                            $loggedUser = $facebook->api('/me');
                            echo "<b>User data:</b><br />";
                            echo "<br /><b>User ID:</b> " . $loggedUser['id'];
                            echo "<br /><b>First name:</b> " . $loggedUser['first_name'];
                            echo "<br /><b>Last name:</b> " . $loggedUser['last_name'];
                            echo "<br /><b>Email:</b> " . $loggedUser['email'];
                            echo "<br /><br />";
                            $logout = new LogoutButton();
                            $logout->SetImage("images/fb_logout.png");
                            $logout->SetOnLogoutSubmitForm("form1");
                            $logout->Render();
                        }
                        else
                        {
                            ShowLoginButton();
                        }
                    }
                    catch (Exception $ex)
                    {
                        ShowLoginButton();
                    }
                ?>
                <br /><br />
                <div class="contentdiv" style="width:400px">
                    This starter kit is used to demonstrate the implementation of authorization inside Facebook Connect application. To see all 15 components from the Faceconn Toolkit 
                    and the other two starter kits click <a href="http://faceconn.com/demo/" target="_blank">Faceconn Demo</a>.
                </div>
                <br />
                <a href="http://faceconn.com/licensing-facebook-connect" target="_black"><img src="images/downloadbutton.png" alt="" /></a>
            </center>

        </form>

    </body>
</html>