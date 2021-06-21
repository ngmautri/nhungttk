<?php
namespace Application\Application\Command\Doctrine\Company\Brand\Factory;

use Application\Application\Command\Contracts\EntityCmdHandlerAbstractFactory;
use Application\Application\Command\Doctrine\Company\Brand\CreateBrandCmdHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BrandCmdHandlerFactory extends EntityCmdHandlerAbstractFactory
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
        return new CreateBrandCmdHandler();
    }

    public function getCreateMemberCmdHandler()
    {}

    public function getRemoveMemberCmdHandler()
    {}
}