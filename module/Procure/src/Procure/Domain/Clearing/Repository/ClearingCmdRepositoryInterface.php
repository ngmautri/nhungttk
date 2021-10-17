<?php
namespace Procure\Domain\Clearing\Repository;

use Procure\Domain\Clearing\BaseClearingDoc;
use Procure\Domain\Clearing\BaseClearingRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ClearingCmdRepositoryInterface
{

    public function store(BaseClearingDoc $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function post(BaseClearingDoc $rootEntity, $generateSysNumber = True);

    public function storeHeader(BaseClearingDoc $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function storeRow(BaseClearingDoc $rootEntity, BaseClearingRow $localEntity, $isPosting = false);

    public function removeRow(BaseClearingDoc $rootEntity, BaseClearingRow $localEntity);
}
