<?php
namespace Application\Infrastructure\AggregateRepository;

use Application\Domain\Company\Company;
use Application\Domain\Company\CompanyRepositoryInterface;
use Application\Domain\Shared\Department;
use Application\Domain\Company\CompanySnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineCompanyRepository extends AbstractDoctrineRepository implements CompanyRepositoryInterface
{

    public function addPostingPeriod(Company $company)
    {}

    public function getPostingPeriod($periodId)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\CompanyRepositoryInterface::getById()
     */
    public function getById($id)
    {
        $criteria = array(
            "id" => $id
        );

        /**
         *
         * @var \Application\Entity\NmtApplicationCompany $entity ;
         */
        $entity = $this->doctrineEM->getRepository("\Application\Entity\NmtApplicationCompany")->findOneBy($criteria);
        if ($entity == null)
            return null;

        $snapshot = $this->createSnapshot($entity);
        return $snapshot;
    }

    public function addWarehouse(Company $company, $warehouse)
    {}

    public function getByUUID($uuid)
    {}

    public function store(Company $company)
    {}

    public function findAll()
    {}

    public function addDeparment(Company $company, Department $department)
    {}

    /**
     *
     * @param \Application\Entity\NmtApplicationCompany $entity
     * @return NULL|\Application\Domain\Company\CompanySnapshot
     */
    private function createSnapshot(\Application\Entity\NmtApplicationCompany $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new CompanySnapshot();

        // mapping referrence
        if ($entity->getCreatedBy() !== null) {
            $snapshot->createdBy = $entity->getCreatedBy()->getId();
        }

        if ($entity->getLastChangeBy() !== null) {
            $snapshot->lastChangeBy = $entity->getLastChangeBy()->getId();
        }

        if ($entity->getDefaultCurrency() !== null) {
            $snapshot->defaultCurrency = $entity->getDefaultCurrency()->getId();
        }

        if ($entity->getDefaultWarehouse() !== null) {
            $snapshot->defaultWarehouse = $entity->getDefaultWarehouse()->getId();
        }

        if ($entity->getCountry() !== null) {
            $snapshot->country = $entity->getCountry()->getId();
        }

        $reflectionClass = new \ReflectionClass($entity);
        $entityProperites = $reflectionClass->getProperties();

        foreach ($entityProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! is_object($property->getValue($entity))) {

                if (property_exists($snapshot, $propertyName)) {
                    $snapshot->$propertyName = $property->getValue($entity);
                }
            }
        }
        return $snapshot;
    }
}
