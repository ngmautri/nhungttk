<?php
namespace Inventory\Domain\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface OpeningBalanceServiceInterface
{

    public function closeTrxOf($itemId);
}
