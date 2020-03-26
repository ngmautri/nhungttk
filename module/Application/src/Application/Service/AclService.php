<?php
namespace Application\Service;

use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Doctrine\ORM\EntityManager;
use Zend\Permissions\Acl\Acl as ZendAcl;
use Application\Entity\NmtApplicationAclResource;
use Application\Entity\NmtApplicationAclRole;
use Application\Entity\NmtApplicationAclRoleResource;
use Application\Utility\CategoryRegistry;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class AclService extends ZendAcl
{

    const DEFAULT_ROLE = 'member';

    protected $doctrineEM;

    protected $roles;

    protected $resources;

    protected $roleResources;

    protected $branch = array();

    protected $branch1 = array();

    protected $roleRegistry;

    protected $member;

    protected $catRegistry;

    protected $tree;

    /**
     *
     * @return \Application\Service\AclService
     */
    public function initAcl()
    {
        $roles = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRole')->findAll();
        $resources = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclResource')->findAll();
        $roleResources = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRoleResource')->findAll();

        // Add Role
        $this->addRole(new Role(self::DEFAULT_ROLE));

        $data = array();
        $index = array();

        foreach ($roles as $r) {

            if ($r instanceof NmtApplicationAclRole) {
                $row = new NmtApplicationAclRole();
                $row = $r;
                $id = $row->getId();
                $parent_id = $row->getParentId();
                $data[$id] = $row;
                $index[$parent_id][] = $id;
            }
        }

        // var_dump($index);

        $this->add_roles($index, $data, NULL, 0);

        // Add resource
        if (! empty($resources)) {
            foreach ($resources as $res1) {

                if ($res1 instanceof NmtApplicationAclResource) {
                    $res = new NmtApplicationAclResource();
                    $res = $res1;

                    $res_name = strtoupper($res->getResource());
                    if (! $this->hasResource($res_name)) {
                        $this->addResource(new Resource($res_name));
                    }
                }
            }
        }

        foreach ($roleResources as $r1) {
            if ($r1 instanceof NmtApplicationAclRoleResource) {
                $r = new NmtApplicationAclRoleResource();
                $r = $r1;
                $this->allow($r->getRole()
                    ->getRole(), strtoupper($r->getResource()
                    ->getResource()), null);
            }
        }

        // so that it can be serialized and save on session//
        unset($this->doctrineEM);

        return $this;
    }

    /**
     *
     * @param string $role
     * @param string $resource
     * @param string $permission
     * @return boolean
     */
    public function isAccessAllowed($role, $resource, $permission = null)
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
     * @param array $index
     * @param array $data
     * @param int $parent_id
     * @param int $level
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
     * @param array $index
     * @param array $data
     * @param int $parent_id
     * @param int $level
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
     * @param array $index
     * @param array $data
     * @param int $parent_id
     * @param int $level
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
     * @param array $role
     * @param string $parent
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

    public function getRoleRegistry()
    {
        return parent::getRoleRegistry();
    }

    public function getRoles()
    {
        return parent::getRoleRegistry()->getRoles();
    }

    /**
     *
     * @return \User\Utility\CategoryRegistry
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
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \Application\Service\AclService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}