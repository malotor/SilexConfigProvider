<?php


namespace malotor\ConfigProvider;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class YamlFileParser
{
    private $filePath;
    private $replacements;


    public function parse($file , \ArrayAccess $replacement = null)
    {
        $this->assertFileExists($file);

        $this->filePath = $file;
        $this->replacements = $replacement;
        try {
            $content = Yaml::parse(file_get_contents($file));
        } catch (ParseException $e)
        {
            throw YamlFileParserException::fileIncorrect($file,$e->getMessage());
        }

        $content = $this->import($content);
        $content = $this->replaceVariables($content);
        return $content;
    }

    private function assertFileExists($file)
    {
        if (!(file_exists($file)))
            throw YamlFileParserException::fileNotExists($file);
    }


    private function import($config) {
        foreach ($config as $key => $value) {
            if ($key == 'imports') {
                foreach ($value as $resource) {
                    $base_dir = str_replace(basename($this->filePath), '', $this->filePath);
                    $config = array_replace_recursive($this->parse($base_dir . $resource['resource'], $this->replacements), $config );
                }
                unset($config['imports']);
            }
        }
        return $config;
    }

    private function replaceVariables($content)
    {
        foreach ($content as $itemKey => $itemValue)
        {

            if (is_array($itemValue))
            {
                $content[$itemKey] = $this->replaceVariables($itemValue);
            }
            else {
                preg_match_all("/%([^%]*)%/", $itemValue, $variables);


                foreach ($variables[0] as $key => $match)
                {
                    $variable_key = $variables[1][$key];
                    $variable_value = $this->replacements[$variable_key];

                    if (!$variable_value) throw new YamlFileParserException("Variablie '$variable_key' doesn't exists!");

                    $itemValue = str_replace($match,$variable_value,$itemValue);
                    $content[$itemKey] = $itemValue;
                }
            }

        }
        return $content;
    }



}