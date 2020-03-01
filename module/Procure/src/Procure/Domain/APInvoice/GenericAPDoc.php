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

    abstract protected function prePost(APSpecificationService $specService, APPostingService $postingService);

    abstract protected function doPost(APSpecificationService $specService, APPostingService $postingService);

    abstract protected function afterPost(APSpecificationService $specService, APPostingService $postingService);

    abstract protected function raiseEvent();

    abstract protected function specificHeaderValidation(APSpecificationService $specService, $isPosting = false);

    abstract protected function specificRowValidation(ApDocRow $row, APSpecificationService $specService, $isPosting = false);

    abstract protected function specificValidation(APSpecificationService $specService, $isPosting = false);

    abstract protected function preReserve(APSpecificationService $specService, APPostingService $postingService);

    abstract protected function doReverse(APSpecificationService $specService, APPostingService $postingService);

    abstract protected function afterReserve(APSpecificationService $specService, APPostingService $postingService);

    /**
     *
     * @param APSpecificationService $specService
     * @param APPostingService $postingService
     * @throws InvalidArgumentException
     * @return \Procure\Domain\APInvoice\GenericAPDoc
     */
    public function reverse(APSpecificationService $specService, APPostingService $postingService)
    {
        if ($specService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        $this->validate($specService, TRUE);

        if ($this->hasErrors()) {
            throw new InvalidArgumentException("Invalid document!");
        }

        $this->recordedEvents = array();
        $this->doReverse($specService, $postingService);
        return $this;
    }

    /**
     *
     * @param APSpecificationService $specService
     * @param APPostingService $postingService
     * @throws InvalidArgumentException
     * @return \Procure\Domain\APInvoice\GenericAPDoc
     */
    public function post(APSpecificationService $specService, APPostingService $postingService)
    {
        if ($specService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        $this->validate($specService, TRUE);

        if ($this->hasErrors()) {
            throw new InvalidArgumentException("Invalid document!");
        }

        $this->recordedEvents = array();

        // template method
        $this->prePost($specService, $postingService);

        // template method
        $this->doPost($specService, $postingService);

        // template method
        $this->afterPost($specService, $postingService);

        if (! $this->hasErrors()) {
            $this->raiseEvent();
        }

        return $this;
    }

    /**
     *
     * @param APSpecificationService $specService
     * @param Notification $notification
     * @param boolean $isPosting
     */
    public function validate(APSpecificationService $specService, $isPosting = false)
    {
        if ($specService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        $this->validateHeader($specService, $isPosting);

        if ($this->hasErrors()) {
            return $this;
        }

        // template method.
        $this->specificValidation($specService, $isPosting);

        if ($this->hasErrors()) {
            return $this;
        }

        if (count($this->apDocRows) == 0) {
            $this->addError("AP Doc has no lines");
            return $this;
        }

        foreach ($this->apDocRows as $row) {
            $this->validateRow($row, $specService, $isPosting);
        }

        return $this;
    }

    /**
     *
     * @param APSpecificationService $specService
     * @param boolean $isPosting
     * @return \Procure\Domain\APInvoice\GenericAPDoc
     */
    public function validateHeader(APSpecificationService $specService, $isPosting = false)
    {
        try {

            if ($specService == null) {
                $this->addError("Specification service not found");
                return $this;
            }

            // general validation done
            $this->generalHeaderValidation($specService, $isPosting);

            if ($this->hasErrors()) {
                return $this;
            }

            // Check and set exchange rate
            // if ($isPosting) {
            $fxRate = $specService->getFxService()->checkAndReturnFX($this->getDocCurrency(), $this->getLocalCurrency(), $this->getExchangeRate());
            $this->exchangeRate = $fxRate;
            // }

            // specific validation
            $this->specificHeaderValidation($specService, $isPosting);
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }

        return $this;
    }

    /**
     *
     * @param APSpecificationService $specService
     * @param boolean $isPosting
     * @return \Procure\Domain\APInvoice\GenericAPDoc
     */
    protected function generalHeaderValidation(APSpecificationService $specService, $isPosting = false)
    {
        if ($specService == null) {
            $this->addError("Specification not found");
            return $this;
        }

        $specService->doGeneralHeaderValiation($this, $isPosting);

        return $this;
    }

    /**
     *
     * @param ApDocRow $row
     * @param APSpecificationService $specService
     * @param boolean $isPosting
     * @return \Procure\Domain\APInvoice\GenericAPDoc
     */
    public function validateRow(ApDocRow $row, APSpecificationService $specService, $isPosting = false)
    {
        try {

            if ($specService == null) {
                $this->addError("Specification service not found");
                return $this;
            }

            $this->generalRowValidation($row, $specService, $isPosting);

            if ($this->hasErrors()) {
                return $this;
            }

            // specific validation
            $this->specificRowValidation($row, $specService, $isPosting);
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }

        return $this;
    }

    /**
     *
     * @param ApDocRow $row
     * @param APSpecificationService $specService
     * @param boolean $isPosting
     * @return \Procure\Domain\APInvoice\GenericAPDoc
     */
    protected function generalRowValidation(ApDocRow $row, APSpecificationService $specService, $isPosting = false)
    {
        if ($specService == null) {
            $this->addError("Specification not found");
            return $this;
        }
        $specService->doGeneralRowValiation($this, $row, $isPosting);

        return $this;
    }

   /**
    * 
    * @param APDocRowSnapshot $snapshot
    * @param APSpecificationService $specService
    * @return NULL|\Procure\Domain\APInvoice\GenericAPDoc
    */
    public function addRowFromSnapshot(APDocRowSnapshot $snapshot, APSpecificationService $specService)
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

        $this->validateRow($row, $specService, false);

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