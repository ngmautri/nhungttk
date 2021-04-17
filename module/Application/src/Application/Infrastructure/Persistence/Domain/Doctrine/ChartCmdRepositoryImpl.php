<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ChartCmdRepositoryImpl extends AbstractDoctrineRepository implements ChartCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\AppCoa";

    const DEPT_ENTITY_NAME = "\Application\Entity\AppCoaAccount";

    public function store(BaseChart $chart)
    {}

    public function removeAccount(BaseChart $chart, BaseAccount $account)
    {}

    public function remove(BaseChart $chart)
    {}

    public function storeAccount(BaseChart $chart, BaseAccount $account)
    {}
}
