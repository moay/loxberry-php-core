<?php
function notify ($package, $name, $message, $error = false)
{
    global $lbpplugindir;

    $NOTIFHANDLERURL = "http://localhost:" . lbwebserverport() . "/admin/system/tools/ajax-notification-handler.cgi";
    // error_log "Notifdir: " . LBLog::$notification_dir . "\n";
    if (! $package || ! $name || ! $message) {
        error_log("Notification: Missing parameters\n");
        return;
    }

    if ($error == True) {
        $severity = 3;
    } else {
        $severity = 6;
    }

    $fields = array(
        'action' => 'notifyext',
        'PACKAGE' => $package,
        'NAME' => $name,
        'MESSAGE' => $message,
        'SEVERITY' => $severity,
    );

    if (isset($lbpplugindir)) {
        $fields['_ISPLUGIN'] = 1;
    } else {
        $fields['_ISSYSTEM'] = 1;
    }

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($fields)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($NOTIFHANDLERURL, false, $context);
    if ($result === FALSE) { /* Handle error */ }

}

function notify_ext ($fields)
{
    global $lbpplugindir;

    $NOTIFHANDLERURL = "http://localhost:" . lbwebserverport() . "/admin/system/tools/ajax-notification-handler.cgi";
    // error_log "Notifdir: " . LBLog::$notification_dir . "\n";
    if ( ! isset($fields['PACKAGE']) || ! isset($fields['NAME']) || ! isset($fields['MESSAGE']) ) {
        error_log("Notification: Missing parameters\n");
        return;
    }

    if ( ! isset($fields['SEVERITY']) ) {
        $severity = 6;
    }

    $fields['action'] = "notifyext";

    if (isset($lbpplugindir)) {
        $fields['_ISPLUGIN'] = 1;
    } else {
        $fields['_ISSYSTEM'] = 1;
    }

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($fields)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($NOTIFHANDLERURL, false, $context);
    if ($result === FALSE) { /* Handle error */ }

}

$stdLog = NULL;

function LOGSTART ($msg="")
{
    global $stdLog;
    if (!isset($stdLog)) { create_temp_logobject(); }
    $stdLog->LOGSTART($msg);
}

function LOGDEB ($msg)
{
    global $stdLog;
    if (!isset($stdLog)) { create_temp_logobject(); }
    $stdLog->DEB($msg);
}

function LOGINF ($msg)
{
    global $stdLog;
    if (!isset($stdLog)) { create_temp_logobject(); }
    $stdLog->INF($msg);
}

function LOGOK ($msg)
{
    global $stdLog;
    if (!isset($stdLog)) { create_temp_logobject(); }
    $stdLog->OK($msg);
}

function LOGWARN ($msg)
{
    global $stdLog;
    if (!isset($stdLog)) { create_temp_logobject(); }
    $stdLog->WARN($msg);
}

function LOGERR ($msg)
{
    global $stdLog;
    if (!isset($stdLog)) { create_temp_logobject(); }
    $stdLog->ERR($msg);
}

function LOGCRIT ($msg)
{
    global $stdLog;
    if (!isset($stdLog)) { create_temp_logobject(); }
    $stdLog->CRIT($msg);
}

function LOGALERT ($msg)
{
    global $stdLog;
    if (!isset($stdLog)) { create_temp_logobject(); }
    $stdLog->ALERT($msg);
}

function LOGEMERG ($msg)
{
    global $stdLog;
    if (!isset($stdLog)) { create_temp_logobject(); }
    $stdLog->EMERG($msg);
}

function LOGEND ($msg = "")
{
    global $stdLog;
    if (!isset($stdLog)) { create_temp_logobject(); }
    $stdLog->LOGEND($msg);
}

function LOGTITLE ($title)
{
    global $stdLog;
    if (!isset($stdLog)) { return $title; }
    $stdLog->logtitle($title);
}

function create_temp_logobject()
{
    global $stdLog;
    global $lbpplugindir;
    if (! defined($lbpplugindir)) {
        $package = basename(__FILE__, '.php');
    } else {
        $package = $lbpplugindir;
    }
    $stdLog = LBLog::newLog( [
        "package" => $package,
        "name" => "PHPLog",
        "stderr" => 1,
        "nofile" => 1,
        "addtime" => 1,
    ] );
}
