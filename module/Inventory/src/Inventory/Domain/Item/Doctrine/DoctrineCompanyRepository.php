<?php
namespace Application\Domain\Company\Doctrine;

use Application\Domain\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Item\ItemRepositoryInterface;

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
    public function getById($id)
    {}

    public function getByUUID($uuid)
    {}

    public function store(AbstractItem $item)
    {}

    public function findAll()
    {}


   
}
