<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
       <title>Facebook Extended Permissions PHP class</title>
       <meta name="description" content="PHP Example of creting Facebook Extended Permissions using the Graph API." />
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

                                         <h1 style="font-size:18px">Facebook Extended Permissions</h1>

                                        <p style="text-align: justify">
                                        Permission control is used to allow the user to add extended permissions, like sending upload images and status updates, to Facebook application. It is possible
                                        to define JavaScript code which will be executed and form id which will be submitted after permissions are confirmed by user. This example shows 
                                        Permission class for adding 2 permissions (getting user email and getting user activities).

                                        <br /><br />
                                        For all details about the control, with descriptions of all optional properties, please visit:<br />
                                        <b><a href="http://faceconn.com/facebook-extended-permissions-php">Facebook Extended Permissions Tutorial</a></b>.
                                        <br /><br />
                                        </p>

                                        <?php
                                            // create new instance of Permssions control
                                            $perms = new Permissions();

                                            // set permissions on email and SMS
                                            $perms->SetPermissions("email, user_activities");

                                            // Optional: set CSS class and style for add permissions button
                                            $perms->SetCssClass("faceconn_button");
                                            $perms->SetCssStyle("width:170px");

                                            // Optional: set JavaScript code for confirmation
                                            $perms->SetOnConfirmJavaScript("alert('permissions added')");

                                            // Optional: set submit form id for confirmation
                                            $perms->SetOnConfirmSubmitForm("form1");

                                            // render the control on the page
                                            $perms->Render();
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


