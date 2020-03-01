<?php
namespace Procure\Domain\APInvoice;

use Application\Notification;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\APSpecificationService;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericAPDoc extends AbstractAPDoc
{

    /**
     *
     * @var array
     */
    protected $apDocRows;

    /**
     *
     * @var string
     */
    protected $rowsOutput;

    abstract protected function prePost(APSpecificationService $specificationService, APPostingService $postingService);

    abstract protected function doPost(APSpecificationService $specificationService, APPostingService $postingService);

    abstract protected function afterPost(APSpecificationService $specificationService, APPostingService $postingService);

    abstract protected function raiseEvent();

    abstract protected function specificHeaderValidation(APSpecificationService $specificationService, $isPosting = false);

    abstract protected function specificRowValidation(ApDocRow $row, APSpecificationService $specificationService, $isPosting = false);

    abstract protected function specificValidation(APSpecificationService $specificationService, $isPosting = false);

    abstract protected function preReserve(APSpecificationService $specificationService, APPostingService $postingService);

    abstract protected function doReverse(APSpecificationService $specificationService, APPostingService $postingService);

    abstract protected function afterReserve(APSpecificationService $specificationService, APPostingService $postingService);

    /**
     *
     * @param APSpecificationService $specificationService
     * @param APPostingService $postingService
     * @param Notification $notification
     */
    public function reverse(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null)
    {
        if ($specificationService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        if ($notification == null) {
            $notification = new Notification();
        }

        $validationResult = $this->validate($specificationService, $notification, TRUE);
        if ($validationResult instanceof Notification) {
            if ($validationResult->hasErrors()) {
                return $validationResult;
            }
        }

        $this->recordedEvents = array();
        $doReverseNotify = $this->doReverse($specificationService, $postingService, $notification);

        if (! $doReverseNotify == null) {
            $notification = $doReverseNotify;
        }

        return $notification;
    }

    /**
     *
     * @param APSpecificationService $specificationService
     * @param APPostingService $postingService
     * @param Notification $notification
     * @throws InvalidArgumentException
     * @return \Application\Notification
     */
    public function post(APSpecificationService $specificationService, APPostingService $postingService)
    {
        if ($specificationService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        $this->validate($specificationService, TRUE);

        $this->recordedEvents = array();

        $prePostNotify = $this->prePost($specificationService, $postingService, $notification);
        if (! $prePostNotify == null) {
            $notification = $prePostNotify;
        }

        $doPostNotify = $this->doPost($specificationService, $postingService, $notification);

        if (! $doPostNotify == null) {
            $notification = $doPostNotify;
        }

        $afterPostNotify = $this->afterPost($specificationService, $postingService, $notification);

        if (! $afterPostNotify == null) {
            $notification = $afterPostNotify;
        }

        if (! $notification->hasErrors()) {
            $this->raiseEvent();
        }

        return $notification;
    }

    /**
     *
     * @param APSpecificationService $specificationService
     * @param Notification $notification
     * @param boolean $isPosting
     */
    public function validate(APSpecificationService $specificationService, $isPosting = false)
    {
        if ($specificationService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        $this->validateHeader($specificationService, $isPosting);

        if ($notification->hasErrors()) {
            return $notification;
        }

        $specificValidationResult = $this->specificValidation($specificationService, $notification, $isPosting);
        if ($specificValidationResult instanceof Notification) {
            $notification = $specificValidationResult;
        }

        if ($notification->hasErrors()) {
            return $notification;
        }

        if (count($this->apDocRows) == 0) {
            $notification->addError("AP Doc has no lines");
            return $notification;
        }

        foreach ($this->apDocRows as $row) {

            $checkRowResult = $this->validateRow($row, $specificationService, $notification, $isPosting);

            if ($checkRowResult !== null) {
                $notification = $checkRowResult;
            }
        }

        return $notification;
    }

    /**
     *
     * @param APSpecificationService $specificationService
     * @param Notification $notification
     * @param boolean $isPosting
     */
    public function validateHeader(APSpecificationService $specificationService, $isPosting = false)
    {
        try {

            if ($specificationService == null) {
                $this->addError("Specification service not found");
                return $this;
            }

            // general validation done
            $this->generalHeaderValidation($specificationService, $isPosting);

            if ($this->hasErrors()) {
                return $this;
            }

            // Check and set exchange rate
            // if ($isPosting) {
            $fxRate = $specificationService->getFxService()->checkAndReturnFX($this->getDocCurrency(), $this->getLocalCurrency(), $this->getExchangeRate());
            $this->exchangeRate = $fxRate;
            // }

            // specific validation
            $this->specificHeaderValidation($specificationService, $isPosting);
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }

        return $this;
    }

    /**
     * Default implementation
     *
     * @param APSpecificationService $specificationService
     * @param Notification $notification
     * @param boolean $isPosting
     * @return \Application\Notification
     */
    protected function generalHeaderValidation(APSpecificationService $specificationService, $isPosting = false)
    {
        if ($specificationService == null) {
            $this->addError("Specification not found");
            return $this;
        }

        $specificationService->doGeneralHeaderValiation($this, $isPosting);

        return $this;
    }

    /**
     *
     * @param APSpecificationService $specificationService
     * @param ApDocRow $row
     * @param Notification $notification
     * @param boolean $isPosting
     */
    public function validateRow(ApDocRow $row, APSpecificationService $specificationService, $isPosting = false)
    {
        try {

            if ($specificationService == null) {
                $this->addError("Specification service not found");
                return $this;
            }

            $this->generalRowValidation($row, $specificationService, $isPosting);

            if ($this->hasErrors()) {
                return $this;
            }

            // specific validation
            $this->specificRowValidation($row, $specificationService, $isPosting);
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }

        return $this;
    }

    /**
     *
     * @param APSpecificationService $specificationService
     * @param Notification $notification
     * @param boolean $isPosting
     * @return \Application\Notification
     */
    protected function generalRowValidation(ApDocRow $row, APSpecificationService $specificationService, $isPosting = false)
    {
        if ($specificationService == null) {
            $this->addError("Specification not found");
            return $this;
        }
        $specificationService->doGeneralRowValiation($this, $row, $isPosting);

        return $this;
    }

    /**
     *
     * @param APSpecificationService $specificationService
     * @param APDocRowSnapshot $snapshot
     * @param Notification $notification
     * @throws InvalidArgumentException
     * @return NULL|\Procure\Domain\APInvoice\GenericAPDoc
     */
    public function addRowFromSnapshot(APDocRowSnapshot $snapshot, APSpecificationService $specificationService)
    {
        if (! $snapshot instanceof APDocRowSnapshot)
            return null;

        // $snapshot-> = $this->uuid;
        $snapshot->docType = $this->docType;
        $snapshot->transactionType = $this->docType;
        $snapshot->quantity = $snapshot->docQuantity;
        $snapshot->wh = $this->warehouse;

        $row = new APDocRow();
        $row->makeFromSnapshot($snapshot);

        $this->validateRow($row, $specificationService, false);

        if ($this->hasErrors()) {
            return $this;
        }

        $convertedPurchaseQuantity = $row->getDocQuantity();
        $convertedPurchaseUnitPrice = $row->getDocUnitPrice();

        $conversionFactor = $row->getConversionFactor();
        $standardCF = $row->getConversionFactor();

        // converted to purchase quantity
        $convertedPurchaseQuantity = $convertedPurchaseQuantity * $conversionFactor;
        $convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice / $conversionFactor;

        $convertedStandardQuantity = $entity->getDocQuantity();
        $convertedStandardUnitPrice = $entity->getDocUnitPrice();

        $this->apDocRows[] = $row;
        return $this;
    }

    /**
     *
     * @param ApDocRow $row
     */
    public function addRow(ApDocRow $row)
    {
        $this->apDocRows[] = $row;
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
     *
     * @param string $rowsOutput
     */
    public function setRowsOutput($rowsOutput)
    {
        $this->rowsOutput = $rowsOutput;
    }
}