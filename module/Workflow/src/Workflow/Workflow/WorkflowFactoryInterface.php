<?php

namespace Workflow\Workflow;

/**
 * 
 * @author nmt
 *
 */
 interface WorkflowFactoryInterface {
   
     /**
      * 
      */
     public function getSubject();
     
     
     /**
      * 
      * @param unknown $subject
      */
     public function setSubject($subject);
     
     /**
      * 
      */
     public function getWorkFlowList();
     
   
}
