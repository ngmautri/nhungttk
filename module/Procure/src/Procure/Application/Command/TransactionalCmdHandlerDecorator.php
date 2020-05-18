<?php
namespace Procure\Application\Command;

use Application\Application\Command\AbstractDoctrineCmd;
use Application\Domain\Shared\Command\AbstractCommandHandlerDecorator;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Domain\Exception\OperationFailedException;

/**
 * Transaction Decoration.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionalCmdHandlerDecorator extends AbstractCommandHandlerDecorator
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

            $cmd->getDoctrineEM()->commit(); // now commit
        } catch (\Exception $e) {
            $cmd->getDoctrineEM()
                ->getConnection()
                ->rollBack();
            throw new OperationFailedException(sprintf("%s", $e->getMessage()));
        }
    }
}
