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
    protected $procureService;

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity ;
     * @param \Application\Entity\FinVendorInvoiceRow $r ;
     * @param \Application\Entity\MlaUsers $u ;
     */
    abstract public function doPosting($entity, $r, $u=null);

    /**
     *
     * @return \Procure\Service\AbstractProcureService
     */
    public function getProcureService()
    {
        return $this->procureService;
    }

    /**
     *
     * @param \Procure\Service\AbstractProcureService $procureService
     */
    public function setProcureService(\Procure\Service\AbstractProcureService $procureService)
    {
        $this->procureService = $procureService;
    }
}