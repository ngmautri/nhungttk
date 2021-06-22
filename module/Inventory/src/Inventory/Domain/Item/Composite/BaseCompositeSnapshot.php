<?php
namespace Inventory\Domain\Item\Composite

use Application\Domain\Shared\Constants;
use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseCompositeSnapshot extends CompositeSnapshot
{

    public function init($createdBy, $createdDate)
    {
        $this->setCreatedOn($createdDate);
        $this->setCreatedBy($createdBy);
        $this->setSysNumber(Constants::SYS_NUMBER_UNASSIGNED);
        $this->setRevisionNo(0);
        $this->setUuid(Uuid::uuid4()->toString());
        $this->setToken($this->getUuid());
    }

    public function updateSnapshot($createdBy, $createdDate)
    {
        $this->setLastChangeOn($createdDate);
        $this->setLastChangeBy($createdBy);
    }
}