<?php
namespace Inventory\Domain\Validator\Association;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory as SharedSpecsFactory;
use Inventory\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractAssociationValidator
{

    protected $sharedSpecificationFactory;

    /**
     *
     * @param \Application\Domain\Shared\Specification\AbstractSpecificationFactory $sharedSpecsFactory
     * @throws \Procure\Domain\Exception\PoInvalidArgumentException
     */
    public function __construct(SharedSpecsFactory $sharedSpecsFactory)
    {
        if (! $sharedSpecsFactory instanceof SharedSpecsFactory) {
            throw new InvalidArgumentException("Shared Specification is required");
        }

        $this->sharedSpecificationFactory = $sharedSpecsFactory;
    }

    /**
     *
     * @return \Application\Domain\Shared\Specification\AbstractSpecificationFactory
     */
    public function getSharedSpecificationFactory()
    {
        return $this->sharedSpecificationFactory;
    }
}

