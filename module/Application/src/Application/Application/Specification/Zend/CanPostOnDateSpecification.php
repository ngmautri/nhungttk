<?php
namespace Application\Application\Specification\Zend;

use Application\Domain\Company\PostingPeriod\PostingPeriodstatus;

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
        if ($this->doctrineEM == null)
            return false;

        $spec = new DateSpecification();
        if (! $spec->isSatisfiedBy($subject))
            return false;

        /** @var \Application\Entity\NmtFinPostingPeriod $postingPeriod ;*/
        $postingPeriod = null;

        $query = $this->doctrineEM->createQuery('SELECT p
        FROM Application\Entity\NmtFinPostingPeriod p
        WHERE p.postingFromDate <= :date AND p.postingToDate >= :date')->setParameter('date', $subject);
        $result = $query->getResult();

        if (count($result) == 1) {
            $postingPeriod = $result[0];
            //var_dump($postingPeriod->getPeriodName());
        }

        if ($postingPeriod == null)
            return false;

        if ($postingPeriod->getPeriodStatus() == PostingPeriodstatus::PERIOD_STATUS_CLOSED)
            return false;

        return true;
    }
}