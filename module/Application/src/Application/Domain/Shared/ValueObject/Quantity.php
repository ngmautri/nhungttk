<?php
namespace Application\Domain\Shared;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Quantity extends AbstractValueObject
{

    private $quantity;

    public function __construct($code)
    {
        if (! is_string($code)) {
            throw new \InvalidArgumentException('Currency code should be string');
        }

        if ($code === '') {
            throw new \InvalidArgumentException('Currency code should not be empty string');
        }

        $this->code = $code;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }
}
