<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Model\Storage;

use ClawRock\Debug\Model\Storage\ProfileFileStorage;
use ClawRock\Debug\Model\ValueObject\SearchResult;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Phrase;
use PHPUnit\Framework\TestCase;

class ProfileFileStorageTest extends TestCase
{
    /** @var \Magento\Framework\Filesystem\Driver\File&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Filesystem\Driver\File $fileSystemMock;
    /** @var \Magento\Framework\Filesystem\File\ReadFactory&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Filesystem\File\ReadFactory $fileReadFactoryMock;
    /** @var \Magento\Framework\Filesystem\File\Read&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Filesystem\File\Read $readMock;
    /** @var \Magento\Framework\Filesystem\File\WriteFactory&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactoryMock;
    /** @var \Magento\Framework\Filesystem\File\WriteInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \Magento\Framework\Filesystem\File\WriteInterface $writeMock;
    /** @var \ClawRock\Debug\Logger\Logger&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Logger\Logger $loggerMock;
    /** @var \ClawRock\Debug\Helper\File&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Helper\File $fileHelperMock;
    /** @var \ClawRock\Debug\Model\Serializer\ProfileSerializer&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Serializer\ProfileSerializer $profileSerializerMock;
    /** @var \ClawRock\Debug\Model\Indexer\ProfileIndexer&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Indexer\ProfileIndexer $profileIndexerMock;
    /** @var \ClawRock\Debug\Api\Data\ProfileInterface&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Api\Data\ProfileInterface $profileMock;
    /** @var \ClawRock\Debug\Model\Profile\Criteria&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Profile\Criteria $criteriaMock;
    private \ClawRock\Debug\Model\Storage\ProfileFileStorage $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileSystemMock = $this->getMockBuilder(\Magento\Framework\Filesystem\Driver\File::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fileReadFactoryMock = $this->getMockBuilder(\Magento\Framework\Filesystem\File\ReadFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->readMock = $this->getMockBuilder(\Magento\Framework\Filesystem\File\Read::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fileWriteFactoryMock = $this->getMockBuilder(\Magento\Framework\Filesystem\File\WriteFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->writeMock = $this->getMockForAbstractClass(\Magento\Framework\Filesystem\File\WriteInterface::class);

        $this->loggerMock = $this->getMockBuilder(\ClawRock\Debug\Logger\Logger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fileHelperMock = $this->getMockBuilder(\ClawRock\Debug\Helper\File::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->profileSerializerMock = $this->getMockBuilder(\ClawRock\Debug\Model\Serializer\ProfileSerializer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->profileIndexerMock = $this->getMockBuilder(\ClawRock\Debug\Model\Indexer\ProfileIndexer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->profileMock = $this->getMockForAbstractClass(\ClawRock\Debug\Api\Data\ProfileInterface::class);

        $this->criteriaMock = $this->getMockBuilder(\ClawRock\Debug\Model\Profile\Criteria::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->storage = new ProfileFileStorage(
            $this->fileSystemMock,
            $this->fileReadFactoryMock,
            $this->fileWriteFactoryMock,
            $this->loggerMock,
            $this->fileHelperMock,
            $this->profileSerializerMock,
            $this->profileIndexerMock
        );
    }

    public function testPurge(): void
    {
        $this->fileHelperMock->expects($this->once())->method('getProfileDirectory')->willReturn('profile_directory');
        $this->fileSystemMock->expects($this->once())->method('deleteDirectory')->with('profile_directory');

        $this->storage->purge();
    }

    public function testRead(): void
    {
        $token = 'token';
        $this->fileHelperMock->expects($this->once())->method('getProfileFilename')
            ->with($token)->willReturn('file');

        $this->fileReadFactoryMock->expects($this->once())->method('create')
            ->with('file', $this->fileSystemMock)->willReturn($this->readMock);

        $this->readMock->expects($this->once())->method('readAll')
            ->willReturn('content');

        $this->profileSerializerMock->expects($this->once())->method('unserialize')
            ->with('content')->willReturn($this->profileMock);

        $this->assertEquals($this->profileMock, $this->storage->read($token));
    }

    public function testWrite(): void
    {
        $token = 'token';
        $path = 'path';
        $this->profileMock->expects($this->once())->method('getToken')->willReturn($token);

        $this->fileHelperMock->expects($this->once())->method('getProfileFilename')
            ->with($token)->willReturn($path);

        $this->fileSystemMock->expects($this->once())->method('getParentDirectory')->with($path)->willReturn('dir');
        $this->fileSystemMock->expects($this->once())->method('createDirectory')->with('dir');
        $this->fileWriteFactoryMock->expects($this->once())->method('create')
            ->with($path, $this->fileSystemMock, 'w')
            ->willReturn($this->writeMock);
        $this->profileSerializerMock->expects($this->once())->method('serialize')
            ->with($this->profileMock)
            ->willReturn('serialized_profile');
        $this->writeMock->expects($this->once())->method('write')->with('serialized_profile');
        $this->writeMock->expects($this->once())->method('close');

        $this->fileSystemMock->expects($this->once())->method('stat')->with($path)->willReturn(['size' => 1]);
        $this->profileMock->expects($this->once())->method('setFilesize')->with(1);
        $this->profileIndexerMock->expects($this->once())->method('index')->with($this->profileMock);

        $this->assertEquals($path, $this->storage->write($this->profileMock));
    }

    public function testRemove(): void
    {
        $token = 'token';
        $this->fileHelperMock->expects($this->once())->method('getProfileFilename')
            ->with($token)->willReturn('file');

        $this->fileSystemMock->expects($this->once())->method('deleteFile')->with('file');

        $this->storage->remove($token);
    }

    public function testFindNoIndex(): void
    {
        $this->fileHelperMock->expects($this->once())->method('getProfileIndex')->willReturn('file');
        $this->fileSystemMock->expects($this->once())->method('isExists')->with('file')->willReturn(false);
        $this->assertEquals([], $this->storage->find($this->criteriaMock));
    }

    public function testFind(): void
    {
        $searchResult = ['token', 'ip', 'method', 'url', 3600, '200', '1024', null, '100'];
        $this->fileHelperMock->expects($this->exactly(2))->method('getProfileIndex')->willReturn('file');
        $this->fileSystemMock->expects($this->once())->method('isExists')->with('file')->willReturn(true);
        $this->fileSystemMock->expects($this->once())->method('fileOpen')->with('file', 'r')->willReturn('resource');
        $this->fileSystemMock->expects($this->once())->method('fileGetCsv')
            ->with('resource')
            ->willReturn($searchResult);
        $this->criteriaMock->expects($this->once())->method('match')->with($searchResult)->willReturn(true);
        $this->fileSystemMock->expects($this->once())->method('fileClose')->with('resource');

        $this->assertEquals([new SearchResult(...$searchResult)], $this->storage->find($this->criteriaMock));
    }

    public function testFindException(): void
    {
        $exception = new FileSystemException(new Phrase('Exception'));
        $this->fileHelperMock->expects($this->once())->method('getProfileIndex')->willReturn('file');
        $this->fileSystemMock->expects($this->once())->method('isExists')
            ->with('file')->willThrowException($exception);
        $this->loggerMock->expects($this->once())->method('error');

        $this->assertEquals([], $this->storage->find($this->criteriaMock));
    }
}
