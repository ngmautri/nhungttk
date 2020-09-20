<?php
namespace Procure\Domain\AccountPayable;

use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Contracts\ReversalDocInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class CreditMemoReversal extends GenericAP implements ReversalDocInterface
{

    private function __construct()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\AccountPayable\GenericAP::specify()
     */
    public function specify()
    {
        $this->setDocType(ProcureDocType::CREDIT_MEMO_REVERSAL);
    }

    /**
     *
     * @return \Procure\Domain\AccountPayable\CreditMemoReversal
     */
    public static function getInstance()
    {
        return new CreditMemoReversal();
    }
}