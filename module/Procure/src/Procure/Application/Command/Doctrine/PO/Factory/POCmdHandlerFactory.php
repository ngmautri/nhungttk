<?php
namespace Procure\Application\Command\Doctrine\PO\Factory;

use Procure\Application\Command\Contracts\CmdHandlerAbstractFactory;
use Procure\Application\Command\DoctrinePO\UpdateRowCmdHandler;
use Procure\Application\Command\Doctrine\PO\CreateHeaderCmdHandler;
use Procure\Application\Command\Doctrine\PO\CreateRowCmdHandler;
use Procure\Application\Command\Doctrine\PO\InlineUpdateRowCmdHandler;
use Procure\Application\Command\Doctrine\PO\PostCmdHandler;
use Procure\Application\Command\Doctrine\PO\UpdateHeaderCmdHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class POCmdHandlerFactory extends CmdHandlerAbstractFactory
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

    public function getAddRowCmdHandler()
    {
        return new CreateRowCmdHandler();
    }

    public function getUpdateRowCmdHandler()
    {
        return new UpdateRowCmdHandler();
    }

    public function getInlineUpdateRowCmdHandler()
    {
        return new InlineUpdateRowCmdHandler();
    }
}