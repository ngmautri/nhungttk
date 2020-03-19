<?php
namespace module\Procure\src\Procure\Domain\PurchaseOrder\Validator;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Procure\Domain\Exception\PoInvalidArgumentException;
use Procure\Domain\Service\FXServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractValidator
{

    protected $sharedSpecificationFactory;

    protected $fxService;

   /**
    * 
    * @param AbstractSpecificationFactory $sharedSpecificationFactory
    * @param FXServiceInterface $fxService
    * @throws PoInvalidArgumentException
    */
    public function __construct(AbstractSpecificationFactory $sharedSpecificationFactory, FXServiceInterface $fxService)
    {
        if ($sharedSpecificationFactory == null) {
            throw new PoInvalidArgumentException("Shared Specification is required");
        }

        if ($fxService == null) {
            throw new PoInvalidArgumentException("FX service is required");
        }

        $this->sharedSpecificationFactory = $sharedSpecificationFactory;
        $this->fxService = $fxService;
    }

    /**
     *
     * @return \Application\Domain\Shared\Specification\AbstractSpecificationFactory
     */
    public function getSharedSpecificationFactory()
    {
        return $this->sharedSpecificationFactory;
    }

    /**
     * @return \Procure\Domain\Service\FXServiceInterface
     */
    public function getFxService()
    {
        return $this->fxService;
    }

}

