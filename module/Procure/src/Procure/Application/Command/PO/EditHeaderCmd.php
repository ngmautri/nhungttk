<?php
namespace Procure\Application\Command\PO;

use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\CommandHandlerInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class EditHeaderCmd extends AbstractDoctrineCmd
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandInterface::execute()
     */
    public function execute()
    {
        if ($this->handler instanceof CommandHandlerInterface) {
            throw new \Exception("No handler is found!");
        }

        $this->handler->run($this);
    }
}
