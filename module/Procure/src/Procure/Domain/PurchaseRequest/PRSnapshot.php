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

    protected $id;

    protected $prAutoNumber;

    protected $prNumber;

    protected $prName;

    protected $keywords;

    protected $remarks;

    protected $createdOn;

    protected $lastChangeOn;

    protected $isDraft;

    protected $isActive;

    protected $status;

    protected $token;

    protected $checksum;

    protected $submittedOn;

    protected $currentState;

    protected $totalRowManual;

    protected $revisionNo;

    protected $docStatus;

    protected $workflowStatus;

    protected $transactionStatus;

    protected $docType;

    protected $reversalBlocked;

    protected $uuid;

    protected $createdBy;

    protected $lastChangeBy;

    protected $department;

    protected $company;

    protected $warehouse;
}