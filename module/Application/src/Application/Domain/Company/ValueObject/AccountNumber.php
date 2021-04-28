<?php
namespace Application\Domain\Company\ValueObject;

use Application\Domain\Shared\ValueObject;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AccountNumber extends ValueObject
{

    private $accountNumber;

    /**
     *
     * @param int $productId
     */
    public function __construct($accountNumber, $context = null)
    {
        Assert::maxLength($accountNumber, 20, sprintf('%s ID %s is invalid. ID must be number that is greater than zero', $context, $accountNumber));
        $this->accountNumber = $accountNumber;
    }

    public function makeSnapshot()
    {}

    public function getAttributesToCompare()
    {
        return [
            $this->getValue()
        ];
    }
}
