<?php

namespace ClawRock\Debug\Model\Indexer;

use ClawRock\Debug\Api\Data\ProfileInterface;
use Magento\Framework\Exception\FileSystemException;

class ProfileIndexer
{
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileSystem;

    /**
     * @var \Magento\Framework\Filesystem\File\WriteFactory
     */
    private $fileWriteFactory;

    /**
     * @var \ClawRock\Debug\Logger\Logger
     */
    private $logger;

    /**
     * @var \ClawRock\Debug\Helper\File
     */
    private $fileHelper;

    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $fileSystem,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory,
        \ClawRock\Debug\Logger\Logger $logger,
        \ClawRock\Debug\Helper\File $fileHelper
    ) {
        $this->fileSystem = $fileSystem;
        $this->fileWriteFactory = $fileWriteFactory;
        $this->logger = $logger;
        $this->fileHelper = $fileHelper;
    }

    public function index(ProfileInterface $profile): ProfileIndexer
    {
        try {
            $tmpIndexPath = $this->fileHelper->getProfileTempIndex();
            $this->fileSystem->createDirectory($this->fileSystem->getParentDirectory($tmpIndexPath));
            $tmpIndex = $this->fileWriteFactory->create($tmpIndexPath, $this->fileSystem, 'w');

            $tmpIndex->writeCsv($profile->getIndex());
            $index = $tmpIndex->readAll();
            $tmpIndex->close();

            try {
                $index .= $this->fileSystem->fileGetContents($this->fileHelper->getProfileIndex());
            } catch (FileSystemException $e) {
                $this->logger->info($e);
            }

            $this->fileSystem->filePutContents($this->fileHelper->getProfileIndex(), $index);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $this;
    }
}
