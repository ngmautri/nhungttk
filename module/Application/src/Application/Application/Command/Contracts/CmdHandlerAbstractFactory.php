<?php
namespace Application\Application\Command\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class CmdHandlerAbstractFactory
{

    abstract function getCreateEntityCmdHandler();

    abstract function getUpdateEntityCmdHandler();

    abstract function getCreateMemberCmdHandler();

    abstract function getUpdateMemberCmdHandler();

    abstract function getInlineUpdateMemberCmdHandler();

    abstract function getCloneCmdHandler();

    abstract function getRemoveMemberCmdHandler();
}
