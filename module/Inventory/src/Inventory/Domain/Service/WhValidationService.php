<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Service\Contracts\WhValidationServiceInterface;
use Inventory\Domain\Warehouse\Validator\Contracts\LocationValidatorCollection;
use Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorCollection;
use InvalidArgumentException;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WhValidationService implements WhValidationServiceInterface
{

    private $warehouseValidators;

    private $locationValidators;

    public function __construct(WarehouseValidatorCollection $warehouseValidators, LocationValidatorCollection $locationValidators = null)
    {
        if ($warehouseValidators == null) {
            throw new InvalidArgumentException("Warehouse Validator(s) is required");
        }

        $this->warehouseValidators = $warehouseValidators;
        $this->locationValidators = $locationValidators;
    }

    /**
     *
     * @return \Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorCollection
     */
    public function getWarehouseValidators()
    {
        return $this->warehouseValidators;
    }

    /**
     *
     * @return \Inventory\Domain\Warehouse\Validator\Contracts\LocationValidatorCollection
     */
    public function getLocationValidators()
    {
        return $this->locationValidators;
    }
}