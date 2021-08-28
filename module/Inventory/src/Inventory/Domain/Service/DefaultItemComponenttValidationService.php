<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Item\Component\Validator\Contracts\ComponentValidatorCollection;
use Inventory\Domain\Service\Contracts\ItemComponentValidationServiceInterface;
use Inventory\Domain\Validator\Item\ItemValidatorCollection;

/**
 * Item Component Validation Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultItemComponenttValidationService implements ItemComponentValidationServiceInterface
{

    protected $itemValidators;

    protected $componentValidators;

    public function __construct(ItemValidatorCollection $itemValidators, ComponentValidatorCollection $componentValidators = null)
    {
        if ($itemValidators == null) {
            throw new InvalidArgumentException("Item Validator(s) is required");
        }

        $this->itemValidators = $itemValidators;
        $this->componentValidators = $componentValidators;
    }

    public function getComponentValidators()
    {
        return $this->componentValidators;
    }

    public function getItemValidators()
    {
        return $this->itemValidators;
    }
}