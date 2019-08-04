<?php
namespace Inventory\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Warehouse\AbstractWarehouse;
use Inventory\Domain\Warehouse\WarehouseCmdRepositoryInterface;
use Inventory\Domain\Warehouse\Location\DefaultLocation;
use Ramsey;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineWarehouseCmdRepository extends AbstractDoctrineRepository implements WarehouseCmdRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\WarehouseCmdRepositoryInterface::store()
     */
    public function store(AbstractWarehouse $wh)
    {
        if ($wh == null)
            throw new InvalidArgumentException("Warehouse is empty");

        /**
         *
         * @var WarehouseSnapshot $snapshot ;
         */
        $snapshot = $wh->makeSnapshot();

        if ($snapshot == null)
            throw new InvalidArgumentException("Warehouse snapshot can not be created");

        /**
         *
         * @var \Application\Entity\NmtInventoryWarehouse $entity ;
         */
        if ($wh->getId() > 0) {

            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $entity ;
             */
            $entity = $this->doctrineEM->find("\Application\Entity\NmtInventoryWarehouse", $wh->getId());

            if ($entity == null)
                throw new InvalidArgumentException("Warehouse can't be retrieved.");

            $entity->setLastChangeOn(new \Datetime());
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }
        } else {

            $entity = new \Application\Entity\NmtInventoryWarehouse();
            $entity->setToken(Ramsey\Uuid\Uuid::uuid4()->toString());
            $entity->setUuid(Ramsey\Uuid\Uuid::uuid4()->toString());

            if ($snapshot->createdBy > 0) {

                /**
                 *
                 * @var \Application\Entity\MlaUsers $u ;
                 */
                $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $snapshot->createdBy);
                if ($u !== null)
                    $entity->setCreatedBy($u);
            }

            $createdOn = new \DateTime();
            $entity->setCreatedOn($createdOn);

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            // create default location:

            $rootLocation = new \Application\Entity\NmtInventoryWarehouseLocation();
            $rootLocation->setWarehouse($entity);
            $rootLocation->setLocationCode($entity->getId() . '-' . DefaultLocation::ROOT_LOCATION);
            $rootLocation->setCreatedBy($u);
            $rootLocation->setCreatedOn($createdOn);
            $rootLocation->setIsActive(1);
            $rootLocation->setIsRootLocation(1);
            $rootLocation->setIsSystemLocation(1);
            $rootLocation->setLocationName($entity->getId() . '-' . DefaultLocation::ROOT_LOCATION);
            $rootLocation->setToken(Ramsey\Uuid\Uuid::uuid4()->toString());

            $this->doctrineEM->persist($rootLocation);
            $this->doctrineEM->flush();

            $rootLocation->setPath($rootLocation->getId() . '/');
            $rootLocation->setPathDepth(1);
            $this->doctrineEM->persist($rootLocation);
            $this->doctrineEM->flush();

            $entity->setLocation($rootLocation);

            $returnLocation = new \Application\Entity\NmtInventoryWarehouseLocation();
            $returnLocation->setWarehouse($entity);
            $returnLocation->setLocationCode($entity->getId() . '-' . DefaultLocation::RETURN_LOCATION);
            $returnLocation->setCreatedBy($u);
            $returnLocation->setCreatedOn($createdOn);
            $returnLocation->setIsActive(1);
            $returnLocation->setIsRootLocation(0);
            $returnLocation->setIsSystemLocation(0);
            $returnLocation->setIsReturnLocation(1);
            $returnLocation->setLocationName($entity->getId() . '-' . DefaultLocation::RETURN_LOCATION);
            $returnLocation->setParentId($rootLocation->getId()); // important

            $returnLocation->setToken(Ramsey\Uuid\Uuid::uuid4()->toString());
            $this->doctrineEM->persist($returnLocation);
            $this->doctrineEM->flush();

            $returnLocation->setPath($rootLocation->getPath() . $returnLocation->getId() . '/');
            $returnLocation->setPathDepth($rootLocation->getPathDepth() + 1);
            $this->doctrineEM->persist($returnLocation);

            $scrapLocation = new \Application\Entity\NmtInventoryWarehouseLocation();
            $scrapLocation->setWarehouse($entity);
            $scrapLocation->setLocationCode($entity->getId() . '-' . DefaultLocation::SCRAP_LOCATION);
            $scrapLocation->setLocationName($entity->getId() . '-' . DefaultLocation::SCRAP_LOCATION);
            $scrapLocation->setCreatedBy($u);
            $scrapLocation->setCreatedOn($createdOn);
            $scrapLocation->setIsActive(1);
            $scrapLocation->setIsRootLocation(0);
            $scrapLocation->setIsSystemLocation(0);
            $scrapLocation->setIsReturnLocation(0);
            $scrapLocation->setIsScrapLocation(1);
            $scrapLocation->setParentId($rootLocation->getId()); // important

            $scrapLocation->setToken(Ramsey\Uuid\Uuid::uuid4()->toString());

            $this->doctrineEM->persist($scrapLocation);
            $this->doctrineEM->flush();

            $scrapLocation->setPath($rootLocation->getPath() . $scrapLocation->getId() . '/');
            $scrapLocation->setPathDepth($rootLocation->getPathDepth() + 1);
            $this->doctrineEM->persist($scrapLocation);
            $this->doctrineEM->flush();
        }

        $entity->setWhCode($snapshot->whCode);
        $entity->setWhName($snapshot->whName);
        $entity->setWhAddress($snapshot->whAddress);
        $entity->setWhContactPerson($snapshot->whContactPerson);
        $entity->setWhTelephone($snapshot->whTelephone);
        $entity->setWhEmail($snapshot->whEmail);
        $entity->setIsLocked($snapshot->isLocked);
        $entity->setWhStatus($snapshot->whStatus);
        $entity->setRemarks($snapshot->remarks);
        // $entity->setIsDefault($snapshot->isDefault);

        if ($snapshot->company > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCompany $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCompany')->find($snapshot->company);
            $entity->setCompany($obj);
        }

        if ($snapshot->whCountry > 0) {
            /**
             *
             * @var \Application\Entity\NmtApplicationCountry $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->find($snapshot->whCountry);
            $entity->setWhCountry($obj);
        }

        if ($snapshot->stockkeeper > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->stockkeeper);
            $entity->setStockkeeper($obj);
        }

        if ($snapshot->whController > 0) {
            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->find($snapshot->whController);
            $entity->setWhController($obj);
        }

        $this->doctrineEM->persist($entity);
        $this->doctrineEM->flush();
        
        return $entity->getId();
    }
}
