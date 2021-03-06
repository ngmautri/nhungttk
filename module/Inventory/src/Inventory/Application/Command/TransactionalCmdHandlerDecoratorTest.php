<?php
namespace Inventory\Application\Command;

use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandlerDecorator;
use Application\Domain\Shared\Command\CommandInterface;

/**
 * Transaction Decoration.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionalCmdHandlerDecoratorTest extends AbstractCommandHandlerDecorator
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

            throw new \Exception(sprintf("Testing only %s...", "haha"));

            // $cmd->getDoctrineEM()->commit(); // now commit
        } catch (\Exception $e) {

            $cmd->getDto()->addError($e->getMessage());
            $cmd->getDoctrineEM()
                ->getConnection()
                ->rollBack();
        }
    }
}
