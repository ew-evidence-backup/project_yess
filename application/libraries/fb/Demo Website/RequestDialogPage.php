<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
      <title>Facebook Request Dialog PHP class</title>
       <meta name="description" content="PHP Example of creting Facebook Request Dialog using the Graph API." />
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

                                         <h1 style="font-size:18px">Facebook Request Dialog</h1>

                                       <p style="text-align: justify">
                                        Facebook Request Dialog is used to invite friends to start using an application, or to send request 
                                        for some specific action to application users. It is implemented as button and link on which user has
                                        to click to open the request dialog. There is also an option to open it automatically on page load. 
                                        <br /><br />
                                        For all details about the control, with descriptions of all optional properties, please visit:<br />
                                        <b><a href="http://faceconn.com/facebook-request-dialog-php">Facebook Request Dialog Tutorial</a></b>.
                                        <br /><br />
                                        </p>

                                        <?php
                                            // create instance of request dialog cotrols
                                            $request = new RequestDialog();

                                            // set message
                                            $request->SetMessage("Faceconn toolkit is a set of PHP classes used to provide an easy and fast way to integrate the most common Facebook UI features using the pure PHP code for development of Facebook Connect websites, Facebook Canvas apps and Facebook Page apps.");

                                            // Optional: set css class of publish button
                                            $request->SetCssClass("faceconn_button");
                                            $request->SetCssStyle("width:170px");
                                            
                                            // Optional: set form to submit
                                            $request->SetOnSendRequestSubmitForm("form1");

                                            // render the control on the page
                                            $request->Render();
                                            
                                            if (isset($_GET["invitedFriends"])) {
                                                echo "<br /><br />Invited Friends IDs = " . $_GET["invitedFriends"];
                                            }
                                            
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



