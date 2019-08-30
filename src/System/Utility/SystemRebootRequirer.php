<?php

namespace LoxBerry\System\Utility;

use LoxBerry\System\PathProvider;
use LoxBerry\System\Paths;

/**
 * Class SystemInformation.
 */
class SystemRebootRequirer
{
    /** @var PathProvider */
    private $pathProvider;

    /**
     * SystemInformation constructor.
     *
     * @param PathProvider $pathProvider
     */
    public function __construct(PathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;
    }

    /**
     * @param string $message
     *
     * @return bool
     */
    public function requireReboot(string $message = 'A reboot was requested'): bool
    {
        $rebootRequiredFileName = $this->pathProvider->getPath(Paths::PATH_REBOOT_REQUIRED_FILE);
        if (!file_exists($rebootRequiredFileName) || !is_writable($rebootRequiredFileName)) {
            throw new \RuntimeException('Cannot write reboot required state, file does not exist or is not writable');
        }

        return false !== file_put_contents($rebootRequiredFileName, $message.PHP_EOL, FILE_APPEND);
    }
}
