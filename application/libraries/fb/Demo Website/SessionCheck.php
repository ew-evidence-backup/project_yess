<?php
    // create facebook object.
    $facebook = new Facebook(AppConfig::GetKeyArray());
    

    // create facebook session
    $facebookUser = $facebook->getUser();
    if ($facebookUser)
    {
        try
        {
            $loggedUser = $facebook->api('/me');
        }
        catch (Exception $ex)
        {
            $facebookUser = null;
        }
    }
    
    // check if user if connected
    if (! $facebookUser) {
        echo '<div class="contentdiv" style="height:78px;" id="errorDiv" runat="server" visible="false">';
        echo '    <center>';
        echo '        Application is not yet authorized or connection with Facebook is not established. Press the login button.<br />';
        echo '        <table>';
        echo '            <tr>';
        echo '                <td>';
        echo '                   <img id="animate_arrow" src="images/agt_forward.png" />';
        echo '                </td>';
        echo '                <td style="width:10px" />';
        echo '                <td>';
        $login = new CustomLoginButton();
        $login->SetImage("images/fb-connect-large.png");
        $login->SetOnLoginSubmitForm("form1");
        $login->Render();                          
        echo '                </td>';
        echo '                <td style="width:75px" />';
        echo '            </tr>';
        echo '        </table>';
        echo '    </center>';
        echo '</div>';
    }
    else
    {
        echo '<div class="contentdiv" style="height:65px" id="okDiv" runat="server" visible="false">';
        echo '    <table>';
        echo '        <tr>';
        echo '            <td valign="top">';
        echo '                <img src="images/apply.png" alt="" />';
        echo '            </td>';
        echo '            <td style="width:20px"></td>';
        echo '            <td valign="top" style="text-align:justify">';
        echo '                Application is authorized, and connection to Facebook is established. All components';
        echo '                should work properly. If you have any issues with this online demo application, please contact us by sending an email, with issue description, at';
        echo '                <a href="mailto:support@vatlab.com">support@vatlab.com</a>';
        echo '            </td>';
        echo '        </tr>';
        echo '    </table>';
        echo '</div>    ';
    }
?>