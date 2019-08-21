<?php

namespace LoxBerry\System;

/**
 * Class PluginDatabase.
 */
class PluginDatabase
{
    /** @var PathProvider */
    private $pathProvider;

    /**
     * PluginDatabase constructor.
     *
     * @param PathProvider $pathProvider
     */
    public function __construct(PathProvider $pathProvider)
    {
        $this->pathProvider = $pathProvider;
    }
}
