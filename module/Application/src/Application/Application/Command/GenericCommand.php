<?php
namespace Application\Application\Command;

use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandler;
use Application\Application\Command\Doctrine\AbstractCommand;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericCommand extends AbstractCommand
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandInterface::execute()
     */
    public function execute()
    {
        if (! $this->handler instanceof AbstractCommandHandler) {
            throw new \Exception(sprintf("[Error] No handler is found! %s", get_class($this->getHandler())));
        }

        $this->handler->run($this);
    }
}
