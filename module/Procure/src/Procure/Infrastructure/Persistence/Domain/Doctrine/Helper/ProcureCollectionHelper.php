<?php
namespace Procure\Infrastructure\Persistence\Domain\Doctrine\Helper;

use Application\Domain\Company\Brand\Factory\BrandFactory;
use Application\Domain\Company\Collection\BrandCollection;
use Application\Domain\Company\Collection\ItemAttributeCollection;
use Application\Domain\Company\Collection\ItemAttributeGroupCollection;
use Application\Domain\Company\ItemAttribute\GenericAttribute;
use Application\Domain\Company\ItemAttribute\Factory\ItemAttributeFactory;
use Application\Entity\NmtApplicationBrand;
use Application\Entity\NmtInventoryAttribute;
use Application\Entity\NmtInventoryAttributeGroup;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\BrandMapper;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\ItemAttributeMapper;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ProcureCollectionHelper
{
    /**
     *
     * @param EntityManager $doctrineEM
     * @param int $id
     * @return \Closure
     */
    public static function createPrRowCollectionRef(EntityManager $doctrineEM, $id)
    {
        return function () use ($doctrineEM, $id) {

            $criteria = [
                'company' => $id
            ];

            $sort = [
                'brandName' => 'ASC'
            ];
            $results = $doctrineEM->getRepository('\Application\Entity\NmtApplicationBrand')->findBy($criteria, $sort);

            $collection = new BrandCollection();

            if (count($results) == 0) {
                return $collection;
            }

            foreach ($results as $r) {

                /**@var NmtApplicationBrand $r ;*/

                $localSnapshot = BrandMapper::createBrandSnapshot($doctrineEM, $r);
                $e = BrandFactory::contructFromDB($localSnapshot);
                $collection->add($e);
            }
            return $collection;
        };
    }
}
