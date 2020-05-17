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

    public $targetWhList;

    public $targetDepartmentList;

    /**
     *
     * @return mixed
     */
    public function getTargetDepartmentList()
    {
        return $this->targetDepartmentList;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetWhList()
    {
        return $this->targetWhList;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }
}