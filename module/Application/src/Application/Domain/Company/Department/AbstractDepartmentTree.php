<?php
namespace Application\Domain\Company\Department;

use Application\Domain\Company\Contracts\DefaultDepartment;
use Application\Domain\Util\Tree\AbstractTree;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractDepartmentTree extends AbstractTree
{

    public function createRoot()
    {
        return parent::createTree(DefaultDepartment::ROOT, 1);
    }
}
