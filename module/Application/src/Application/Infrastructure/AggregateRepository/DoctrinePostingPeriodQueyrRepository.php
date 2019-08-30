<?php
namespace Application\Infrastructure\AggregateRepository;

use Application\Domain\Company\PostingPeriod\PostingPeriodQueryRepositoryInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrinePostingPeriodQueyrRepository extends AbstractDoctrineRepository implements PostingPeriodQueryRepositoryInterface
{

    /**
     * 
     * {@inheritDoc}
     * @see \Application\Domain\Company\PostingPeriod\PostingPeriodQueryRepositoryInterface::getPostingPeriodStatus()
     */
    public function getPostingPeriodStatus($postingDate)
    {
        $query = $this->_em->createQuery('SELECT p
        FROM Application\Entity\NmtFinPostingPeriod p
        WHERE p.postingFromDate <= :date AND p.postingToDate >= :date')->setParameter('date', $postingDate);
        $result = $query->getResult();

        if (count($result) == 1) {
            return $result[0];
        }
        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\PostingPeriod\PostingPeriodQueryRepositoryInterface::getPostingPeriod()
     */
    public function getPostingPeriod($postingDate)
    {
        $query = $this->_em->createQuery('SELECT p
        FROM Application\Entity\NmtFinPostingPeriod p
        WHERE p.postingFromDate <= :date AND p.postingToDate >= :date')->setParameter('date', $postingDate);
        $result = $query->getResult();

        if (count($result) == 1) {
            return $result[0]->getPeriodStatus();
        }
        return "Period not found";
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\PostingPeriod\PostingPeriodQueryRepositoryInterface::getLatestFX()
     */
    public function getLatestFX($sourceCurrencyId, $targetCurrencyId)
    {
        $sql = "
SELECT
*
FROM fin_fx
            
JOIN
(
    SELECT
    MAX(fin_fx.fx_date) AS fx_date,
    fin_fx.source_currency
    FROM fin_fx
    GROUP BY fin_fx.source_currency
)
AS  fin_fx1
ON fin_fx1.fx_date = fin_fx.fx_date AND fin_fx.source_currency = fin_fx1.source_currency
WHERE 1
";

        $sql = $sql . sprintf(" AND fin_fx.source_currency=%s and fin_fx.target_currency=%s", $sourceCurrencyId, $targetCurrencyId);

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinFx', 'fix_fx');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();
            return $result->getFxRate();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
