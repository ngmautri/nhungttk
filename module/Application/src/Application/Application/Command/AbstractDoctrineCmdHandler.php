<?php
namespace Application\Application\Command;

use Application\Domain\Shared\Command\CommandHandlerInterface;
use Application\Domain\Shared\Command\CommandInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractDoctrineCmdHandler implements CommandHandlerInterface
{
  /**
   * 
   * {@inheritDoc}
   * @see \Application\Domain\Shared\Command\CommandHandlerInterface::run()
   */
    abstract public function run(CommandInterface $cmd);
    
}
