<?php
namespace Procure\Infrastructure\Persistence\Doctrine;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\NoResultException;
use Procure\Infrastructure\Persistence\ProcureReportRepositoryInterface;
use Procure\Infrastructure\Persistence\SQL\ProcureSQL;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ProcureReportRepositoryImpl extends AbstractDoctrineRepository implements ProcureReportRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Infrastructure\Persistence\ProcureReportRepositoryInterface::getPriceOfItem()
     */
    public function getPriceOfItem($itemId, $itemToken)
    {
        $sql = sprintf(ProcureSQL::ITEM_PRICE_SQL, $itemId, $itemId, $itemId);

        try {
            $stmt = $this->_em->getConnection()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
