<?php
namespace Application\Application\Command\Doctrine\Company\AccountChart\Factory;

use Application\Application\Command\Contracts\EntityCmdHandlerAbstractFactory;
use Application\Application\Command\Doctrine\Company\AccountChart\CreateAccountCmdHandler;
use Application\Application\Command\Doctrine\Company\AccountChart\CreateChartCmdHandler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ChartCmdHandlerFactory extends EntityCmdHandlerAbstractFactory
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
        return new CreateChartCmdHandler();
    }

    public function getCreateMemberCmdHandler()
    {
        return new CreateAccountCmdHandler();
    }

    public function getRemoveMemberCmdHandler()
    {}
}