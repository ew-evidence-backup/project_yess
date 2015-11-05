<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
       <title>Facebook Graph API PHP example</title>
       <meta name="description" content="PHP Example of getting Facebook data using the Graph API." />
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

                                         <h1 style="font-size:18px">Facebook Graph API Example</h1>

                                         <p style="text-align: justify">
                                        This page demonstrates how to use Facebook Graph API to get some data from Facebook. First part of PHP code gets basic data
                                        of logged user and write it to page. Second part gets data about user's friends and write their names.
                                        <br />
                                        </p>

                                        <?php
                                            // create facebook object.
                                            $facebook = new Facebook(AppConfig::GetKeyArray());

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

                                                // get first 5 friends
                                                echo "<br /><br /><br /><b>Friends:</b><br /><br />";
                                                $friends = $facebook->api('/me/friends');
                                                $friendsData = $friends['data'];

                                                for ($i = 0; $i < sizeof($friendsData) && $i < 5; $i++)
                                                {
                                                    $friend = $friendsData[$i];
                                                    echo $friend['name'] . ", ";
                                                }
                                            }
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


