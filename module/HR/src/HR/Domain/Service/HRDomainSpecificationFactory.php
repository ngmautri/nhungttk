<?php
namespace HR\Domain\Service;

use HR\Domain\Validator\AbstractContractSpecificationFactory;
use HR\Domain\Validator\AbstractIndividualSpecificationFactory;

/**
 * HR Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HRDomainSpecificationFactory
{

    protected $individualSpecificationFactory;

    protected $contractSpecificationFactory;

    /**
     *
     * @return \HR\Domain\Validator\AbstractIndividualSpecificationFactory
     */
    public function getIndividualSpecificationFactory()
    {
        return $this->individualSpecificationFactory;
    }

    /**
     *
     * @return \HR\Domain\Validator\AbstractContractSpecificationFactory
     */
    public function getContractSpecificationFactory()
    {
        return $this->contractSpecificationFactory;
    }

    /**
     *
     * @param mixed $individualSpecificationFactory
     */
    public function setIndividualSpecificationFactory(AbstractIndividualSpecificationFactory $individualSpecificationFactory)
    {
        $this->individualSpecificationFactory = $individualSpecificationFactory;
    }

    /**
     *
     * @param mixed $contractSpecificationFactory
     */
    public function setContractSpecificationFactory(AbstractContractSpecificationFactory $contractSpecificationFactory)
    {
        $this->contractSpecificationFactory = $contractSpecificationFactory;
    }
}
