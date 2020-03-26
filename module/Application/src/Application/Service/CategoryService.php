<?php
namespace Application\Service;

use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use User\Model\AclResourceTable;
use User\Model\AclRoleTable;
use Zend\Permissions\Acl\Role\Registry;
use Application\Utility\CategoryRegistry;
use Doctrine\ORM\EntityManager;
use Zend\Permissions\Acl\Acl as ZendAcl;
use Application\Entity\NmtApplicationAclResource;
use Application\Entity\NmtApplicationAclRole;
use Application\Entity\NmtApplicationAclRoleResource;

/**
 *
 * @author nmt
 *        
 */
class CategoryService
{

    protected $doctrineEM;

    protected $branch = array();

    protected $branch1 = array();

    protected $roleRegistry;

    protected $catRegistry;

    protected $tree;

    /**
     *
     * @param array $data
     * @return \Application\Service\CategoryService
     */
    public function initAcl($input = array())
    {
        $data = array();
        $index = array();

        if (count($input) > 0) {
            foreach ($input as $i) {
                $id = $i->getId();
                $parent_id = $i->getParentId();
                $data[$id] = $i;
                $index[$parent_id][] = $id;
            }

            // var_dump($index);
            $this->add_roles($index, $data, NULL, 0);
        }
        return $this;
    }

    /**
     *
     * @param unknown $role
     * @param unknown $resource
     * @param unknown $permission
     * @return boolean
     */
    public function isAccessAllowed($role, $resource, $permission)
    {
        if (! $this->hasResource($resource)) {
            return false;
        }
        if ($this->isAllowed($role, $resource, $permission)) {
            return true;
        }
        return false;
    }

    /**
     *
     * @deprecated
     *
     * @return array
     */
    public function returnAclTreeOld()
    {
        $roles = $this->aclRoleTable->getRoles(0, 0);
        $data = array();
        $index = array();

        foreach ($roles as $row) {
            $id = $row->id;
            $parent_id = $row->parent_id;
            $data[$id] = $row;
            $index[$parent_id][] = $id;

            if ($row->role == 'member') {
                array_push($this->branch, array(
                    'level' => 1,
                    'role_id' => $id,
                    'role' => $row->role
                ));
            }
        }

        // var_dump($index);

        $this->acl_tree($index, $data, NULL, 0);

        return $this->branch;
    }

    /**
     *
     * @return array
     */
    public function returnAclTree()
    {
        $roles = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRole')->findAll();
        $data = array();
        $index = array();

        foreach ($roles as $row) {
            $id = $row->getId();
            $parent_id = $row->getParentId();
            $data[$id] = $row;
            $index[$parent_id][] = $id;

            if ($row->getRole() == 'member') {
                array_push($this->branch, array(
                    'level' => 1,
                    'role_id' => $id,
                    'role' => $row->getRole()
                ));
            }
        }

        // var_dump($index);

        $this->acl_tree($index, $data, NULL, 0);

        return $this->branch;
    }

    /**
     *
     * @return \User\Utility\CategoryRegistry
     */
    public function returnAclTree1()
    {
        $roles = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRole')->findAll();
        $data = array();
        $index = array();

        foreach ($roles as $row) {
            $id = $row->getId();
            $parent_id = $row->getParentId();
            $data[$id] = $row;
            $index[$parent_id][] = $id;

            if ($row->getRole() == 'member') {
                array_push($this->branch, array(
                    'level' => 1,
                    'role_id' => $id,
                    'role' => $row->getRole()
                ));
            }
        }
        $this->acl_tree1($index, $data, NULL, 0);

        return $this->getCatetoryRegistry();
    }

    /**
     *
     * @deprecated
     *
     * @return \User\Utility\CategoryRegistry
     */
    public function returnAclTree1Ond()
    {
        $roles = $this->aclRoleTable->getRoles(0, 0);
        ;
        $data = array();
        $index = array();

        foreach ($roles as $row) {
            $id = $row->id;
            $parent_id = $row->parent_id;
            $data[$id] = $row;
            $index[$parent_id][] = $id;

            if ($row->role == 'member') {
                array_push($this->branch, array(
                    'level' => 1,
                    'role_id' => $id,
                    'role' => $row->role
                ));
            }
        }
        $this->acl_tree1($index, $data, NULL, 0);

        return $this->getCatetoryRegistry();
    }

