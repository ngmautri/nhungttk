<?php
namespace Procure\Domain;

use Application\Domain\Company\CompanyVO;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Contracts\ProcureDocStatus;
use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

/**
 * Doc Snapshot
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DocSnapshot extends BaseDocSnapshot
{

    public function convertTo(DocSnapshot $targetObj)
    {
        if (! $targetObj instanceof DocSnapshot) {
            throw new InvalidArgumentException("Convertion input invalid!");
        }

        // Converting
        // ==========================
        $exculdedProps = [
            "id",
            "uuid",
            "token",
            "docRows",
            "rowIdArray",
            "instance",
            "sysNumber",
            "createdBy",
            "lastchangeBy",
            "docNumber",
            "docDate",
            "revisionNo",
            "docVersion"
        ];

        $sourceObj = $this;
        $reflectionClass = new \ReflectionClass(get_class($sourceObj));
        $props = $reflectionClass->getProperties();

        foreach ($props as $prop) {
            $prop->setAccessible(true);

            $propName = $prop->getName();
            if (property_exists($targetObj, $propName) && ! in_array($propName, $exculdedProps)) {
                $targetObj->$propName = $prop->getValue($sourceObj);
            }
        }
        return $targetObj;
    }

    /**
     *
     * @param int $createdBy
     * @param string $createdDate
     */
    public function init($createdBy, $createdDate)
    {
        $this->setCreatedOn($createdDate);
        $this->setCreatedBy($createdBy);
        $this->setDocStatus(ProcureDocStatus::DRAFT);

        $this->setIsActive(1);
        $this->setIsDraft(1);
        $this->setIsPosted(0);

        $this->setSysNumber(Constants::SYS_NUMBER_UNASSIGNED);
        $this->setRevisionNo(0);
        $this->setDocVersion(0);
        $this->setUuid(Uuid::uuid4()->toString());
        $this->setToken($this->getUuid());
    }

    public function initDoc(CommandOptions $options)
    {
        $createdDate = new \Datetime();
        $this->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setCreatedBy($options->getUserId());
        $this->setDocStatus(ProcureDocStatus::DRAFT);

        $this->setIsActive(1);
        $this->setIsDraft(1);
        $this->setIsPosted(0);

        $this->setSysNumber(Constants::SYS_NUMBER_UNASSIGNED);
        $this->setRevisionNo(0);
        $this->setDocVersion(0);
        $this->setUuid(Uuid::uuid4()->toString());
        $this->setToken($this->getUuid());

        /**
         *
         * @var CompanyVO $companyVO ;
         */

        $companyVO = $options->getCompanyVO();
        $this->setCompany($companyVO->getId());
        $this->setCurrency($this->getDocCurrency());
        $this->setLocalCurrency($companyVO->getDefaultCurrency());
    }

    public function markAsChange($createdBy, $createdDate)
    {
        $this->setLastchangeOn($createdDate);
        $this->setLastChangedByName($createdBy);
    }
}
