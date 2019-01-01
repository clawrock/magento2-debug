<?php

namespace ClawRock\Debug\Test\Unit\Model\Storage;

use ClawRock\Debug\Model\Storage\ProfileFileStorage;
use ClawRock\Debug\Model\ValueObject\SearchResult;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class ProfileFileStorageTest extends TestCase
{
    /**
     * @var \Magento\Framework\Filesystem\Driver\File|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileSystemMock;

    private $fileReadFactoryMock;

    private $readMock;

    private $fileWriteFactoryMock;

    private $writeMock;

    private $loggerMock;

    private $fileHelperMock;

    private $profileFactoryMock;

    private $profileSerializerMock;

    private $profileIndexerMock;

    private $profileMock;

    private $criteriaMock;

    private $storage;

    protected function setUp()
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

        $this->profileFactoryMock = $this->getMockBuilder(\ClawRock\Debug\Model\ProfileFactory::class)
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

        $this->storage = (new ObjectManager($this))->getObject(ProfileFileStorage::class, [
            'fileSystem' => $this->fileSystemMock,
            'fileReadFactory' => $this->fileReadFactoryMock,
            'fileWriteFactory' => $this->fileWriteFactoryMock,
            'logger' => $this->loggerMock,
            'fileHelper' => $this->fileHelperMock,
            'profileFactory' => $this->profileFactoryMock,
            'profileSerializer' => $this->profileSerializerMock,
            'profileIndexer' => $this->profileIndexerMock,
        ]);
    }

    public function testPurge()
    {
        $this->fileHelperMock->expects($this->once())->method('getProfileDirectory')->willReturn('profile_directory');
        $this->fileSystemMock->expects($this->once())->method('deleteDirectory')->with('profile_directory');

        $this->storage->purge();
    }

    public function testRead()
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

    public function testWrite()
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

    public function testRemove()
    {
        $token = 'token';
        $this->fileHelperMock->expects($this->once())->method('getProfileFilename')
            ->with($token)->willReturn('file');

        $this->fileSystemMock->expects($this->once())->method('deleteFile')->with('file');

        $this->storage->remove($token);
    }

    public function testFindNoIndex()
    {
        $this->fileHelperMock->expects($this->once())->method('getProfileIndex')->willReturn('file');
        $this->fileSystemMock->expects($this->once())->method('isExists')->with('file')->willReturn(false);
        $this->assertEquals([], $this->storage->find($this->criteriaMock));
    }

    public function testFind()
    {
        $searchResult = ['token', 'ip', 'method', 'url', 3600, '200', '1024', null];
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

    public function testFindException()
    {
        $exception = new FileSystemException(new Phrase('Exception'));
        $this->fileHelperMock->expects($this->once())->method('getProfileIndex')->willReturn('file');
        $this->fileSystemMock->expects($this->once())->method('isExists')
            ->with('file')->willThrowException($exception);
        $this->loggerMock->expects($this->once())->method('critical')->with($exception);

        $this->assertEquals([], $this->storage->find($this->criteriaMock));
    }
}
