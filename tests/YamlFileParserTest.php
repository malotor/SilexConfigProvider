<?php

namespace malotor\ConfigProvider\tests;

use malotor\ConfigProvider\YamlFileParserException;
use PHPUnit\Framework\TestCase;
use malotor\ConfigProvider\YamlFileParser;
use org\bovigo\vfs\vfsStream;

class YamlFileParserTest extends TestCase
{
    private $filesystem;
    private $basePath;

    public function setUp()
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
        $config_dev_yml =
            <<<YAML
imports:
  - { resource: config.yml }
YAML;


        $this->filesystem = vfsStream::setup("root",null, [
            'config' => [
                'config.yml' => $config_yml,
                'config_dev.yml' => $config_dev_yml
            ]
        ]);

        $this->basePath = $this->filesystem->url("root");

    }

    /**
     * @test
     */
    public function it_should_fail_if_file_does_not_exists()
    {
        $this->expectException(YamlFileParserException::class);

        $parser = new YamlFileParser();

        $result = $parser->parse($this->basePath . '/noexists.yml');

    }

    /**
     * @test
     */
    public function it_should_parse_basic_yaml_file()
    {

        $parser = new YamlFileParser();

        $result = $parser->parse($this->basePath . '/config/config.yml');

        $this->assertEquals("sqlite", $result['database']['driver']);
        $this->assertTrue( $result['debug']);
        $this->assertFalse( $result['mock']);
    }

    /**
     * @test
     */
    public function it_should_import_yml_files()
    {

        $parser = new YamlFileParser();

        $result = $parser->parse($this->basePath . '/config/config_dev.yml');

        $this->assertEquals("sqlite", $result['database']['driver']);

    }
}
