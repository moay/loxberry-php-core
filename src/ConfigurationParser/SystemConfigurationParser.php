<?php

namespace LoxBerry\ConfigurationParser;

use LoxBerry\System\PathProvider;

/**
 * Class SystemConfigurationParser.
 */
class SystemConfigurationParser
{
    /** @var PathProvider */
    private $pathProvider;

    /**
     * SystemConfigurationParser constructor.
     *
     * @param PathProvider $pathProvider
     */
    public function __construct(PathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;
    }
}
