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
    protected $recordedEvents;

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
     * @param ApInvoiceRow $row
     * @param Notification $notification
     * @param boolean $isPosting
     */
    public function validateRow(APInvoiceSpecificationService $specificationService, ApInvoiceRow $row, Notification $notification = null, $isPosting = false)
    {}

    /**
     *
     * @return array
     */
    public function getRecordedEvents()
    {
        return $this->recordedEvents;
    }
}