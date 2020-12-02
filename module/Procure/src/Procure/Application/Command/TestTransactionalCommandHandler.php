<?php
namespace Procure\Application\Command;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandlerDecorator;
use Application\Domain\Shared\Command\CommandInterface;
use Webmozart\Assert\Assert;

/**
 * Transaction Decoration.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TestTransactionalCommandHandler extends AbstractCommandHandlerDecorator
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\AbstractCommandHandler::run()
     */
    public function run(CommandInterface $cmd)
    {
        Assert::isInstanceOf($cmd, AbstractCommand::class, "Command not found!");

        /**
         *
         * @var AbstractCommand $cmd ;
         */
        $cmd->getDoctrineEM()
            ->getConnection()
            ->beginTransaction(); // suspend auto-commit

        try {
            $cmd->logInfo(sprintf("Testing %s running", \get_class($this->handler)));

            $this->handler->run($cmd);
            // $cmd->getDoctrineEM()->commit();

            $cmd->logInfo(sprintf("Testing %s completed in %d (ms), but not committed!", \get_class($this->handler), $cmd->getEstimatedDuration()));
        } catch (\Exception $e) {
            $cmd->logAlert($e->getMessage());
            $cmd->logException($e);
            $cmd->getDoctrineEM()
                ->getConnection()
                ->rollBack();

            $cmd->logInfo(sprintf("%s-%s", "Transaction rollback // ", \get_class($this)));

            throw new \RuntimeException(sprintf("%s", $e->getMessage()));
        }
    }
}
