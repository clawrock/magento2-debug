<?php
declare(strict_types=1);

namespace ClawRock\Debug\Helper;

class File
{
    private \Magento\Framework\App\Filesystem\DirectoryList $directoryList;

    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
    }

    public function getProfileDirectory(): string
    {
        return $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'debug';
    }

    public function getProfileIndex(): string
    {
        return $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'debug' . DIRECTORY_SEPARATOR . 'index.csv';
    }

    public function getProfileTempIndex(): string
    {
        return $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'debug' . DIRECTORY_SEPARATOR . 'tmp'
            . DIRECTORY_SEPARATOR . 'index.csv';
    }

    public function getProfileFilename(string $token): string
    {
        return $this->getProfileDirectory() . DIRECTORY_SEPARATOR
            . substr($token, -2, 2) . DIRECTORY_SEPARATOR
            . substr($token, -4, 2) . DIRECTORY_SEPARATOR
            . $token;
    }
}
