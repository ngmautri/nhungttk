<?php
namespace Procure\Application\Command\PO;

use Application\Application\Command\AbstractDoctrineCmd;
use Application\Application\Command\AbstractDoctrineCmdHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AddRowCmd extends AbstractDoctrineCmd
{

    /**
     * 
     * {@inheritDoc}
     * @see \Application\Domain\Shared\Command\CommandInterface::execute()
     */
    public function execute()
    {
        if (!$this->handler instanceof AbstractDoctrineCmdHandler) {
            throw new \Exception(sprintf("[Error] No handler is found! %s", get_class($this->getHandler())) );
        }
        
        $this->handler->run($this);
    }
}
