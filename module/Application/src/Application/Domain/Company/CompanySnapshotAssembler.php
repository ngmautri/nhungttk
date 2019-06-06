<?php
namespace Application\Domain\Company;

use Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTO;
use Application\Application\DTO\Company\CompanyDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CompanySnapshotAssembler
{

    /**
     *
     * @param CompanyDTO $dto
     * @return NULL|\Application\Domain\Company\CompanySnapshot
     */
    public static function createSnapshotFromDTO(CompanyDTO $dto)
    {
        if (! $dto instanceof CompanyDTO)
            return null;

        $snapShot = new CompanySnapshot();

        $reflectionClass = new \ReflectionClass(get_class($dto));
        $properites = $reflectionClass->getProperties();

        foreach ($properites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName)) {
                $snapShot->$propertyName = $property->getValue($dto);
            }
        }
        return $snapShot;
    }

    public static function createFromSnapshotCode()
    {
        $snapshot = new CompanySnapshot();
        $reflectionClass = new \ReflectionClass($snapshot);
        $properites = $reflectionClass->getProperties();
        foreach ($properites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$this->" . $propertyName . " = \$snapshot->" . $propertyName . ";";
        }
    }

    /**
     * generete Mapping.
     */
    public static function createStoreMapping()
    {
        $entity = new \Application\Entity\NmtApplicationCompany();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$entity->set" . ucfirst($propertyName) . "(\$snapshot->" . $propertyName . ");";
        }
    }

    /**
     *
     * @param GenericCompany $obj
     * @return NULL|\Application\Domain\Company\CompanySnapshot
     */
    public static function createSnapshotFrom($obj)
    {
        if (! $obj instanceof GenericCompany)
            return null;

        $snapShot = new CompanySnapshot();

        // should uss reflection object
        $reflectionClass = new \ReflectionObject($obj);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName)) {

                if ($property->getValue($obj) == null || $property->getValue($obj) == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $property->getValue($obj);
                }
            }
        }

        return $snapShot;
    }
}
