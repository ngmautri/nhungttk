<?php

namespace User\Service;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

use User\Model\AclResourceTable;
use User\Model\AclRoleTable;
use Zend\Permissions\Acl\Role\Registry;


/**
 * 
 * @author nmt
 *
 */
class Acl extends ZendAcl
{

    const DEFAULT_ROLE = 'member';
    
    protected $aclResourceTable;
    protected $aclRoleTable;
    
    protected $roles;
    protected $resources;
    protected $roleResources;
    protected $branch = array();
    protected $roleRegistry;
    protected $member;
    
    /**
     * 
     */
    public function initAcl()
    {
      $roles = $this->aclRoleTable->getRoles(0,0);;
      $resources = $this->aclResourceTable->getResources(0,0);
      $roleResources = $this->aclResourceTable->getAclRoleResources();
      
      // Add Role
      $this->addRole(new Role(self::DEFAULT_ROLE));
  
      $data = array();
      $index = array();
      
       foreach ($roles as $row)
      {
      	$id = $row->id;
      	$parent_id = $row->parent_id;
      	$data[$id] = $row;
      	$index[$parent_id][] = $id;
      
      }
      
      //var_dump($index);
      
      $this->add_roles($index,$data,NULL, 0);
      
      /*
      if (! empty($roles)) {
      	foreach ($roles as $role) {
      		$roleName = $role->role;
      		if (! $this->hasRole($roleName)) {
      			$this->addRole(new Role($roleName), self::DEFAULT_ROLE);
      		}
      	}
      }
      */
      
      //Add resource
      if (! empty($resources)) {
      	foreach ($resources as $res) {
      		$res_name = strtoupper($res->resource);
      		if (! $this->hasResource($res_name)) {
      			$this->addResource(new Resource($res_name));
      		}
      	}
      }
      
      foreach ($roleResources as $r) {
      		$this->allow($r->role,strtoupper($r->resource),null);
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
    
    public function returnAclTree()
    {
    	$roles = $this->aclRoleTable->getRoles(0,0);;
    	$data = array();
    	$index = array();
    	
    	foreach ($roles as $row)
    	{
    		$id = $row->id;
    		$parent_id = $row->parent_id;
    		$data[$id] = $row;
    		$index[$parent_id][] = $id;
    		
    		if ($row->role == 'member'){
    			array_push($this->branch, array(
    					'level'=>1,
    					'role_id'=>$id,
    					'role'=>$row->role,
    			));
    		}
    	}
    	
    	//var_dump($index);
    	
    	
    	
    	$this->acl_tree($index,$data,NULL, 0);
    	
    	return $this->branch;
    }
  	
    //=====================================
  	public function getAclResourceTable() {
		return $this->aclResourceTable;
	}
	public function setAclResourceTable(AclResourceTable $aclResourceTable) {
		$this->aclResourceTable = $aclResourceTable;
		return $this;
	}
	public function getAclRoleTable() {
		return $this->aclRoleTable;
	}
	public function setAclRoleTable(AclRoleTable $aclRoleTable) {
		$this->aclRoleTable = $aclRoleTable;
		return $this;
	}
	
	protected function acl_tree($index,$data,$parent_id, $level)
	{
	
		if (isset($index[$parent_id])) {
			foreach ($index[$parent_id] as $id) {
	
				if(isset($data[$parent_id])):
					//echo $level . "." . str_repeat(" - ", $level) .$data[$id]->role . "==". $data[$id]->path . "\n";
					array_push($this->branch, array(
							'level'=>$level,
							'role_id'=>$id,
							'role'=>$data[$id]->role,
					));
				endif;
				
				$this->acl_tree($index,$data,$id, $level+1);
			}
		}
	}
	
	
	protected function add_roles($index,$data,$parent_id, $level)
	{
		
		if (isset($index[$parent_id])) {
			foreach ($index[$parent_id] as $id) {
				
				$this->add_roles($index,$data,$id, $level+1);
				
				if(isset($data[$parent_id])):
				
				$roleName = $data[$id]->role;
				$parentRoleName = $data[$parent_id]->role;
				
				if(!$this->hasRole($roleName)):
					$this->addRole(new Role($roleName),self::DEFAULT_ROLE);
				endif;
				
				if(!$this->hasRole($parentRoleName)):
					$this->addRole(new Role($parentRoleName),$roleName);
				else:
					$this->addParentRole($parentRoleName,$roleName);				
				endif;
				
				
				//var_dump($this->inheritsRole($parentRoleName, $roleName,true));
				
				
					////echo $level . ". " . str_repeat(" - ", $level) . $data[$parent_id]->role .'//'.$data[$id]->role . "==". $data[$id]->path . "\n";
				else:
					//echo $level . ". " . str_repeat(" - ", $level) .$data[$id]->role . "==". $data[$id]->path . "\n";
					
				endif;
			}
		}
	}
	
	protected function addParentRole($role,$parent){
		$roles= parent::getRoleRegistry()->getRoles();
		
		$parents= $roles[$role]['parents'];
		//var_dump($parents);
	
		//var_dump($children);
		
		array_push($parents,$parent);
		parent::getRoleRegistry()->remove($role);
		$this->addRole(new Role($role),$parents);
		//var_dump($roles[$parent]['instance']);
	}
	
	public function getRoleRegistry() {
		return parent::getRoleRegistry();
	}

}