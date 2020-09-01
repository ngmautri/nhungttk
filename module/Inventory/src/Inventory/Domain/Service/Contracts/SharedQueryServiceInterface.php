<?php
namespace Inventory\Domain\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface SharedQueryServiceInterface
{

    public function getItemQueryRepository();

    public function getTrxQueryRepository();
}
