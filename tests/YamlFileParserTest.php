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


    }

    /**
     * @test
     */
    public function it_should_fail_if_file_does_not_exists()
    {



        $this->expectException(YamlFileParserException::class);

        $parser = new YamlFileParser();

        $result = $parser->parse(__DIR__ . '/noexists.yml');

    }

    /**
     * @test
     */
    public function it_should_fail_if_file_does_not_have_correct_yaml_format()
    {

        $config_yml =
            <<<YAML
foo: bar
bar:  foo: bar,  }
YAML;


        $configFilePath = FileSystemFactory::create($config_yml);

        $this->expectException(YamlFileParserException::class);

        $parser = new YamlFileParser();

        $result = $parser->parse($configFilePath);

    }

    /**
     * @test
     */
    public function it_should_parse_basic_yaml_file()
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

        $parser = new YamlFileParser();

        $result = $parser->parse($configFilePath);

        $this->assertEquals("sqlite", $result['database']['driver']);
        $this->assertTrue($result['debug']);
        $this->assertFalse($result['mock']);
    }

    /**
     * @test
     */
    public function it_should_import_yml_files()
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
foo: bar
debug: false
YAML;


        $this->filesystem = vfsStream::setup("root",null, [
            'config' => [
                'config.yml' => $config_yml,
                'config_dev.yml' => $config_dev_yml,
            ]
        ]);

        $this->basePath = $this->filesystem->url("root");

        $parser = new YamlFileParser();

        $result = $parser->parse($this->basePath . '/config/config_dev.yml');

        $this->assertEquals("sqlite", $result['database']['driver']);

        $this->assertEquals("bar", $result['foo']);

    }

    /**
     * @test
     */
    public function it_should_override_config_in_parent_files()
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
foo: bar
debug: false
YAML;


        $this->filesystem = vfsStream::setup("root",null, [
            'config' => [
                'config.yml' => $config_yml,
                'config_dev.yml' => $config_dev_yml,
            ]
        ]);

        $this->basePath = $this->filesystem->url("root");

        $parser = new YamlFileParser();

        $result = $parser->parse($this->basePath . '/config/config_dev.yml');

        $this->assertFalse($result['debug']);

    }


    /**
     * @test
     */
    public function it_should_replace_variables()
    {

        $config_yml =
            <<<YAML
foo: %bar%
YAML;


        $configFilePath = FileSystemFactory::create($config_yml);

        $parser = new YamlFileParser();

        $replacements = new \ArrayObject([
          'bar' => 'my new content'
        ]);

        $result = $parser->parse($configFilePath, $replacements);

        $this->assertEquals('my new content', $result['foo']);

    }

    /**
     * @test
     */
    public function it_should_replace_variables_in_deeper_levels()
    {
        $config_yml =
            <<<YAML
foo: bar
bar:
        foo: bar
        bar: %bar2%
YAML;


        $configFilePath = FileSystemFactory::create($config_yml);

        $parser = new YamlFileParser();

        $replacements = new \ArrayObject([
            'bar2' => 'other content'
        ]);

        $result = $parser->parse($configFilePath, $replacements);

        $this->assertEquals('other content', $result['bar']['bar']);

    }



}
