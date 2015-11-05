<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
      <title>Facebook Like Button PHP class</title>
       <meta name="description" content="PHP Example of creting Facebook Like Button using the Graph API." />
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

                                         <h1 style="font-size:18px">Facebook Like Button</h1>

                                       <p style="text-align: justify">
                                        Like Button is used to enable users of website to share interesting content with their friends. When user clicks on like button inside you page, 
                                        a story will appear in the user's friends' News Feed, with link back to your site. This increases number of visitors to your site. 
                                        Following example shows 3 types of layout of like button: 'standard', 'button_count' and 'box_count'. Last button is standard 
                                        type with 'send' option enabled. 
                                        <br /><br />
                                        For all details about the control, with descriptions of all optional properties, please visit:<br />
                                        <b><a href="http://faceconn.com/facebook-like-button-php">Facebook Like Button Tutorial</a></b>.
                                        <br />
                                        </p>

                                        <?php
                                            // create new instance of the control
                                            $like = new LikeButton();

                                            // Optional: set external URL
                                            $like->SetUrl("http://faceconn.com");

                                            // render the control on the page
                                            $like->Render();
                                            echo "<br /><br />";

                                            // set layout to button_count
                                            $like->SetLayout("button_count");
                                            $like->Render();
                                            echo "<br /><br />";

                                            // set layout to box_count
                                            $like->SetLayout("box_count");
                                            $like->Render();
                                            echo "<br /><br />";

                                            // set layout to standard and show send button
                                            $like->SetLayout("standard");
                                            $like->SetSend(true);
                                            $like->Render();
                                        ?>                                    
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
