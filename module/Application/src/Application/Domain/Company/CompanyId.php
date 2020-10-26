<?php
namespace Application\Domain\Company;

use Webmozart\Assert\Assert;

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

    public function __construct($id)
    {
        Assert::integer($id, "Company Id not valid!");
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
