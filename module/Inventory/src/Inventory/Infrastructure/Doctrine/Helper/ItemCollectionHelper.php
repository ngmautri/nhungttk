<?php
namespace Inventory\Infrastructure\Doctrine\Helper;

use Application\Entity\NmtInventoryItemPicture;
use Application\Entity\NmtInventoryItemSerial;
use Application\Entity\NmtInventoryItemVariant;
use Application\Entity\NmtInventoryItemVariantAttribute;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Item\Collection\ItemPictureCollection;
use Inventory\Domain\Item\Collection\ItemSerialCollection;
use Inventory\Domain\Item\Collection\ItemVariantAttributteCollection;
use Inventory\Domain\Item\Collection\ItemVariantCollection;
use Inventory\Domain\Item\Picture\Factory\ItemPictureFactory;
use Inventory\Domain\Item\Variant\Factory\ItemVariantFactory;
use Inventory\Infrastructure\Mapper\ItemVariantMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemCollectionHelper
{

    /*
     * |=============================
     * |PICUTRE
     * |
     * |=============================
     */
    static public function createPictureCollectionRef(EntityManager $doctrineEM, $id)
    {
        return function () use ($doctrineEM, $id) {

            $criteria = [
                'item' => $id
            ];
            $results = $doctrineEM->getRepository('\Application\Entity\NmtInventoryItemPicture')->findBy($criteria);

            $collection = new ItemPictureCollection();

            if (count($results) == 0) {
                return $collection;
            }

            /**@var NmtInventoryItemPicture $r ;*/

            foreach ($results as $r) {

                $localEnity = ItemPictureFactory::contructFromDB($r);
                $collection->add($localEnity);
            }
            return $collection;
        };
    }

    /*
     * |=============================
     * |SERIAL
     * |
     * |=============================
     */
    static public function createSerialCollectionRef(EntityManager $doctrineEM, $id)
    {
        return function () use ($doctrineEM, $id) {

            $criteria = [
                'item' => $id
            ];
            $results = $doctrineEM->getRepository('\Application\Entity\NmtInventoryItemPicture')->findBy($criteria);

            $collection = new ItemSerialCollection();

            if (count($results) == 0) {
                return $collection;
            }

            /**@var NmtInventoryItemSerial $localEnityDoctrine ;*/

            foreach ($results as $r) {

                $localSnapshot = ItemVariantMapper::createVariantSnapshot($localEnityDoctrine);
                $localEnity = ItemPictureFactory::contructFromDB($localSnapshot);
                $collection->add($localEnity);
            }
            return $collection;
        };
    }

    /*
     * |=============================
     * |VARIANT
     * |
     * |=============================
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

    /*
     * |=============================
     * |VARIANT-ATTRIBUTE
     * |
     * |=============================
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
