<?php
namespace Procure\Domain\GoodsReceipt;


use Procure\Domain\DocSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRSnapshot extends DocSnapshot
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