<?php
declare(strict_types=1);

namespace ClawRock\Debug\Model\Storage;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Model\Profile\Criteria;
use ClawRock\Debug\Model\ValueObject\SearchResult;
use Magento\Framework\Exception\FileSystemException;

class ProfileFileStorage
{
    private \Magento\Framework\Filesystem\Driver\File $fileSystem;
    private \Magento\Framework\Filesystem\File\ReadFactory $fileReadFactory;
    private \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory;
    private \ClawRock\Debug\Logger\Logger $logger;
    private \ClawRock\Debug\Helper\File $fileHelper;
    private \ClawRock\Debug\Model\Serializer\ProfileSerializer $profileSerializer;
    private \ClawRock\Debug\Model\Indexer\ProfileIndexer $profileIndexer;

    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $fileSystem,
        \Magento\Framework\Filesystem\File\ReadFactory $fileReadFactory,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory,
        \ClawRock\Debug\Logger\Logger $logger,
        \ClawRock\Debug\Helper\File $fileHelper,
        \ClawRock\Debug\Model\Serializer\ProfileSerializer $profileSerializer,
        \ClawRock\Debug\Model\Indexer\ProfileIndexer $profileIndexer
    ) {
        $this->fileSystem = $fileSystem;
        $this->fileReadFactory = $fileReadFactory;
        $this->fileWriteFactory = $fileWriteFactory;
        $this->logger = $logger;
        $this->fileHelper = $fileHelper;
        $this->profileSerializer = $profileSerializer;
        $this->profileIndexer = $profileIndexer;
    }

    public function find(Criteria $criteria): array
    {
        $results = [];

        try {
            if (!$this->fileSystem->isExists($this->fileHelper->getProfileIndex())) {
                return $results;
            }

            $resource = $this->fileSystem->fileOpen($this->fileHelper->getProfileIndex(), 'r');
            $i = 0;
            while ($profile = $this->fileSystem->fileGetCsv($resource)) {
                if (is_array($profile) && $criteria->match($profile)) {
                    $results[] = SearchResult::createFromCsv($profile);
                    if (++$i >= $criteria->getLimit()) {
                        break;
                    }
                }
            }

            $this->fileSystem->fileClose($resource);
        } catch (FileSystemException $e) {
            $this->logger->error('ClawRock_Debug: error during profile search', ['exception' => $e]);
        }

        return $results;
    }

    public function purge(): void
    {
        $this->fileSystem->deleteDirectory($this->fileHelper->getProfileDirectory());
    }

    public function read(string $token): ProfileInterface
    {
        $file = $this->fileReadFactory->create($this->fileHelper->getProfileFilename($token), $this->fileSystem);

        return $this->profileSerializer->unserialize($file->readAll());
    }

    public function write(ProfileInterface $profile): string
    {
        $path = $this->fileHelper->getProfileFilename($profile->getToken());
        $this->fileSystem->createDirectory($this->fileSystem->getParentDirectory($path));
        $file = $this->fileWriteFactory->create($path, $this->fileSystem, 'w');
        $file->write($this->profileSerializer->serialize($profile));
        $file->close();
        $profile->setFilesize($this->fileSystem->stat($path)['size']);

        $this->profileIndexer->index($profile);

        return $path;
    }

    public function remove(string $token): void
    {
        $path = $this->fileHelper->getProfileFilename($token);
        $this->fileSystem->deleteFile($path);
    }
}
