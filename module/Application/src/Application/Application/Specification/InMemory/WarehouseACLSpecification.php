<?php
namespace Application\Application\Specification\InMemory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class WarehouseACLSpecification extends AbstractInMemorySpecification
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        $warehouseId = null;
        if (isset($subject["warehouseId"])) {
            $warehouseId = $subject["warehouseId"];
        }

        $companyId = null;
        if (isset($subject["companyId"])) {
            $companyId = $subject["companyId"];
        }

        $userId = null;
        if (isset($subject["userId"])) {
            $userId = $subject["userId"];
        }

        return false;
    }
}