<?php
namespace Procure\Domain\APInvoice\Repository\Doctrine;

use Procure\Domain\APInvoice\Repository\APInvoiceRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class DoctrineAPInvoiceRepository implements APInvoiceRepositoryInterface

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
    {
        $criteria = array(
            "id" => $id
        );

        /**
         *
         * @var \Application\Entity\FinVendorInvoice $entity ;
         */
        $entity = $this->em->getRepository("\Application\Entity\FinVendorInvoice")->findOneBy($criteria);
        if ($entity == null) {
            return null;
        }

        return $entity->getSysNumber();
    }

    public function getByUUID($uuid)
    {}
}
