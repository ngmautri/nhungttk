<?php
namespace Application\Domain\EventBus\Queue;

use Application\Domain\EventBus\Event\EventInterface;
use Application\Domain\EventBus\Event\NullEvent;
use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FileSystemQueue implements QueueInterface
{

    /** @var string */
    protected $baseDirectory;

    /** @var int */
    protected $permissions = 0740;

    /** @var string */
    protected $queueName;

    /**
     * FileSystemEventMiddleware constructor.
     *
     * @param string $baseDirectory
     * @param string $queueName
     */
    public function __construct($baseDirectory, $queueName)
    {
        $this->guard($baseDirectory);

        $this->baseDirectory = $baseDirectory;
        $this->queueName = $queueName;
    }

    /**
     *
     * @param string $directory
     *
     * @throws \InvalidArgumentException
     */
    protected function guard($directory)
    {
        if (false === \is_dir($directory)) {
            throw new \InvalidArgumentException(\sprintf('The provided path %s is not a valid directory', $directory));
        }

        if (false === \is_writable($directory)) {
            throw new \InvalidArgumentException(\sprintf('The provided directory %s is not writable', $directory));
        }
    }

    /**
     * Returns the name of the Queue.
     *
     * @return string
     */
    public function name()
    {
        return $this->queueName;
    }

    /**
     * Adds an event to the Queue.
     *
     * @param EventInterface $event
     */
    public function push(EventInterface $event)
    {
        file_put_contents($this->filePath(), \serialize($event) . PHP_EOL);
    }

    public function pop()
    {
        $iterator = $this->directoryIterator();

        if ($this->directoryIterator()->count()) {
            $iteratorArray = iterator_to_array($iterator, true);
            $fileName = key($iteratorArray);

            $event = file_get_contents($this->baseDirectory . DIRECTORY_SEPARATOR . $fileName);
            $event = \unserialize($event);
            unlink($this->baseDirectory . DIRECTORY_SEPARATOR . $fileName);

            return $event;
        }

        return NullEvent::create();
    }

    /**
     * Returns true if queue has been fully processed or not, false otherwise.
     *
     * @return bool
     */
    public function hasElements()
    {
        $iterator = $this->directoryIterator();

        return false === empty($iterator->count());
    }

    /**
     *
     * @return string
     */
    protected function filePath()
    {
        return sprintf('%s/%s.%s.job.php', $this->baseDirectory, $this->queueName, Uuid::uuid4()->toString());
    }

    /**
     *
     * @return \GlobIterator
     */
    protected function directoryIterator()
    {
        $iterator = new \GlobIterator($this->baseDirectory . DIRECTORY_SEPARATOR . '*.job.php', \FilesystemIterator::KEY_AS_FILENAME);

        return $iterator;
    }
}