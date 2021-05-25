<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine\Helper;

use Application\Domain\Company\Collection\ItemAttributeCollection;
use Application\Domain\Company\Collection\ItemAttributeGroupCollection;
use Application\Domain\Company\ItemAttribute\GenericAttribute;
use Application\Domain\Company\ItemAttribute\Factory\ItemAttributeFactory;
use Application\Entity\NmtInventoryAttribute;
use Application\Entity\NmtInventoryAttributeGroup;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\ItemAttributeMapper;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyCollectionHelper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param int $id
     * @return \Closure
     */
    public static function createItemAttributeGroupCollectionRef(EntityManager $doctrineEM, $id)
    {
        return function () use ($doctrineEM, $id) {

            $criteria = [
                'company' => $id
            ];
            $results = $doctrineEM->getRepository('\Application\Entity\NmtInventoryAttributeGroup')->findBy($criteria);

            $collection = new ItemAttributeGroupCollection();

            if (count($results) == 0) {
                return $collection;
            }

            foreach ($results as $r) {

                /**@var NmtInventoryAttributeGroup $localEnityDoctrine ;*/
                $localEnityDoctrine = $r;
                $localSnapshot = ItemAttributeMapper::createAttributeGroupSnapshot($doctrineEM, $localEnityDoctrine);
                $e = ItemAttributeFactory::contructFromDB($localSnapshot);
                $e->setAttributeCollectionRef(self::createItemAttributeCollectionRef($doctrineEM, $e->getId()));
                $collection->add($e);
            }
            return $collection;
        };
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @param int $id
     * @return \Closure
     */
    public static function createItemAttributeCollectionRef(EntityManager $doctrineEM, $id)
    {
        return function () use ($doctrineEM, $id) {

            $criteria = [
                'group' => $id
            ];
            $results = $doctrineEM->getRepository('\Application\Entity\NmtInventoryAttribute')->findBy($criteria);

            $collection = new ItemAttributeCollection();

            if (count($results) == 0) {
                return $collection;
            }

            foreach ($results as $r) {

                /**@var NmtInventoryAttribute $localEnityDoctrine ;*/
                $localEnityDoctrine = $r;
                $localSnapshot = ItemAttributeMapper::createAttributeSnapshot($doctrineEM, $localEnityDoctrine);
                $e = GenericAttribute::constructFromDB($localSnapshot);
                $collection->add($e);
            }
            return $collection;
        };
    }
}
