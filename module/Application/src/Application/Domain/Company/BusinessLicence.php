<?php
namespace Application\Domain\Company;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BusinessLicence
{

    /**
     *
     * @var string
     */
    private $registerNo;

   /**
    * 
    * @param string $id
    */
    public function __construct($registerNo)
    {
        $this->registerNo = $registerNo;
    }

  
}
