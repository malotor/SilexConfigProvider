<?php

namespace malotor\ConfigProvider\tests;

use malotor\ConfigProvider\ConfigProvider;
use PHPUnit\Framework\TestCase;

use Silex\Application;

class ConfigProviderTest extends TestCase
{

    /**
     * @test
     */
    public function it_could_load_a_yaml_config_file()
    {

        $config_yml =
            <<<YAML
#Common config for all enviroments
debug: true
mock: false
# Database connections
database:
    driver:   sqlite
    path:     memory
YAML;


        $configFilePath = FileSystemFactory::create($config_yml);

        $app = new Application();

        $app->register(new ConfigProvider($configFilePath), array());

        $this->assertEquals("sqlite", $app['config']['database']['driver']);

    }
}
