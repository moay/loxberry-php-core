<?php


require_once "../loxberry_system.php";

class LBLog
{
    public static $VERSION = "1.4.1.1";

    public static function newLog($args)
    {
        global $stdLog;
        $newlog = new intLog($args);
        if ( ! $stdLog ) { $stdLog = $newlog; }
        return $newlog;
    }

    public static function get_notifications ($package = NULL, $name = NULL)
    {
        // error_log("get_notifications called.\n");

        global $lbpplugindir;

        $NOTIFHANDLERURL = "http://localhost:" . lbwebserverport() . "/admin/system/tools/ajax-notification-handler.cgi";

        $fields = array(
            'action' => 'get_notifications',
        );

        if (isset($package)) { $fields['package'] = $package; }
        if (isset($name)) { $fields['name'] = $name; }

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($fields)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($NOTIFHANDLERURL, false, $context);
        if ($result === FALSE) {
            error_log("get_notifications_html: Could not get notifications");
            return;
        }

        $jsonresult = json_decode($result, true);
        # var_dump($jsonresult);

        return ($jsonresult);

    }


    // get_notifications_html
    public static function get_notifications_html ($package = NULL, $name = NULL, $type = NULL, $buttons = NULL)
    {
        error_log("get_notifications_html called.\n");

        global $lbpplugindir;

        $NOTIFHANDLERURL = "http://localhost:" . lbwebserverport() . "/admin/system/tools/ajax-notification-handler.cgi";

        $fields = array(
            'action' => 'get_notifications_html',
        );

        if (isset($package)) { $fields['package'] = $package; }
        if (isset($name)) { $fields['name'] = $name; }
        if (isset($type)) { $fields['type'] = $type; }
        if (isset($buttons)) { $fields['buttons'] = $buttons; }


        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($fields)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($NOTIFHANDLERURL, false, $context);
        if ($result === FALSE) {
            error_log("get_notifications_html: Could not get notifications");
            return;
        }

        return $result;

    }

}
