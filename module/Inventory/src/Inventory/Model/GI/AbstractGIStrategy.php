<?php 
namespace Inventory\Model\GI;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractGIStrategy
{
    protected $contextService;
   
    abstract public function execute();

}