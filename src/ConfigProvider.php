<?php

namespace malotor\ConfigProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use malotor\ConfigProvider\YamlFileParser;

class ConfigProvider implements ServiceProviderInterface
{

    private $configFile;

    /**
     * ConfigProvider constructor.
     * @param $configFile
     */
    public function __construct($configFile)
    {
        $this->configFile = $configFile;
    }


    public function register(Container $pimple)
    {
        // TODO: Implement register() method.

        $parser = new YamlFileParser();

        $config = $parser->parse($this->configFile, $pimple);

        $pimple['config'] = $config;
    }
}