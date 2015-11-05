<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
      <title>Facebook Logout PHP Demo web site</title>
       <meta name="description" content="Facebook Connet Logout Button." />
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
                                <form id="form1" action="LogoutPage.php">
                                    <div class="contentdiv">

                                        <h1 style="font-size:18px">Facebook Logout Button</h1>

                                        <p style="text-align: justify">
                                        Facebook Logout button is used to break application connection with Facebook. After logout is made,
                                        it is impossible to get any more data from Facebook or to use some of the UI features implemented in the toolkit.
                                        Logout button can be implemented as button, link, image, or auto open on page load.

                                        <br /><br />
                                        For all details about the control, with descriptions of all optional properties, please visit:<br />
                                        <b><a href="http://faceconn.com/facebook-logout-button">Facebook Logout Button Tutorial</a></b>.</p>
                                        <br />

                                       <?php
                                            $logout = new LogoutButton();
                                            $logout->SetOnLogoutJavaScript("alert('Logout executed')");
                                            $logout->SetOnLogoutSubmitForm("form1");
                                            $logout->SetCssClass("faceconn_button");
                                            $logout->SetCssStyle("width:170px");
                                            $logout->Render();
                                          ?>
                                        <br /><br />
                                    </div>
                                    <br />
                                    <?php include 'SessionCheck.php'; ?>
                                </form>
                                
                                
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


