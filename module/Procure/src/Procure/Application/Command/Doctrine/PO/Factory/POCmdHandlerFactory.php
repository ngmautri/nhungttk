<?php
namespace Procure\Application\Command\Doctrine\PO\Factory;

use Procure\Application\Command\Contracts\CmdHandlerAbstractFactory;
use Procure\Application\Command\Doctrine\PO\CreateHeaderCmdHandler;
use Procure\Application\Command\Doctrine\PO\CreateRowCmdHandler;
use Procure\Application\Command\Doctrine\PO\PostCmdHandler;
use Procure\Application\Command\Doctrine\PO\UpdateHeaderCmdHandler;
use Procure\Application\Command\Doctrine\PO\UpdateRowCmdHandler;
use Procure\Application\Command\Doctrine\PO\UpdateRowInlineCmdHandler;
use Procure\Application\Command\Doctrine\PO\CloneAndSavePOCmdHandler;
use Procure\Application\Command\Doctrine\PO\RemoveRowCmdHandler;

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
        return new CloneAndSavePOCmdHandler();
    }

    public function getRemoveRowCmdHandler()
    {
        return new RemoveRowCmdHandler();
    }
}