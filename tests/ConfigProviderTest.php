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
    public function it_could_be_registered()
    {
        $app = new Application();

        $app->register(new ConfigProvider(), array(
        ));

    }
}
