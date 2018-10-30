<?php
namespace Procure\Model\Ap;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractAPRowPostingStrategy
{

    // Context
    protected $contextService;

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     *
     * @param \Application\Entity\FinVendorInvoiceRow $r
     *
     * @param \Application\Entity\MlaUsers $u
     *
     * @param boolean $isPosting
     */
    abstract public function doPosting($entity, $r, $u = null, $isPosting = 1);

    /**
     *
     * @return \Application\Service\AbstractService
     */
    public function getContextService()
    {
        return $this->contextService;
    }

    /**
     *
     * @param \Application\Service\AbstractService $contextService
     */
    public function setContextService(\Application\Service\AbstractService $contextService)
    {
        $this->contextService = $contextService;
    }
}