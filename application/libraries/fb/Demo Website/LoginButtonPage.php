<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
      <title>Facebook Login Button PHP class</title>
       <meta name="description" content="PHP Example of creting Facebook Login Button using the Graph API." />
       <link rel="stylesheet" type="text/css" href="styles.css" />
       <?php include 'HeaderScripts.php'; ?>
    </head>
     <body>
        <?php
            require_once 'facebook.php';
            require_once 'faceconn/faceconn.php';
            UseGraphAPI();
        ?>
         
        <table>
        <tr>
            <td style="width:20%"></td>
            <td>
               <table width="840px">
                <tr>
                    <td>
                        <a href="http://faceconn.com/demo/"><img src="images/banner.png" alt="Facebook PHP Demo" /></a>
                    </td>
                </tr>
               </table>
                 
               <table>
                    <tr>
                        <td valign="top">
                            <?php include "LinkList.php"; ?>
                        </td>
                        <td valign="top" style="width:770px">
                            <div style="padding:5px; padding-top:3px">
                                <!-- CONTENT DIV -->
                                <form id="form1" action="UserDataPage.php">
                                    <div class="contentdiv">

                                         <h1 style="font-size:18px">Facebook Login Button</h1>

                                         <p style="text-align: justify">
                                            Login Button control is used to connect a web site to the Facebook and allow it to use the Facebook API. It also enables the user that once he is logged, all controls from this list will 
                                            work without additional logging. It is also possible to define JavaScript code which will be executed after user is successfully logged, or form id which will be submitted which allows
                                            redirection to another page, or resubmitting the current page.

                                            <br /><br />
                                            For all details about the control, with descriptions of all optional properties, please visit: <br />
                                            <b><a href="http://faceconn.com/facebook-login-button-php">Facebook Login Button Tutorial</a></b>.</p>
                                            <br />

                                            <b>Example:</b><br />
                                            <p style="text-align: justify">
                                            The example demonstrates login button configured with all optional parameters. Below the login button is code used for configuration. Press the login button to see how it works. 
                                            When you press it, popup window will show up, and you have to enter Facebook credentials. After your successful login, you will be asked to allow the usage of extended permissions for the 
                                            website (this example shows setting of email permission). After confirmation you will be redirected to another page where your basic profile data will be shown. There is
                                            also an option to set JavaScript code which will be executed after successful login.
                                            </p>
                                            <br />
                                            <?php
                                                // creating new instance of Login Button
                                                $login = new LoginButton();

                                                // Optional: setting text and size
                                                $login->SetText("Sign up with Facebook");
                                                $login->SetSize("small");

                                                // Optional: setting list of extended permissions
                                                $login->SetPermissions("email, publish_stream");

                                                // Optional: setting the form id which will be submitted
                                                // on successfull login (redirect on User Data page)
                                                $login->SetOnLoginSubmitForm("form1");

                                                // Render commmand on the page
                                                $login->Render();
                                            ?>
                                        <br /><br />
                                    </div>
                                </form>
                                <br />
                                <?php include 'SessionCheck.php'; ?>
                                <div style="width:605px">
                                    <center>
                                        <br />
                                        <a href="http://faceconn.com" id="tutorialbtn" runat="server" target="_black"><img src="images/tutorialbutton.png" alt="" /></a>
                                        <a href="http://faceconn.com/facebook-starter-kit-php" target="_black"><img src="images/starterkitsbutton.png" alt="" /></a>
                                        <a href="http://faceconn.com/licensing-facebook-connect" target="_black"><img src="images/downloadbutton.png" alt="" /></a>
                                    </center>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:20%"></td>
        </tr>
      </table>
    </body>
</html>




