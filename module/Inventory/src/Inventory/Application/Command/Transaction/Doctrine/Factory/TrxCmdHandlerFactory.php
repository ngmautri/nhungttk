<?php
namespace Inventory\Application\Command\Transaction\Doctrine\Factory;

use Inventory\Application\Command\Contracts\CmdHandlerAbstractFactory;
use Inventory\Application\Command\Transaction\Doctrine\CreateHeaderCmdHandler;
use Inventory\Application\Command\Transaction\Doctrine\CreateRowCmdHandler;
use Inventory\Application\Command\Transaction\Doctrine\PostCmdHandler;
use Inventory\Application\Command\Transaction\Doctrine\UpdateHeaderCmdHandler;
use Inventory\Application\Command\Transaction\Doctrine\UpdateRowCmdHandler;
use Inventory\Application\Command\Transaction\Doctrine\UpdateRowInlineCmdHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TrxCmdHandlerFactory extends CmdHandlerAbstractFactory
{

    public function getCreateHeaderCmdHandler()
    {
        return new CreateHeaderCmdHandler();
    }

    public function getUpdateHeaderCmdHandler()
    {
        return new UpdateHeaderCmdHandler();
    }

    public function getReverseCmdHandler()
    {
        return null;
    }

    public function getPostCmdHandler()
    {
        return new PostCmdHandler();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Command\Contracts\CmdHandlerAbstractFactory::getCreateRowCmdHandler()
     */
    public function getCreateRowCmdHandler()
    {
        return new CreateRowCmdHandler();
    }

    public function getUpdateRowCmdHandler()
    {
        return new UpdateRowCmdHandler();
    }

    public function getInlineUpdateRowCmdHandler()
    {
        return new UpdateRowInlineCmdHandler();
    }

    public function getCloneCmdHandler()
    {
        return null;
    }
}