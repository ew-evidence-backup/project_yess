<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
       <title>Facebook Connect Demo web site</title>
       <meta name="description" content="Facebook Connet Graph API classes for PHP development." />
       <link rel="stylesheet" type="text/css" href="styles.css" />
    </head>
     <body>
        <?php
            require_once 'facebook.php';
            require_once 'faceconn/faceconn.php';
            UseGraphAPI();
        ?>
        <form id="form1">
             <center>
                <a href="http://faceconn.com" id="bannerLink" runat="server"><img src="images/banner.png" alt="Facebook ASP.NET Demo" /></a>
                <br />   
                <?php
                    $likeGate = new LikeGate();
                    if ($likeGate->IsUserFan())
                    {
                        echo "You are fan of the page";
                    }
                    else
                    {
                        echo "You are NOT fan of the page. Please click on like button above to become a fan.";
                    }
                ?>     
                <br /><br />
                <div class="contentdiv" style="width:400px">
                    This starter kit is used to demonstrate the implementation of Like Gate. To see all 15 components from the Faceconn Toolkit 
                    and the other two starter kits click <a href="http://faceconn.com/demo/" target="_blank">Faceconn Demo</a>.

                </div>
                <br />
                <a href="http://faceconn.com/licensing-facebook-connect" target="_black"><img src="images/downloadbutton.png" alt="" /></a>
            </center>

        </form>

    </body>
</html>