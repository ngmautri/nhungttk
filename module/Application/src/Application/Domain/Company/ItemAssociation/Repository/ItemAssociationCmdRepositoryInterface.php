<?php
namespace Application\Domain\Company\ItemAssociation\Repository;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\ItemAssociation\BaseAssociation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface ItemAssociationCmdRepositoryInterface
{

    public function storeItemAssociation(BaseCompany $rootEntity, BaseAssociation $localEntity, $isPosting = false);

    public function removeItemAssociation(BaseCompany $rootEntity, BaseAssociation $localEntity, $isPosting = false);
}
