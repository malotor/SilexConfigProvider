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
foo: bar
debug: false
YAML;

        $error_file =
            <<<YAML
foo: bar
bar:  foo: bar,  }
YAML;

        $replacement_file =
            <<<YAML
foo: %bar%
YAML;

        $replacement_file_b =
            <<<YAML
foo: %bar%
foo2: %bar2%
YAML;


        $this->filesystem = vfsStream::setup("root",null, [
            'config' => [
                'config.yml' => $config_yml,
                'config_dev.yml' => $config_dev_yml,
                'error.yml' => $error_file,
                'replacement.yml' => $replacement_file,
                'replacement_b.yml' => $replacement_file_b
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
    public function it_should_fail_if_file_does_not_have_correct_yaml_format()
    {
        $this->expectException(YamlFileParserException::class);

        $parser = new YamlFileParser();

        $result = $parser->parse($this->basePath . '/config/error.yml');

    }

    /**
     * @test
     */
    public function it_should_parse_basic_yaml_file()
    {

        $parser = new YamlFileParser();

        $result = $parser->parse($this->basePath . '/config/config.yml');

        $this->assertEquals("sqlite", $result['database']['driver']);
        $this->assertTrue($result['debug']);
        $this->assertFalse($result['mock']);
    }

    /**
     * @test
     */
    public function it_should_import_yml_files()
    {

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

        $parser = new YamlFileParser();

        $result = $parser->parse($this->basePath . '/config/config_dev.yml');

        $this->assertFalse($result['debug']);

    }


    /**
     * @test
     */
    public function it_should_replace_variables()
    {

        $parser = new YamlFileParser();

        $replacements = new \ArrayObject([
          'bar' => 'my new content'
        ]);

        $result = $parser->parse($this->basePath . '/config/replacement.yml', $replacements);

        $this->assertEquals('my new content', $result['foo']);

    }

    /**
     * @test
     */
    public function it_should_replace_variables_in_deeper_levels()
    {


        $parser = new YamlFileParser();

        $replacements = new \ArrayObject([
            'bar' => 'my new content',
            'bar2' => 'other content'
        ]);

        $result = $parser->parse($this->basePath . '/config/replacement_b.yml', $replacements);

        $this->assertEquals('my new content', $result['foo']);
        $this->assertEquals('other content', $result['foo2']);

    }




}
