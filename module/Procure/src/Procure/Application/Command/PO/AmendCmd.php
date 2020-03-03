<?php
namespace Procure\Application\Command\PO;

use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\CommandHandlerInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AmendHeaderCmd extends AbstractDoctrineCmd
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandInterface::execute()
     */
    public function execute()
    {
        if (!$this->getHandler() instanceof CommandHandlerInterface) {
            throw new \Exception(sprintf("[Error] No handler is found! %s", get_class($this->getHandler())) );
        }

        $this->handler->run($this);
    }
}
