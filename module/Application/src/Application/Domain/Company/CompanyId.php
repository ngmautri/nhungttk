<?php
namespace Application\Domain\Company;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CompanyId
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
