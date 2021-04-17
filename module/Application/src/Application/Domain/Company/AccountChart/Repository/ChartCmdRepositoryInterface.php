<?php
namespace Application\Domain\Company\AccountChart\Repository;

use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface ChartCmdRepositoryInterface
{

    public function store(BaseChart $chart);

    public function remove(BaseChart $chart);

    public function storeAccount(BaseChart $chart, BaseAccount $account);

    public function removeAccount(BaseChart $chart, BaseAccount $account);
}
