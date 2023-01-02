<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Indexer;

use ClawRock\Debug\Api\Data\ProfileInterface;

class ProfileIndexer
{
    private \Magento\Framework\Filesystem\Driver\File $fileSystem;
    private \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory;
    private \ClawRock\Debug\Logger\Logger $logger;
    private \ClawRock\Debug\Helper\File $fileHelper;

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

    public function index(ProfileInterface $profile): void
    {
        try {
            $tmpIndexPath = $this->fileHelper->getProfileTempIndex();
            $this->fileSystem->createDirectory($this->fileSystem->getParentDirectory($tmpIndexPath));
            $tmpIndex = $this->fileWriteFactory->create($tmpIndexPath, $this->fileSystem, 'w');

            $tmpIndex->writeCsv($profile->getIndex());
            $index = $tmpIndex->readAll();
            $tmpIndex->close();
            $index .= $this->fileSystem->isExists($this->fileHelper->getProfileIndex())
                ? $this->fileSystem->fileGetContents($this->fileHelper->getProfileIndex())
                : '';

            $this->fileSystem->filePutContents($this->fileHelper->getProfileIndex(), $index);
        } catch (\Exception $e) {
            $this->logger->error('ClawRock_Debug: Error during profile indexation', ['exception' => $e]);
        }
    }
}
