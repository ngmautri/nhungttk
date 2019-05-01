<?php
namespace Inventory\Domain\Item;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemId
{

    /**
     *
     * @var string
     */
    private $id;
    
    /**
     *
     * @var string
     */
    private $uuid;

  
   /**
    * 
    * @param string $id
    * @param string $uuid
    */
    public function __construct($uuid, $id=null)
    {
        $this->id = $id;
        $this->uuid = $uuid;
    }
}
