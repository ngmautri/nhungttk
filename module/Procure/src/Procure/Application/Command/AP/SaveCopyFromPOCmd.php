<?php
namespace Procure\Application\Command\AP;

use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SaveCopyFromPOCmd extends AbstractDoctrineCmd
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandInterface::execute()
     */
    public function execute()
    {
        if (!$this->handler instanceof AbstractCommandHandler) {
            throw new InvalidArgumentException(sprintf("[Error] No handler is found! %s", get_class($this->getHandler())) );
        }

        $this->handler->run($this);
    }
}
