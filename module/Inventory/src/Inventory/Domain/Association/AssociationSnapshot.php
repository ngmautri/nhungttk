<?php
namespace Inventory\Domain\Association;

use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationSnapshot extends BaseAssociationSnapshot
{

    public function initDoc($createdBy, $createdDate)
    {
        $this->setCreatedOn($createdDate);
        $this->setCreatedBy($createdBy);

        $this->setIsActive(1);
        $this->setRevisionNo(0);
        $this->setUuid(Uuid::uuid4()->toString());
    }

    public function updateDoc($createdBy, $createdDate)
    {
        $this->setLastChangeOn($createdDate);
        $this->setLastChangeBy($createdBy);
    }
}