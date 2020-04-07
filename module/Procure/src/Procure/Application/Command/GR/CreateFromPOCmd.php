<?php
namespace Procure\Application\Command\GR;

use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CreateFromPOCmd extends AbstractDoctrineCmd
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandInterface::execute()
     */
    public function execute()
    {
        if (!$this->handler instanceof AbstractCommandHandler) {
            throw new \Exception(sprintf("[Error] No handler is found! %s", get_class($this->getHandler())) );
        }

        $this->handler->run($this);
    }
}
