<?php

/**
 * 
 */
namespace MLA\Workflow;



interface WorkflowInterface
{
   
    public function getId();
    public function setTransitions($transitions);
    public function getTransitions();
    public function setPlaces($places);
    public function getPlaces();
    public function addTransition($t);
    public function addPlace($p);
    public function connectTP($t,$p);
    public function connectPT($p, $t);
    
}
