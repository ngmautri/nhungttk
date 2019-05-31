<?php
namespace Inventory\Application\Specification\Doctrine;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionExitsSpecification extends DoctrineSpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        if ($this->doctrineEM == null || $subject == null || $subject == "")
            return false;

        /**
         *
         * @var \Application\Entity\NmtInventoryMv $mv ;
         */
        $mv = $this->doctrineEM->find("\Application\Entity\NmtInventoryMv", $subject);
        return (! $mv == null);
    }
}