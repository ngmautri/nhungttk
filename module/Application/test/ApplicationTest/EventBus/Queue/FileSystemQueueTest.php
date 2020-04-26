<?php
namespace ApplicationTest\EventBus\Queue;

use ApplicationTest\EventBus\DummyEvent;
use Application\Domain\EventBus\Event\NullEvent;
use Application\Domain\EventBus\Queue\FileSystemQueue;
use PHPUnit_Framework_TestCase;

class FileSystemQueueTest extends PHPUnit_Framework_TestCase
{

    /** @var FileSystemQueue */
    protected $consumer;

    /** @var FileSystemQueue */
    protected $producer;

    /** @var string */
    protected $dirPath;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
        // require ($root . '/Bootstrap.php');

        // $path = $root . '/data/jobs/';

        $path = __DIR__ . '/jobs/';
        if (false === file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $this->dirPath = realpath($path);

        $this->producer = new FileSystemQueue($this->dirPath, 'testAdapterQueue');
        $this->consumer = new FileSystemQueue($this->dirPath, 'testAdapterQueue');
    }

    public function testAdapterQueue()
    {
        $event = new DummyEvent();
        $this->producer->push($event);

        $this->assertTrue($this->producer->hasElements());
        $this->assertEquals($event, $this->consumer->pop());
    }

    public function testAdapterQueueReturnNullEvent()
    {
        $queue = new FileSystemQueue('.', 'testAdapterQueue');
        $this->assertInstanceOf(NullEvent::class, $queue->pop());
    }

    public function testName()
    {
        $this->assertEquals('testAdapterQueue', $this->consumer->name());
    }

    public function testItWillThrowExceptionIfDirectoryDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);

        new FileSystemQueue('/nope', 'testAdapterQueue');
    }

    public function testItThrowsExceptionWhenDirDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);

        new FileSystemQueue('/nope', 'testAdapterQueue');
    }

    public function testItThrowsExceptionWhenDirIsNotWritable()
    {
        $this->expectException(\InvalidArgumentException::class);

        new FileSystemQueue('/', 'testAdapterQueue');
    }

    public function tearDown()
    {
        /*
         * if (file_exists($this->dirPath)) {
         * foreach (\glob("{$this->dirPath}/*") as $file) {
         * unlink($file);
         * }
         * }
         * rmdir($this->dirPath);
         */
    }
}