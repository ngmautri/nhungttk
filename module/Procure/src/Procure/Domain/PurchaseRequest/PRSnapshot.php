<?php
namespace Procure\Domain\PurchaseRequest;

use Application\Domain\Shared\AbstractValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRSnapshot extends AbstractValueObject
{

    public $id;

    public $prAutoNumber;

    public $prNumber;

    public $prName;

    public $keywords;

    public $remarks;

    public $createdOn;

    public $lastChangeOn;

    public $isDraft;

    public $isActive;

    public $status;

    public $token;

    public $checksum;

    public $submittedOn;

    public $currentState;

    public $totalRowManual;

    public $revisionNo;

    public $docStatus;

    public $workflowStatus;

    public $transactionStatus;

    public $docType;

    public $reversalBlocked;

    public $uuid;

    public $createdBy;

    public $lastChangeBy;

    public $department;

    public $company;

    public $warehouse;
}