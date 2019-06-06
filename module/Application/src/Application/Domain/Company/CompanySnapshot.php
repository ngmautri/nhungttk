<?php
namespace Application\Domain\Company;

use Application\Domain\Shared\AbstractValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CompanySnapshot extends AbstractValueObject
{

    public $id;

    public $companyCode;

    public $companyName;

    public $defaultLogoId;

    public $status;

    public $createdOn;

    public $isDefault;

    public $token;

    public $lastChangeOn;

    public $revisionNo;

    public $uuid;

    public $defaultCurrency;

    public $createdBy;

    public $country;

    public $defaultAddress;

    public $lastChangeBy;

    public $defaultWarehouse;
}