<?php
namespace Procure\Application\Command\Doctrine\QR\Factory;

use Procure\Application\Command\Contracts\CmdHandlerAbstractFactory;
use Procure\Application\Command\Doctrine\QR\CloneAndSaveCmdHandler;
use Procure\Application\Command\Doctrine\QR\CreateHeaderCmdHandler;
use Procure\Application\Command\Doctrine\QR\CreateRowCmdHandler;
use Procure\Application\Command\Doctrine\QR\PostCmdHandler;
use Procure\Application\Command\Doctrine\QR\RemoveRowCmdHandler;
use Procure\Application\Command\Doctrine\QR\UpdateHeaderCmdHandler;
use Procure\Application\Command\Doctrine\QR\UpdateRowCmdHandler;
use Procure\Application\Command\Doctrine\QR\UpdateRowInlineCmdHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class QRCmdHandlerFactory extends CmdHandlerAbstractFactory
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
        return new CloneAndSaveCmdHandler();
    }

    public function getRemoveRowCmdHandler()
    {
        return new RemoveRowCmdHandler();
    }
}