<?php
namespace Inventory\Application\Specification\Inventory;

use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OnhandQuantityOfMovementSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $movementId = null;
        if (isset($subject["movementId"])) {
            $movementId = $subject["movementId"];
        }

        $itemId = null;
        if (isset($subject["itemId"])) {
            $itemId = $subject["itemId"];
        }
        $docQuantity = null;
        if (isset($subject["docQuantity"])) {
            $docQuantity = $subject["docQuantity"];
        }

        if ($this->doctrineEM == null || $itemId == null || $movementId == null || $docQuantity == null || $docQuantity < 0) {
            return false;
        }

        $rep = new TrxQueryRepositoryImpl($this->getDoctrineEM());
        $trx = $rep->getLazyRootEntityByTokenId($movementId);
        $onhand = $trx->getOnhandQuantityOf($itemId);

        return $onhand >= $docQuantity;
    }
}