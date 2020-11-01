<?php
namespace Procure\Application\Command\PO\Factory;

use Procure\Application\Command\Contracts\CmdHandlerAbstractFactory;
use Procure\Application\Command\PO\CreateHeaderCmdHandler;
use Procure\Application\Command\PO\EditHeaderCmdHandler;
use Procure\Application\Command\PO\PostCmdHandler;
use Procure\Application\Command\PO\AddRowCmdHandler;
use Procure\Application\Command\PO\UpdateRowCmdHandler;
use Procure\Application\Command\PO\InlineUpdateRowCmdHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DefaultPOCmdHandlerFactory extends CmdHandlerAbstractFactory
{

    public function getCreateHeaderCmdHandler()
    {
        return new CreateHeaderCmdHandler();
    }

    public function getUpdateHeaderCmdHandler()
    {
        return new EditHeaderCmdHandler();
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
        return new AddRowCmdHandler();
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