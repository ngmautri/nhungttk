<?php
namespace Inventory\Infrastructure\Doctrine;

use Inventory\Domain\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Item\Repository\ItemRepositoryInterface;
use Inventory\Domain\Item\Factory\InventoryItemFactory;
use Inventory\Application\DTO\ItemAssembler;

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
     * {@inheritdoc}
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

        $dto = ItemAssembler::createItemDTOFromDoctrine($entity);

        if ($dto->isStocked == 1) {
            $factory = new InventoryItemFactory();
           return $factory->createItemFromDB($dto);
       }

       return null;
   }

    public function getByUUID($uuid)
    {}

    public function store(AbstractItem $item)
    {}

    public function findAll()
    {}
}
