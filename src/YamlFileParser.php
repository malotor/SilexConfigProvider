<?php


namespace malotor\ConfigProvider;

use Symfony\Component\Yaml\Yaml;

class YamlFileParser
{
    private $filePath;

    public function parse($file)
    {
        $this->assertFileExists($file);
        $this->filePath = $file;
        $content = $this->parse(file_get_contents($file));
        $content = $this->import($content);
        return $content;
    }

    private function assertFileExists($file)
    {
        var_dump($file);
        var_dump(file_exists($file));
        if (!file_exists($file))
            throw YamlFileParserException::fileNotExists($file);
    }


    private function import($config) {
        foreach ($config as $key => $value) {
            if ($key == 'imports') {
                foreach ($value as $resource) {
                    $base_dir = str_replace(basename($this->filePath), '', $this->filePath);
                    $config = array_replace_recursive($this->parse($base_dir . $resource['resource']), $config );
                }
                unset($config['imports']);
            }
        }
        return $config;
    }

}