<?php
namespace Inventory\Domain\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ItemComponentServiceInterface
{

    public function getItemValidators();

    public function getComponentValidators();
}
