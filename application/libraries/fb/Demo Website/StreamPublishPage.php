<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
       <title>Facebook Stream Publish PHP class</title>
       <meta name="description" content="PHP Example of creting Facebook Stream Publish using the Graph API." />
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
                                    <div class="contentdiv" action="#">

                                       <h1 style="font-size:18px">Facebook Stream Publish</h1>

                                       <p style="text-align: justify">
                                        This control is used to publish stories on user profile, friends' profiles or the Facebook page. The example shows how easily you can publish a story on a user profile. Stream
                                        publish has 4 possible media types for publish: image, video, flash and MP3.  The control also has possibility to set JavaScript code which will be executed and form id which
                                        will be submitted after story is successfully published.
                                        <br /><br />
                                        For all details about the control, with descriptions of all optional properties and ways of how to configure all 4 media types, please visit:
                                        <b><a href="http://faceconn.com/facebook-stream-publish-php">Facebook Stream Publish Tutorial</a></b>.
                                        <br /><br />
                                        </p>

                                        <?php
                                            // create instance of stream publich cotrols
                                            $publish = new StreamPublish();

                                            // Optional: set story name
                                            $publish->SetName("Facebook Connect PHP Toolkit");

                                            // Optional: set URL
                                            $publish->SetNameUrl("http://faceconn.com");

                                            // Optional: set description
                                            $publish->SetDescription("Facebook Connect PHP toolkit is PHP library specialized for development of Facebook Connect websites and Facebook iFrame applications. Both types of web applications are based on the same technology - Facebook Graph API and XFBML. Facebook Connect PHP Toolkit is a set of PHP classes used to provide an easy and fast way to integrate the most common Facebook UI features (like Invite Friends, Stream Publish, Login Button, Extended Permissions...) using the pure PHP code. Each control is documented in detail, with descriptions of all properties, code examples and demo pages. The value of the toolkit is not in its library alone, it is also an excellent resource of knowledge. By reading the source code of examples, you can spare big amounts of your precious time, and jump up in the learning curve, avoiding days of googling and reading forums.");

                                            // Optional: set css class of publish button
                                            $publish->SetCssClass("faceconn_button");
                                            $publish->SetCssStyle("width:170px");

                                            // Optional: set video as media
                                            $publish->SetMedia(new ImageMedia("http://images.vatlab.net/logo.png", "http://faceconn.com"));

                                            // Optional: add property
                                            $publish->AddPropery(new Property("Product Name", "Faceconn Toolkit"));
                                            $publish->AddPropery(new LinkedProperty("Product Demo", "Faceconn Toolkit Demo", "http://faceconn.com/demo/"));

                                            // Optional: add action link
                                            $publish->AddActionLink(new ActionLink("Faceconn", "http://faceconn.com"));

                                            // render component on page
                                            $publish->Render();
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




