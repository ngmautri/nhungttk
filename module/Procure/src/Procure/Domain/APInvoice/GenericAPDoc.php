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

    abstract protected function prePost(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null);

    abstract protected function doPost(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null);

    abstract protected function afterPost(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null);

    abstract protected function raiseEvent();

    abstract protected function specificHeaderValidation(APSpecificationService $specificationService, Notification $notification, $isPosting = false);

    abstract protected function specificRowValidation(ApDocRow $row, APSpecificationService $specificationService, Notification $notification, $isPosting = false);

    abstract protected function specificValidation(APSpecificationService $specificationService, Notification $notification, $isPosting = false);

    abstract protected function preReserve(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null);

    abstract protected function doReverse(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null);

    abstract protected function afterReserve(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null);

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
    public function post(APSpecificationService $specificationService, APPostingService $postingService, Notification $notification = null)
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
    public function validate(APSpecificationService $specificationService, Notification $notification = null, $isPosting = false)
    {
        if ($specificationService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        if ($notification == null) {
            $notification = new Notification();
        }

        $notification = $this->validateHeader($specificationService, $notification, $isPosting);

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
    public function validateHeader(APSpecificationService $specificationService, Notification $notification = null, $isPosting = false)
    {
        try {

            if ($notification == null) {
                $notification = new Notification();
            }

            if ($specificationService == null) {
                $notification->addError("Specification service not found");
                return $notification;
            }

            // general validation done
            $notification = $this->generalHeaderValidation($specificationService, $notification, $isPosting);

            if ($notification->hasErrors()) {
                return $notification;
            }

            // Check and set exchange rate
            // if ($isPosting) {
            $fxRate = $specificationService->getFxService()->checkAndReturnFX($this->getDocCurrency(), $this->getLocalCurrency(), $this->getExchangeRate());
            $this->exchangeRate = $fxRate;
            // }

            // specific validation
            $specificHeaderValidationResult = $this->specificHeaderValidation($specificationService, $notification, $isPosting);

            if ($specificHeaderValidationResult instanceof Notification) {
                $notification = $specificHeaderValidationResult;
            }
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }

        return $notification;
    }

    /**
     * Default implementation
     *
     * @param APSpecificationService $specificationService
     * @param Notification $notification
     * @param boolean $isPosting
     * @return \Application\Notification
     */
    protected function generalHeaderValidation(APSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {
        if ($notification == null) {
            $notification = new Notification();
        }

        if ($specificationService == null) {
            $notification->addError("Specification not found");
            return $notification;
        }

        $notification = $specificationService->doGeneralHeaderValiation($this, $notification, $isPosting);

        return $notification;
    }

    /**
     *
     * @param APSpecificationService $specificationService
     * @param ApDocRow $row
     * @param Notification $notification
     * @param boolean $isPosting
     */
    public function validateRow(ApDocRow $row, APSpecificationService $specificationService, Notification $notification = null, $isPosting = false)
    {
        try {

            if ($notification == null) {
                $notification = new Notification();
            }

            if ($specificationService == null) {
                $notification->addError("Specification service not found");
                return $notification;
            }

            // general validation done
            $notification = $this->generalRowValidation($row, $specificationService, $notification, $isPosting);

            if ($notification->hasErrors()) {
                return $notification;
            }

            // specific validation
            $specificRowValidationResult = $this->specificRowValidation($row, $specificationService, $notification, $isPosting);

            if ($specificRowValidationResult instanceof Notification) {
                $notification = $specificRowValidationResult;
            }
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }

        return $notification;
    }

    /**
     *
     * @param APSpecificationService $specificationService
     * @param Notification $notification
     * @param boolean $isPosting
     * @return \Application\Notification
     */
    protected function generalRowValidation(ApDocRow $row, APSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {
        if ($notification == null) {
            $notification = new Notification();
        }

        if ($specificationService == null) {
            $notification->addError("Specification not found");
            return $notification;
        }
        $notification = $specificationService->doGeneralRowValiation($this, $row, $notification, $isPosting);

        return $notification;
    }

    /**
     *
     * @param APSpecificationService $specificationService
     * @param APDocRowSnapshot $snapshot
     * @param Notification $notification
     * @throws InvalidArgumentException
     * @return NULL|\Procure\Domain\APInvoice\GenericAPDoc
     */
    public function addRowFromSnapshot(APDocRowSnapshot $snapshot, APSpecificationService $specificationService, Notification $notification = null)
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

        $ckResult = $this->validateRow($row, $specificationService, $notification, false);
        if ($ckResult->hasErrors()) {
            throw new InvalidArgumentException($ckResult->errorMessage());
        }

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