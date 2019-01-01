<?php

namespace ClawRock\Debug\Helper;

class File
{
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getProfileDirectory()
    {
        return $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'debug';
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getProfileIndex()
    {
        return $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'debug' . DIRECTORY_SEPARATOR . 'index.csv';
    }

    public function getProfileTempIndex()
    {
        return $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'debug' . DIRECTORY_SEPARATOR . 'tmp'
            . DIRECTORY_SEPARATOR . 'index.csv';
    }

    /**
     * @param $token
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getProfileFilename($token)
    {
        return $this->getProfileDirectory() . DIRECTORY_SEPARATOR
            . substr($token, -2, 2) . DIRECTORY_SEPARATOR
            . substr($token, -4, 2) . DIRECTORY_SEPARATOR
            . $token;
    }
}
