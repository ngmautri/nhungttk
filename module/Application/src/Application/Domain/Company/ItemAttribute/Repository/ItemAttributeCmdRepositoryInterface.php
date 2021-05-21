<?php
namespace Application\Domain\Company\ItemAttribute\Repository;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\ItemAttribute\BaseAttribute;
use Application\Domain\Company\ItemAttribute\BaseAttributeGroup;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface ItemAttributeCmdRepositoryInterface
{

    public function storeAttributeGroup(BaseCompany $rootEntity, BaseAttributeGroup $localEntity, $isPosting = false);

    public function storeWholeAttributeGroup(BaseCompany $rootEntity, BaseAttributeGroup $localEntity, $isPosting = false);

    public function removeAttributeGroup(BaseCompany $rootEntity, BaseAttributeGroup $localEntity, $isPosting = false);

    public function storeAttribute(BaseAttributeGroup $rootEntity, BaseAttribute $localEntity, $isPosting = false);

    public function removeAttribute(BaseAttributeGroup $rootEntity, BaseAttribute $localEntity, $isPosting = false);
}
