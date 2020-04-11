<?php
namespace Procure\Application\Command\GR;

use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandlerDecorator;
use Application\Domain\Shared\Command\CommandInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CreateHeaderCmdHandlerDecoratorTest extends AbstractCommandHandlerDecorator
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\AbstractCommandHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        if (! $cmd instanceof AbstractDoctrineCmd) {
            throw new \Exception(sprintf("% not found!", "AbstractDoctrineCmd"));
        }

        $cmd->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {

            $this->handler->run($cmd);

            // $cmd->getDoctrineEM()->commit(); // now commit
        } catch (\Exception $e) {

            $cmd->getDto()->addError($e->getMessage());
            $cmd->getDoctrineEM()
                ->getConnection()
                ->rollBack();
        }
    }
}
