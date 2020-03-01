<?php
namespace Application\Domain\Shared\Command;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface CommandHandlerInterface
{

    public function run(CommandInterface $cmd);
}
