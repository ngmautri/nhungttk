<?php
namespace Application\Application\Command;

use Application\Domain\Shared\Command\CommandHandlerInterface;
use Application\Domain\Shared\Command\CommandInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractDoctrineCmdHandler implements CommandHandlerInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandHandlerInterface::run()
     */
    abstract public function run(CommandInterface $cmd);
    
    /**
     * @todo: implementation.
     * @param EventDispatcher $dipatcher
     */
    public function dispatch (EventDispatcher $dipatcher =null){
     
    }
    
}
