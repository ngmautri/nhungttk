<?php
namespace Application\Application\Command\Doctrine;

use Application\Domain\Shared\Command\CommandHandlerInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractCommandHandler implements CommandHandlerInterface
{

    protected $output;

    protected function setOutput($output)
    {
        $this->output = $output;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
