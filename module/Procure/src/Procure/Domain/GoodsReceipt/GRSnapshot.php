<?php
namespace Procure\Domain\GoodsReceipt;

use Procure\Domain\RowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRSnapshot extends RowSnapshot
{

    public $reversalDoc;

    /**
     *
     * @return mixed
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }
}