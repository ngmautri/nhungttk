<?php
namespace Procure\Application\Command\Doctrine\AP\Factory;

use Procure\Application\Command\Contracts\CmdHandlerAbstractFactory;
use Procure\Application\Command\Doctrine\AP\CreateHeaderCmdHandler;
use Procure\Application\Command\Doctrine\AP\CreateRowCmdHandler;
use Procure\Application\Command\Doctrine\AP\PostCmdHandler;
use Procure\Application\Command\Doctrine\AP\UpdateHeaderCmdHandler;
use Procure\Application\Command\Doctrine\AP\UpdateRowCmdHandler;
use Procure\Application\Command\Doctrine\AP\UpdateRowInlineCmdHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APCmdHandlerFactory extends CmdHandlerAbstractFactory
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