    /**
     *
     * @param unknown $index
     * @param unknown $data
     * @param unknown $parent_id
     * @param unknown $level
     */
    protected function acl_tree($index, $data, $parent_id, $level)
    {
        if (isset($index[$parent_id])) {
            foreach ($index[$parent_id] as $id) {

                if (isset($data[$parent_id])) :
                    // echo $level . "." . str_repeat(" - ", $level) .$data[$id]->role . "==". $data[$id]->path . "\n";
                    array_push($this->branch, array(
                        'level' => $level,
                        'role_id' => $id,
                        'role' => $data[$id]->getRole(),
                        'path' => $data[$id]->getPath()
                    ));
				endif;

                $this->acl_tree($index, $data, $id, $level + 1);
            }
        }
    }

    /**
     *
     * @param unknown $index
     * @param unknown $data
     * @param unknown $parent_id
     * @param unknown $level
     */
    protected function acl_tree1($index, $data, $parent_id, $level)
    {
        if (isset($index[$parent_id])) {
            foreach ($index[$parent_id] as $id) {

                // pre-order travesal
                $this->acl_tree1($index, $data, $id, $level + 1);

                if (isset($data[$parent_id])) :
                    // echo $level . "." . str_repeat(" - ", $level) .$data[$id]->role . "==". $data[$id]->path . "\n";

                    $roleName = ucwords($data[$id]->getRole());
                    $parentRoleName = ucwords($data[$parent_id]->getRole());

                    try {
                        if (! $this->getCatetoryRegistry()->has($roleName)) {
                            $this->getCatetoryRegistry()->add($roleName, $parentRoleName);
                        } else {
                            $this->getCatetoryRegistry()->updateParent($roleName, $parentRoleName);
                        }
                    } catch (\Exception $e) {
                        // var_dump($e);
                    }
					//echo $level . ". " . str_repeat(" - ", $level) . $parentRoleName .'//'.$roleName . "==". $data[$id]->path . "\n";
				endif;

            }
        }
    }

    /**
     *
     * @author NMT
     * @param unknown $index
     * @param unknown $data
     * @param unknown $parent_id
     * @param unknown $level
     */
    protected function add_roles($index, $data, $parent_id, $level)
    {
        if (isset($index[$parent_id])) {
            foreach ($index[$parent_id] as $id) {

                // postOrder
                $this->add_roles($index, $data, $id, $level + 1);

                if (isset($data[$parent_id])) :

                    $roleName = $data[$id]->getRole();
                    $parentRoleName = $data[$parent_id]->getRole();

                    if (! $this->hasRole($roleName)) :
                        $this->addRole(new Role($roleName), self::DEFAULT_ROLE);
				endif;

                    if (! $this->hasRole($parentRoleName)) :
                        $this->addRole(new Role($parentRoleName), $roleName);
                    else :
                        $this->addParentRole($parentRoleName, $roleName);
                    endif;

                    // var_dump($this->inheritsRole($parentRoleName, $roleName,true));

                    // //echo $level . ". " . str_repeat(" - ", $level) . $data[$parent_id]->role .'//'.$data[$id]->role . "==". $data[$id]->path . "\n";
                else : // echo $level . ". " . str_repeat(" - ", $level) .$data[$id]->role . "==". $data[$id]->path . "\n";

                endif;
            }
        }
    }

    /**
     *
     * @param unknown $role
     * @param unknown $parent
     */
    protected function addParentRole($role, $parent)
    {
        $roles = parent::getRoleRegistry()->getRoles();

        $parents = $roles[$role]['parents'];
        // var_dump($parents);

        // var_dump($children);

        array_push($parents, $parent);
        parent::getRoleRegistry()->remove($role);
        $this->addRole(new Role($role), $parents);
        // var_dump($roles[$parent]['instance']);
    }

    /**
     *
     * @return unknown
     */
    public function getRoleRegistry()
    {
        return parent::getRoleRegistry();
    }

    /**
     *
     * @return unknown
     */
    public function getRoles()
    {
        return parent::getRoleRegistry()->getRoles();
    }

    /**
     *
     * @return \Application\Utility\CategoryRegistry
     */
    protected function getCatetoryRegistry()
    {
        if (null === $this->catRegistry) {
            $this->catRegistry = new CategoryRegistry();
        }
        return $this->catRegistry;
    }

    // =====================================

    /**
     *
     * @return unknown
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    public function getAclResourceTable()
    {
        return $this->aclResourceTable;
    }

    public function setAclResourceTable(AclResourceTable $aclResourceTable)
    {
        $this->aclResourceTable = $aclResourceTable;
        return $this;
    }

    public function getAclRoleTable()
    {
        return $this->aclRoleTable;
    }

    public function setAclRoleTable(AclRoleTable $aclRoleTable)
    {
        $this->aclRoleTable = $aclRoleTable;
        return $this;
    }
}