<?php
require_once 'faceconn/AppConfig.php';

/**
 * Call this function after <body> tag on each page to enable GraphAPI JavaScript SDK.
 * You can find all language codes at:
 * http://www.facebook.com/translations/FacebookLocales.xml
 */
function UseGraphAPI($languageCode = "en_US")
{
    echo "<div id='fb-root'></div>\n";
    echo "<script>\n";
    echo "var graphApiInitialized = false;\n";
    echo "  window.fbAsyncInit = function() {\n";
    echo "    FB.init({\n";
    echo "      appId  : '" . AppConfig::GetAppId() ."',\n";
    echo "      status : true, // check login status\n";
    echo "      cookie : true, // enable cookies to allow the server to access the session\n";
    echo "      xfbml  : true,  // parse XFBML\n";
    echo "      oauth: true";
    echo "    });\n";
    echo "    graphApiInitialized = true;\n";
    echo "  };\n";
    echo "  (function() {\n";
    echo "    var e = document.createElement('script');\n";
    echo "    e.src = document.location.protocol + '//connect.facebook.net/" . $languageCode . "/all.js';\n";
    echo "    e.async = true;\n";
    echo "    document.getElementById('fb-root').appendChild(e);\n";
    echo "  }());\n";
    
    echo "</script>\n";
}


