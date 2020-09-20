<?php
namespace Procure\Domain\GoodsReceipt;

use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Contracts\ProcureGoodsFlow;
use Procure\Domain\GoodsReceipt\Contracts\ReversalDocInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRReversal extends GenericGoodsReceipt implements ReversalDocInterface
{

    public function specify()
    {
        $this->flow = ProcureGoodsFlow::OUT;
        $this->docType = ProcureDocType::GR_REVERSAL;
    }

    // =====================
    private function __construct()
    {}

    /**
     *
     * @return \Procure\Domain\GoodsReceipt\GRReversal
     */
    public static function getInstance()
    {
        return new self();
    }
}