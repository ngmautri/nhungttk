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

    public $targetWhList;

    public $targetDepartmentList;

    public $reversalDoc;

    public $flow;

    public $relevantDocId;

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
    public function getTargetDepartmentList()
    {
        return $this->targetDepartmentList;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }

    /**
     *
     * @return mixed
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     *
     * @return mixed
     */
    public function getRelevantDocId()
    {
        return $this->relevantDocId;
    }
}