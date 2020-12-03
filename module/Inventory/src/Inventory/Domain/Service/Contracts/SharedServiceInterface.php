<?php
namespace Inventory\Domain\Service\Contracts;

use Procure\Domain\Service\Contracts\SharedServiceInterface as ProcureSharedServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface SharedServiceInterface extends ProcureSharedServiceInterface
{

    public function getValuationService();
}
