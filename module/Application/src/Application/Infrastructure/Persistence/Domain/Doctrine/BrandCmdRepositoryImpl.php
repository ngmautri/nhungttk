<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\Brand\BaseBrand;
use Application\Domain\Company\Brand\BrandSnapshot;
use Application\Domain\Company\Brand\Repository\BrandCmdRepositoryInterface;
use Application\Entity\NmtApplicationBrand;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\BrandMapper;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BrandCmdRepositoryImpl extends AbstractDoctrineRepository implements BrandCmdRepositoryInterface
{

    const COMPANY_ENTITY_NAME = "\Application\Entity\NmtApplicationCompany";

    const ROOT_ENTITY_NAME = "\Application\Entity\NmtApplicationBrand";

    public function removeBrand(BaseCompany $rootEntity, BaseBrand $localEntity, $isPosting = false)
    {
        $rootEntityDoctrine = $this->assertAndReturnBrand($localEntity);

        $isFlush = true;

        // remove row.
        $this->getDoctrineEM()->remove($rootEntityDoctrine);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return true;
    }

    public function storeBrand(BaseCompany $rootEntity, BaseBrand $localEntity, $isPosting = false)
    {
        $rootSnapshot = $this->_getRootSnapshot($localEntity);

        $isFlush = true;
        $increaseVersion = true;
        $entity = $this->_store($rootSnapshot, $isPosting, $isFlush, $increaseVersion);

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->revisionNo = $entity->getRevisionNo();
        $rootSnapshot->version = $entity->getVersion();

        return $rootSnapshot;
    }

    private function assertAndReturnBrand(BaseBrand $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("BaseBrand not given.");
        }

        /**
         *
         * @var NmtApplicationBrand $rootEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());
        if (! $rootEntityDoctrine instanceof NmtApplicationBrand) {
            throw new InvalidArgumentException("brand entity not found!");
        }

        return $rootEntityDoctrine;
    }

    private function _getRootSnapshot(BaseBrand $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        return $rootEntity->makeSnapshot();
    }

    private function _store(BrandSnapshot $rootSnapshot, $isPosting, $isFlush, $increaseVersion)
    {

        /**
         *
         * @var \Application\Entity\NmtApplicationBrand $entity ;
         *
         */
        if ($rootSnapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootSnapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootSnapshot->getId()));
            }
        } else {
            $rootClassName = self::ROOT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = BrandMapper::mapBrandEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

        $this->doctrineEM->persist($entity);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        if ($entity == null) {
            throw new InvalidArgumentException("Something wrong. Doctrine root entity not created");
        }

        return $entity;
    }
}
