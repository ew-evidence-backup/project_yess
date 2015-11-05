<!-- Fixed navbar -->
<?php include 'includes/header.php'; ?>



<div style="background-color: #ccc;">
    <?php if (isset($_REQUEST['s']) && $_REQUEST['s'] == 'f') { ?>

        <div style="background-color: red;align-content: center; color:white;"><p>Login Failed!</p></div>

    <?php } ?>
    <form action="/login/submit" method="post">

        <table align="center" class="table" style="width: 500px;align-content: center;">
            
            
            <tr>
                
                <td>


                    
                </td>
                
                <td>

                    <?php //session_start(); ?>
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                    <html xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
                    <head>
                        <title>Facebook Login Button PHP class</title>
                        <meta name="description" content="PHP Example of creting Facebook Login Button using the Graph API." />
                        <link rel="stylesheet" type="text/css" href="styles.css" />
                        <?php //include 'HeaderScripts.php'; ?>
                    </head>
                    <body>
                    <?php
                    require_once 'application/libraries/fb/Library/facebook.php';
                    require_once 'application/libraries/fb/Library/faceconn/faceconn.php';
                    UseGraphAPI();
                    ?>

                    <table>
                        <tr>
                            <td style="width:20%"></td>
                            <td>
                                <table width="840px">
                                    <tr>
                                        <td>
                                            <a href="http://faceconn.com/demo/"></a>
                                        </td>
                                    </tr>
                                </table>

                                <table>
                                    <tr>
                                        <td valign="top">
                                            <?php //include "LinkList.php"; ?>
                                        </td>
                                        <td valign="top" style="width:770px">
                                            <div style="padding:5px; padding-top:3px">
                                                <!-- CONTENT DIV -->
                                                <form id="form1" action="UserDataPage.php">
                                                    <div class="contentdiv">


                                                        <br />
                                                        <?php
                                                        // creating new instance of Login Button
                                                        $login = new LoginButton();

                                                        // Optional: setting text and size
                                                        $login->SetText("Sign up with Facebook");
                                                        $login->SetSize("small");

                                                        // Optional: setting list of extended permissions
                                                        $login->SetPermissions("email, publish_stream");

                                                        // Optional: setting the form id which will be submitted
                                                        // on successfull login (redirect on User Data page)
                                                        $login->SetOnLoginSubmitForm("form1");

                                                        // Render commmand on the page
                                                        $login->Render();
                                                        ?>

                                                    </div>
                                                </form>

                                                <?php //include 'application/libraries/fb/Demo Website/SessionCheck.php'; ?>
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
                </td>
                
            </tr>

            <tr>

               <td>


               </td>

                <td>

                    or

                </td>
            </tr>

            <tr>

                <td>

                    Email


                </td>

                <td>

                    <input type="text" name="Email"/>

                </td>

            </tr>
            <tr>

                <td>

                    Password


                </td>

                <td>

                    <input type="password" name="Password"/>

                </td>

            </tr>
            <tr>

                <td>


                </td>
                <td>
                    <input type="submit" name="login" value="Login"/>
                </td>
            </tr>

            <tr>

                <td>



                </td>

                <td>

                    <a href="/fp">Forgot your password?</a>

                </td>

            </tr>
        </table>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="../../../assets/js/bootstrap.min.js"></script>
</body>
</html>
