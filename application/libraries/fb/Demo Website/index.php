<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
       <title>Facebook Connect Demo web site</title>
       <meta name="description" content="Facebook Connet Graph API classes for PHP development." />
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
                                <form id="form1">
                                    <div class="contentdiv">

                                         <h1 style="font-size:18px">Faceconn Toolkit Demo</h1>

                                        <br />
                                        <div style="padding-left:10px">
                                        <h3 style="font-size:14px; font-weight:normal"><b>1.</b> This Demo application contains examples of using <b>15 Facebook PHP</b> classes</h3>
                                        <br />
                                        <h3 style="font-size:14px; font-weight:normal"><b>2.</b> <b>Source code</b> of the Demo application is included in all license packages</h3>
                                        <br />
                                        <h3 style="font-size:14px; font-weight:normal"><b>3.</b> All license packages include <b>free email support and updates</b> in 12 months from purchase</h3>
                                        </div>
                                        <br />
                                        <div style="padding:3px;padding-left:24px;padding-top:6px">
                                            <button class="faceconn_button" style="width:150px;" onclick="window.location='LoginButtonPage.php'">Continue</button>
                                        </div>
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