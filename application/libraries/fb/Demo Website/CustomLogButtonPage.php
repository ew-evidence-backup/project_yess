<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
       <title>Facebook Custom Login Button PHP class</title>
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
                                <form id="form1" action="#">
                                    <div class="contentdiv">

                                         <h1 style="font-size:18px">Facebook Custom Login Button</h1>

                                           <p style="text-align: justify">
                                            CustomLoginButton is used  for the same purpose as LoginButton. Only difference
                                            is that its appearance is not predefined, but it can be defined with CSS. Login Button 
                                            class is used to connect a web site to the Facebook and to allow it to use the Facebook API. 
                                            The example bellow demonstrates login button without style, with defined CSS, as a link, and as an image.

                                            <br /><br />
                                            For all details about the control, with descriptions of all optional properties, please visit:<br />
                                            <b><a href="http://faceconn.com/custom-login-button">Facebook Custom Login Button Tutorial</a></b>.</p>
                                            <br />

                                            <?php
                                                $login = new CustomLoginButton();
                                                $login->SetCommandText("Connect with Facebook");
                                                $login->SetOnLoginJavaScript("alert('loggin succesfull');");
                                                echo "Login button without CSS styles<br /><br />\n";
                                                $login->Render();

                                                echo "<br /><br /><br />Login button with CSS styles<br /><br />\n";
                                                $login->SetCssClass("blue command_button");
                                                $login->Render();

                                                echo "<br /><br /><br />Login button as link without style<br /><br />\n";
                                                $login->SetCommandType("link");
                                                 $login->SetCssClass("");
                                                $login->Render();

                                                echo "<br /><br /><br />Login button as image<br /><br />\n";
                                                $login->SetImage("images/fblogin.png");
                                                $login->Render();
                                            ?>
                                        <br />
                                        <br />
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

