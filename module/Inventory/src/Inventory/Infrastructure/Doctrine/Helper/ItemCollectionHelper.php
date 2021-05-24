<?php
namespace Inventory\Infrastructure\Doctrine\Helper;

use Application\Entity\NmtInventoryItemVariant;
use Application\Entity\NmtInventoryItemVariantAttribute;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Item\Collection\ItemVariantAttributteCollection;
use Inventory\Domain\Item\Collection\ItemVariantCollection;
use Inventory\Domain\Item\Variant\Factory\ItemVariantFactory;
use Inventory\Infrastructure\Mapper\ItemVariantMapper;
use Closure;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemCollectionHelper
{

    /**
     *
     * @param EntityManager $doctrineEM
     * @param int $id
     * @return Closure
     */
    static public function createVariantCollectionRef(EntityManager $doctrineEM, $id)
    {
        return function () use ($doctrineEM, $id) {

            $criteria = [
                'item' => $id
            ];
            $results = $doctrineEM->getRepository('\Application\Entity\NmtInventoryItemVariant')->findBy($criteria);

            $collection = new ItemVariantCollection();

            if (count($results) == 0) {
                return $collection;
            }

            foreach ($results as $r) {

                /**@var NmtInventoryItemVariant $localEnityDoctrine ;*/
                $localEnityDoctrine = $r;
                $localSnapshot = ItemVariantMapper::createVariantSnapshot($localEnityDoctrine);
                $variant = ItemVariantFactory::contructFromDB($localSnapshot);
                $variant->setAttributeCollectionRef(self::createVariantAttributeCollectionRef($doctrineEM, $localEnityDoctrine->getId()));
                $collection->add($variant);
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
    static public function createVariantAttributeCollectionRef(EntityManager $doctrineEM, $id)
    {
        return function () use ($doctrineEM, $id) {

            $criteria = [
                'variant' => $id
            ];
            $results = $doctrineEM->getRepository('\Application\Entity\NmtInventoryItemVariantAttribute')->findBy($criteria);

            $collection = new ItemVariantAttributteCollection();

            if (count($results) == 0) {
                return $collection;
            }

            foreach ($results as $r) {

                /**@var NmtInventoryItemVariantAttribute $localEnityDoctrine ;*/
                $localEnityDoctrine = $r;
                $localSnapshot = ItemVariantMapper::createVariantAttributeSnapshot($localEnityDoctrine);
                $collection->add($localSnapshot);
            }
            return $collection;
        };
    }
}
