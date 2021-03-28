<?php
namespace Application\Domain\Company\ValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class TaxCode
{

    /**
     *
     * @var string
     */
    private $code;

    /**
     *
     * @param string $id
     */
    public function __construct($code)
    {
        $this->code = $code;
    }
}
