<?php

namespace LoxBerry\Communication;

/**
 * Class Notifications.
 */
class Notifications
{
    const RETRIEVE_ALL = 'all';
    const RETRIEVE_ERRORS = 'error';
    const RETRIEVE_INFO = 'info';

    const SEVERITY_LEVEL_INFO = 6;
    const SEVERITY_LEVEL_ERROR = 3;

    public function get(): array
    {
    }

    public function getHtml(string $type = 'all'): string
    {
    }

    public function notify(): bool
    {
    }
}
