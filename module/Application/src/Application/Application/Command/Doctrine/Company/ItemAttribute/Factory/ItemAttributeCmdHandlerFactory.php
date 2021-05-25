<?php
namespace Application\Application\Command\Doctrine\Company\ItemAttribute\Factory;

use Application\Application\Command\Contracts\EntityCmdHandlerAbstractFactory;
use Application\Application\Command\Doctrine\Company\ItemAttribute\CreateAttributeGroupCmdHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAttributeCmdHandlerFactory extends EntityCmdHandlerAbstractFactory
{

    public function getUpdateMemberCmdHandler()
    {}

    public function getUpdateEntityCmdHandler()
    {}

    public function getCloneCmdHandler()
    {}

    public function getInlineUpdateMemberCmdHandler()
    {}

    public function getCreateEntityCmdHandler()
    {
        return new CreateAttributeGroupCmdHandler();
    }

    public function getCreateMemberCmdHandler()
    {}

    public function getRemoveMemberCmdHandler()
    {}
}