<?php
namespace Application\Domain\Company\PostingPeriod;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Association\AssociationSnapshot;
use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseAssociationSnapshot extends AssociationSnapshot
{

    public function init(CommandOptions $options)
    {
        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();

        $this->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setCreatedBy($createdBy);

        $this->setUuid(Uuid::uuid4()->toString());
    }

    public function update(CommandOptions $options)
    {
        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();

        $this->setLastChangeOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setLastChangeBy($createdBy);
    }
}