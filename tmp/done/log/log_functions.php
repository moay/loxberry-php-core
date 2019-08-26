<?php

function notify($package, $name, $message, $error = false)
{
    global $lbpplugindir;

    $NOTIFHANDLERURL = 'http://localhost:'.lbwebserverport().'/admin/system/tools/ajax-notification-handler.cgi';
    // error_log "Notifdir: " . LBLog::$notification_dir . "\n";
    if (!$package || !$name || !$message) {
        error_log("Notification: Missing parameters\n");

        return;
    }

    if (true == $error) {
        $severity = 3;
    } else {
        $severity = 6;
    }

    $fields = [
        'action' => 'notifyext',
        'PACKAGE' => $package,
        'NAME' => $name,
        'MESSAGE' => $message,
        'SEVERITY' => $severity,
    ];

    if (isset($lbpplugindir)) {
        $fields['_ISPLUGIN'] = 1;
    } else {
        $fields['_ISSYSTEM'] = 1;
    }

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($fields),
        ],
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($NOTIFHANDLERURL, false, $context);
    if (false === $result) { /* Handle error */
    }
}

function notify_ext($fields)
{
    global $lbpplugindir;

    $NOTIFHANDLERURL = 'http://localhost:'.lbwebserverport().'/admin/system/tools/ajax-notification-handler.cgi';
    // error_log "Notifdir: " . LBLog::$notification_dir . "\n";
    if (!isset($fields['PACKAGE']) || !isset($fields['NAME']) || !isset($fields['MESSAGE'])) {
        error_log("Notification: Missing parameters\n");

        return;
    }

    if (!isset($fields['SEVERITY'])) {
        $severity = 6;
    }

    $fields['action'] = 'notifyext';

    if (isset($lbpplugindir)) {
        $fields['_ISPLUGIN'] = 1;
    } else {
        $fields['_ISSYSTEM'] = 1;
    }

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($fields),
        ],
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($NOTIFHANDLERURL, false, $context);
    if (false === $result) { /* Handle error */
    }
}
