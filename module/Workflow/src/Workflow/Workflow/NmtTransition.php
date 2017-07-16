<?php

namespace Workflow\Workflow;
use Symfony\Component\Workflow\Transition;


/**
 * 
 * @author nmt
 *
 */
class NmtTransition extends Transition {
    private $roleId;
    private $agentId;
    
    
    /**
     * @return the $roleId
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @return the $agentId
     */
    public function getAgentId()
    {
        return $this->agentId;
    }

    /**
     * @param field_type $roleId
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
    }

    /**
     * @param field_type $agentId
     */
    public function setAgentId($agentId)
    {
        $this->agentId = $agentId;
    }

    
    
    
}
