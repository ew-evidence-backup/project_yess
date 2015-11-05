
<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
       <title>Facebook Send Button PHP class</title>
       <meta name="description" content="PHP Example of creting Facebook Send Button using the Graph API." />
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

                                         <h1 style="font-size:18px">Facebook Send Button</h1>

                                       <p style="text-align: justify">
                                        Facebook Send Button is used by users of website to share interesting content with their friends 
                                        by selecting them from friends list. This will result in sending the message to friends' inbox. 
                                        There is also an option to send it to the wall of group that user is fan of, or any email address.
                                        <br /><br />
                                        For all details about the control, with descriptions of all optional properties, please visit:<br />
                                        <b><a href="http://faceconn.com/facebook-send-button-php">Facebook Send Button Tutorial</a></b>.
                                        <br />
                                        </p>

                                        <?php
                                            // create new instance of the control
                                            $send = new SendButton();

                                            // Optional: set external URL
                                            $send->SetUrl("http://faceconn.com");

                                            // render the control on the page
                                            $send->Render();
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

