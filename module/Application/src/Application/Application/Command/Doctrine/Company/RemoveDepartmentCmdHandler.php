<?php
namespace Application\Application\Command\Doctrine\Company;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\Service\Department\Tree\DepartmentTree;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class RemoveDepartmentCmdHandler extends AbstractCommandHandler
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandHandlerInterface::run()
     */
    public function run(CommandInterface $cmd)
    {
        /**
         *
         * @var CreateHeaderCmdOptions $options ;
         * @var AbstractCommand $cmd ;
         */
        Assert::isInstanceOf($cmd, AbstractCommand::class);
        Assert::isInstanceOf($cmd->getOptions(), CmdOptions::class);
        Assert::notNull($cmd->getData(), 'Input data in emty');

        $options = $cmd->getOptions();
        $data = $cmd->getData();

        try {

            /**
             *
             * @var DepartmentTree $builder
             */
            $builder = $data['builder'];
            $node = $data['node'];

            $builder->removeNodeFromParent($node, $options);

            // event dispatch
            // ================
            if ($cmd->getEventBus() !== null) {
                $cmd->getEventBus()->dispatch($builder->getRecordedEvents());
            }
            // ================
        } catch (\Exception $e) {

            $cmd->addError($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
    }
}
