<?php


namespace malotor\ConfigProvider;


class YamlFileParserException extends \Exception
{

    static public function fileNotExists($file)
    {
        return new self("File '{$file}' not found");
    }

    static public function fileIncorrect($file, $message)
    {
        return new self("File '{$file}' is not correct yaml format: {$message}");
    }
}