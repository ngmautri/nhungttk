<?php
namespace Application\Domain\Company;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AbstractCompany
{
    private $id;
    private $companyName;

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }
}
