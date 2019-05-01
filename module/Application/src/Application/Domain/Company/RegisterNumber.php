<?php
namespace Application\Domain\Company;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RegisterNumber
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
