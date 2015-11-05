<?php
/**
 * This class is used to provide unique ID for controls inside one page.
 */
class GlobalCounter
{
	/**
     * Function returns new unique ID.
     */
    public static function GetCount()
    {
        self::$count += 1;
        return self::$count;
    }
    private static $count = 0;
}
?>
