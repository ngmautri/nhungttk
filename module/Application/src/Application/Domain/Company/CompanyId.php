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
    * @param string $id
    */
    public function __construct($id)
    {
        $this->id = $id;
    }

  
}
