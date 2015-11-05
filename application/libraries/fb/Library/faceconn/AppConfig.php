<?php
/**
 * This class is collection of methods for application's configuration.
 */

class AppConfig
{
    // Add STRING values from your Facebook application.
    private static $ApiId = '142425069114172';
    private static $Secret = '52a7c350e39c347d9699b57068a5d275';
    private static $AppName = 'FConn';
    private static $AppCanvasUrl = 'http://apps.facebook.com/fconndemolocal/';

    // EXAMPLE from demo application:
    //private static $ApiId = '142425069114172';
    //private static $Secret = '52a7c350e39c347d9699b57068a5d275';
    //private static $AppName = 'FConn';
    //private static $AppCanvasUrl = 'http://apps.facebook.com/fconndemolocal/';

    /**
     * Function gets the Facebook Application Id.
     * @return <string>
     */
    static function GetAppId() 
    {
            return self::$ApiId;
    }

    /**
     * Function gets the Facebook Application Secret Key.
     * @return <string>
     */
    static function GetSecret() 
    {
            return self::$Secret;
    }

    /**
     * Function gets the Facebook Application name
     * @return <string>
     */
    static function GetAppName()
    {
        return self::$AppName;
    }

     /**
     * Function gets the Facebook Application Canvas URL
     * @return <string>
     */
    static function GetAppCanvasUrl()
    {
        return self::$AppCanvasUrl;
    }

    /**
     * Function gets Facebook keys in array.
     * @return <array>
     */
    static function GetKeyArray()
    {
            return array('appId' => self::$ApiId, 'secret' => self::$Secret, 'cookie' => true);
    }
}
?>
