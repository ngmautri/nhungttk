<?php
namespace Inventory\Infrastructure\Doctrine;

use Inventory\Domain\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Item\Repository\ItemRepositoryInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineItemRepository implements ItemRepositoryInterface
{

    /**
     *
     * @var EntityManager
     */
    private $em;

    /**
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        if ($em == null) {
            throw new InvalidArgumentException("Doctrine Entity manager not found!");
        }
        $this->em = $em;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Item\Repository\ItemRepositoryInterface::getById()
     */
    public function getById($id)
    {
        $criteria = array(
            "id" => $id
        );
        
        /**
         *
         * @var \Application\Entity\NmtInventoryItem $entity ;
         */
        $entity = $this->em->getRepository("\Application\Entity\NmtInventoryItem")->findOneBy($criteria);
        if ($entity == null) {
            return null;
        }
     
        
       
        return $entity->getItemName();
        
    }

    public function getByUUID($uuid)
    {}

    public function store(AbstractItem $item)
    {}

    public function findAll()
    {}
}
