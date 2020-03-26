<?php
namespace Application\Domain\Shared;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Currency extends AbstractValueObject
{

    /**
     * Currency code.
     *
     * @var string
     */
    private $code;

    /**
     *
     * @param string $code
     */
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
     * Returns the currency code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
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
