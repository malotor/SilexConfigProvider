<?php


namespace malotor\ConfigProvider\tests;

use org\bovigo\vfs\vfsStream;

class FileSystemFactory
{
    static public function create($fileContent)
    {
        $filesystem = vfsStream::setup("root",null, [
            'config.yml' => $fileContent
        ]);

        return $filesystem->url("root") . '/config.yml';
    }
}