<?php
namespace Application\Domain\Company;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Company
{

    /**
     * 
     * @var CompanyId
     */
    private $id;

    /**
     * 
     * @param CompanyId $id
     */
    public function __construct(CompanyId $id)
    {
        $this->id = $id;
    }
}
