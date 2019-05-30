<?php
namespace Inventory\Application\DTO\Warehouse\Transaction;

use Application\Notification;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Inventory\Application\Specification\Doctrine\DoctrineSpecificationFactory;
use Inventory\Domain\Warehouse\Transaction\TransactionType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 * @var \Application\Entity\NmtInventoryMv ;
 */
class TransactionDTO
{

    /**
     *
     * @system_genereted
     */
    public $id;

    /**
     *
     * @system_genereted
     */
    public $token;

    public $currencyIso3;

    public $exchangeRate;

    public $remarks;

    /**
     *
     * @system_genereted
     */
    public $createdOn;

    /**
     *
     * @system_genereted
     */
    public $currentState;

    public $isActive;

    public $trxType;

    /**
     *
     * @system_genereted
     */
    public $lastchangeBy;

    /**
     *
     * @system_genereted
     */
    public $lastchangeOn;

    public $postingDate;

    public $sapDoc;

    public $contractNo;

    public $contractDate;

    public $quotationNo;

    public $quotationDate;

    /**
     *
     * @system_genereted
     */
    public $sysNumber;

    /**
     *
     * @system_genereted
     */
    public $revisionNo;

    public $deliveryMode;

    public $incoterm;

    public $incotermPlace;

    public $paymentTerm;

    public $paymentMethod;

    /**
     *
     * @system_genereted
     */
    public $docStatus;

    public $isDraft;

    /**
     *
     * @system_genereted
     */
    public $workflowStatus;

    /**
     *
     * @system_genereted
     */
    public $transactionStatus;

    public $movementType;

    public $movementDate;

    public $journalMemo;

    public $movementFlow;

    public $movementTypeMemo;

    /**
     *
     * @system_genereted
     */
    public $isPosted;

    /**
     *
     * @system_genereted
     */
    public $isReversed;

    /**
     *
     * @system_genereted
     */
    public $reversalDate;

    /**
     *
     * @system_genereted
     */
    public $reversalDoc;

    public $reversalReason;

    /**
     *
     * @system_genereted
     */
    public $isReversable;

    /**
     *
     * @system_genereted
     */
    public $docType;

    /**
     *
     * @system_genereted
     */
    public $isTransferTransaction;

    /**
     *
     * @system_genereted
     */
    public $reversalBlocked;

    /**
     *
     * @system_genereted
     */
    public $createdBy;

    public $warehouse;

    public $postingPeriod;

    public $currency;

    public $docCurrency;

    public $localCurrency;

    public $targetWarehouse;

    public $sourceLocation;

    public $tartgetLocation;

    /**
     *
     * @return \Application\Notification
     */
    public function validate($doctrineEM = null)
    {
        $notification = new Notification();
        $specFactory = new ZendSpecificationFactory();
        $specFactory1 = new DoctrineSpecificationFactory($doctrineEM);

        if ($specFactory->getNullorBlankSpecification()->isSatisfiedBy($this->warehouse)) {
            $notification->addError("Warehouse is empty");
        } else {
            if ($specFactory1->getWarehouseExitsSpecification()->isSatisfiedBy($this->warehouse) == False)
                $notification->addError("Warehouse not exits...");
        }

        if (! $specFactory->getDateSpecification()->isSatisfiedBy($this->movementDate))
            $notification->addError("Transaction date is not correct or empty");

        if ($specFactory->getNullorBlankSpecification()->isSatisfiedBy($this->movementType)) {
            $notification->addError("Transaction Type is not correct or empty");
        } else {
            $supportedType = TransactionType::getSupportedTransaction();
            if (! in_array($this->movementType, $supportedType)) {
                $notification->addError("Transaction Type is not supported");
            }
        }

        return $notification;
    }
}
