<?php
namespace Procure\Application\Command\Doctrine\GR\Factory;

use Procure\Application\Command\Contracts\CmdHandlerAbstractFactory;
use Procure\Application\Command\Doctrine\PR\CloneAndSaveCmdHandler;
use Procure\Application\Command\Doctrine\PR\CreateHeaderCmdHandler;
use Procure\Application\Command\Doctrine\PR\CreateRowCmdHandler;
use Procure\Application\Command\Doctrine\PR\PostCmdHandler;
use Procure\Application\Command\Doctrine\PR\UpdateHeaderCmdHandler;
use Procure\Application\Command\Doctrine\PR\UpdateRowCmdHandler;
use Procure\Application\Command\Doctrine\PR\UpdateRowInlineCmdHandler;

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