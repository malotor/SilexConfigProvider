<?php


namespace malotor\ConfigProvider;


class YamlFileParserException extends \Exception
{
    static public function fileNotExists($file)
    {
        return new self("File {$file} not found");
    }
}