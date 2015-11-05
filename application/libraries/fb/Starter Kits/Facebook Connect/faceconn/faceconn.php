<?php
    if ($_SERVER["SERVER_NAME"] == "localhost")
    {
        Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
        Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = 2;
    }

    require_once  'faceconn/AppConfig.php';

//    if (AppConfig::GetAppId() == null)
//    {
//        throw new Exception("AppId key is not set. Please edit AppConfig.php file and set value from your application");
//    }
//    if (AppConfig::GetSecret() == null)
//    {
//        throw new Exception("App secret key is not set. Please edit AppConfig.php file and set value from your application");
//    }
//    if (AppConfig::GetAppName() == null)
//    {
//        throw new Exception("App name is not set. Please edit AppConfig.php file and set value from your application");
//    }
//    if (AppConfig::GetAppCanvasUrl() == null)
//    {
//        throw new Exception("App Canvas URL is not set. Please edit AppConfig.php file and set value from your application");
//    }
//
    require_once 'faceconn/GraphApi.php';
    require_once 'faceconn/LoginButton.php';
    require_once 'faceconn/CustomLoginButton.php';
    require_once 'faceconn/RequestDialog.php';
    require_once 'faceconn/InviteAllFriends.php';
    require_once 'faceconn/StreamPublish.php';
    require_once 'faceconn/Permissions.php';
    require_once 'faceconn/LikeButton.php';
    require_once 'faceconn/SendButton.php';
    require_once 'faceconn/LikeBox.php';
    require_once 'faceconn/Comments.php';
    require_once 'faceconn/Bookmark.php';
    require_once 'faceconn/Recommendations.php';
    require_once 'faceconn/LogoutButton.php';
    require_once 'faceconn/LikeGate.php';
    require_once 'faceconn/ResizeCanvas.php';
?>
