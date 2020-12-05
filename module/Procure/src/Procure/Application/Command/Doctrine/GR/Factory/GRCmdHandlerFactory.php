<?php
namespace Procure\Application\Command\Doctrine\GR\Factory;

use Procure\Application\Command\Contracts\CmdHandlerAbstractFactory;
use Procure\Application\Command\Doctrine\GR\CreateHeaderCmdHandler;
use Procure\Application\Command\Doctrine\GR\CreateRowCmdHandler;
use Procure\Application\Command\Doctrine\GR\PostCmdHandler;
use Procure\Application\Command\Doctrine\GR\UpdateHeaderCmdHandler;
use Procure\Application\Command\Doctrine\GR\UpdateRowCmdHandler;
use Procure\Application\Command\Doctrine\GR\UpdateRowInlineCmdHandler;
use Procure\Application\Command\Doctrine\PR\CloneAndSaveCmdHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRCmdHandlerFactory extends CmdHandlerAbstractFactory
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
}