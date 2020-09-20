<?php
namespace Procure\Domain\AccountPayable;

use Procure\Domain\Contracts\ProcureDocType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class CreditMemo extends GenericAP
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
        $this->setDocType(ProcureDocType::CREDIT_MEMO);
    }

    /**
     *
     * @return \Procure\Domain\AccountPayable\CreditMemo
     */
    public static function getInstance()
    {
        return new CreditMemo();
    }
}