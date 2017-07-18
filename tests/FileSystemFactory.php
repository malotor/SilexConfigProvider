<?php


namespace malotor\ConfigProvider\tests;

use org\bovigo\vfs\vfsStream;

class FileSystemFactory
{
    static public function create($files)
    {
        $filesystem = vfsStream::setup("root",null, $files);

        return $filesystem->url("root");
    }
}