<?php
namespace Procure\Domain\APInvoice;

use Application\Notification;
use Procure\Domain\Service\APInvoiceSpecificationService;
use Procure\Domain\Service\APInvoicePostingService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericAPInvoice extends AbstractAPInvoice
{

    /**
     *
     * @var array
     */
    protected $invoiceRows;

    /**
     *
     * @var string
     */
    protected $rowsOutput;

    abstract protected function prePost(APInvoiceSpecificationService $specificationService, APInvoicePostingService $postingService, Notification $notification = null);

    abstract protected function doPost(APInvoiceSpecificationService $specificationService, APInvoicePostingService $postingService, Notification $notification = null);

    abstract protected function afterPost(APInvoiceSpecificationService $specificationService, APInvoicePostingService $postingService, Notification $notification = null);

    abstract protected function raiseEvent();

    /**
     *
     * @param APInvoiceSpecificationService $specificationService
     * @param Notification $notification
     * @param boolean $isPosting
     */
    public function validate(APInvoiceSpecificationService $specificationService, Notification $notification = null, $isPosting = false)
    {}

    /**
     *
     * @param APInvoiceSpecificationService $specificationService
     * @param Notification $notification
     * @param boolean $isPosting
     */
    public function validateHeader(APInvoiceSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {}

    /**
     *
     * @param APInvoiceSpecificationService $specificationService
     * @param Notification $notification
     * @param boolean $isPosting
     * @return \Application\Notification
     */
    protected function generalHeaderValidation(APInvoiceSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($specificationService == null) {
            $notification->addError("Specification not found");
            return $notification;
        }

        /**
         *
         * @var APInvoiceSpecificationService $specificationService ;
         */

        $notification = $specificationService->doGeneralHeaderValiation($this, $notification);

        return $notification;
    }

    /**
     *
     * @param APInvoiceSpecificationService $specificationService
     * @param ApInvoiceRow $row
     * @param Notification $notification
     * @param boolean $isPosting
     */
    public function validateRow(APInvoiceSpecificationService $specificationService, ApInvoiceRow $row, Notification $notification = null, $isPosting = false)
    {}
    
    protected function generalRowValidation(APInvoiceSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();
            
            if ($specificationService == null) {
                $notification->addError("Specification not found");
                return $notification;
            }
            
            /**
             *
             * @var APInvoiceSpecificationService $specificationService ;
             */
            
            $notification = $specificationService->doGeneralHeaderValiation($this, $notification);
            
            return $notification;
    }
    

    /**
     *
     * @param APInvoiceRow $row
     */
    public function addRow(APInvoiceRow $row)
    {
        $this->invoiceRows[] = $row;
    }

    /**
     *
     * @return string
     */
    public function getRowsOutput()
    {
        return $this->rowsOutput;
    }
    /**
     * @param string $rowsOutput
     */
    public function setRowsOutput($rowsOutput)
    {
        $this->rowsOutput = $rowsOutput;
    }


    
}