<?php
namespace Procure\Domain\GoodsReceipt;

use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Contracts\ProcureGoodsFlow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRReturn extends GenericGoodsIssue
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\GenericGR::specify()
     */
    public function specify()
    {
        $this->flow = ProcureGoodsFlow::OUT;
        $this->docType = ProcureDocType::GOODS_RETURN;
    }

    // =====================
    private function __construct()
    {}

    /**
     *
     * @return \Procure\Domain\GoodsReceipt\GRReturn
     */
    public static function getInstance()
    {
        return new self();
    }
}