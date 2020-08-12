<?php
namespace Inventory\Domain\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface WhValidationServiceInterface
{

    public function getWarehouseValidators();

    public function getLocationValidators();
}
