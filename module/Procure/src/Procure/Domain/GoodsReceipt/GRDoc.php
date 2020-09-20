<?php
namespace Procure\Domain\GoodsReceipt;

use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Contracts\ProcureGoodsFlow;
use Procure\Domain\GoodsReceipt\Contracts\DocInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRDoc extends GenericGoodsReceipt implements DocInterface
{

    public function specify()
    {
        $this->flow = ProcureGoodsFlow::IN;
        $this->docType = ProcureDocType::GR;
    }

    // =====================
    private function __construct()
    {}

    /**
     *
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function getInstance()
    {
        return new self();
    }
}