<?php
namespace Inventory\Domain\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface SharedCmdServiceInterface
{

    public function getItemCmdRepository();

    public function getTrxCmdRepository();
}
