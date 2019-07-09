<?php
namespace Application\Application\Specification\Zend;

use Application\Domain\Company\PostingPeriod\PostingPeriodstatus;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CanPostOnDateSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $companyId = null;
        if (isset($subject["companyId"])) {
            $companyId = $subject["companyId"];
        }

        $movementDate = null;
        if (isset($subject["movementDate"])) {
            $movementDate = $subject["movementDate"];
        }

        if ($this->doctrineEM == null || $companyId == null || $movementDate == null)
            return false;

        $spec = new DateSpecification();
        if (! $spec->isSatisfiedBy($movementDate))
            return false;

        /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod ;*/
        $postingPeriod = null;

        $sql_tmp = "
SELECT * FROM nmt_fin_posting_period
WHERE nmt_fin_posting_period.posting_from_date <= '%s' 
AND nmt_fin_posting_period.posting_to_date >= '%s' 
AND company_id=%s";

        $sql = sprintf($sql_tmp, $movementDate, $movementDate, $companyId);

        try {
            $rsm = new ResultSetMappingBuilder($this->doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtFinPostingPeriod', 'nmt_fin_posting_period');
            $query = $this->doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();

            if (count($result) == 1) {
                $postingPeriod = $result[0];
            }

            if ($postingPeriod == null)
                return false;

            if ($postingPeriod->getPeriodStatus() == PostingPeriodstatus::PERIOD_STATUS_CLOSED)
                return false;

            return true;
        } catch (NoResultException $e) {
            return false;
        }
    }

}