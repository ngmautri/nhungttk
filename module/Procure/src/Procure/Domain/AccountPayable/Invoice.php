<?php
namespace Procure\Domain\AccountPayable;

use Procure\Domain\Contracts\ProcureDocType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class Invoice extends GenericAP
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\AccountPayable\GenericAP::specify()
     */
    public function specify()
    {
        $this->setDocType(ProcureDocType::INVOICE);
    }

    private static $instance = null;

    // ===================
    private function __construct()
    {}

    /**
     *
     * @return \Procure\Domain\AccountPayable\Invoice
     */
    public static function getInstance()
    {
        return new Invoice();
    }
}