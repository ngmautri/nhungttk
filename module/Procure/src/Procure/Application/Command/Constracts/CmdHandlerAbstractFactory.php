<?php
namespace Procure\Application\Command\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class CmdHandlerAbstractFactory
{

    abstract function getCreateHeaderCmdHandler();

    abstract function getUpdateHeaderCmdHandler();

    abstract function getAddRowCmdHandler();

    abstract function getUpdateRowCmdHandler();

    abstract function getInlineUpdateRowCmdHandler();

    abstract function getPostCmdHandler();

    abstract function getReverseCmdHandler();
}
