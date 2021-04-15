<?php
namespace Application\Application\Command\Doctrine\Company;

use Application\Application\Command\Doctrine\AbstractCommand;
use Application\Application\Command\Doctrine\AbstractCommandHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\Service\Department\Tree\DepartmentNode;
use Application\Application\Service\Department\Tree\DepartmentTree;
use Application\Domain\Company\Department\DepartmentSnapshot;
use Application\Domain\Company\Department\DepartmentSnapshotAssembler;
use Application\Domain\Shared\Command\CommandInterface;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class InsertDepartmentCmdHandler extends AbstractCommandHandler
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

        try {

            $snapshot = new DepartmentSnapshot();
            DepartmentSnapshotAssembler::updateAllFieldsFromArray($snapshot, $cmd->getData());

            $snapshot->setNodeName($snapshot->getDepartmentName());

            $snapshot->setCompany($options->getCompanyId());
            $snapshot->setCreatedBy($options->getUserId());

            $builder = new DepartmentTree();
            $builder->setDoctrineEM($cmd->getDoctrineEM());
            $builder->initTree();
            $root = $builder->createTree(1, 0);

            $parent = $root->getNodeByName($snapshot->getParentName());

            $node = new DepartmentNode();
            $node->setParentId($parent->getId());
            $snapshot->setNodeParentId($parent->getId()); // important;
            $node->setContextObject($snapshot);
            $node->setId($snapshot->getDepartmentName());
            $node->setNodeCode($snapshot->getDepartmentName());
            $node->setNodeName($snapshot->getDepartmentName());

            // var_dump($root->isNodeDescendant($node));

            $parent = $root->getNodeByName($snapshot->getParentName());
            $node->setParentId($parent->getId());

            $builder->insertNode($node, $parent, $cmd->getOptions());

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
