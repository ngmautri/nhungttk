<?php
namespace Procure\Model\DomainDriven\Repository\Doctrine;

/**
 *
 * @author Nguyen Mau Tri
 *        
 *@ORM\Entity
 */
class DoctrinePurchaseRequestRepository implements InterfacePurchaseRequestRepository
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Model\DomainDriven\Repository\Doctrine\InterfacePurchaseRequestRepository::getAll()
     */
    public function getAll()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Model\DomainDriven\Repository\Doctrine\InterfacePurchaseRequestRepository::get()
     */
    public function get($id)
    {}
}
