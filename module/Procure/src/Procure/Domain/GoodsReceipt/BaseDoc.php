<?php
namespace Procure\Domain\GoodsReceipt;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BaseDoc extends AbstractGR
{

    protected $targetWhList;

    protected $targetDepartmentList;

    // Addtional Attributes
    // ===================
    protected $reversalDoc;

    protected $flow;

    protected $relevantDocId;

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

    /**
     *
     * @param mixed $reversalDoc
     */
    protected function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;
    }

    /**
     *
     * @param mixed $flow
     */
    protected function setFlow($flow)
    {
        $this->flow = $flow;
    }

    /**
     *
     * @param mixed $relevantDocId
     */
    protected function setRelevantDocId($relevantDocId)
    {
        $this->relevantDocId = $relevantDocId;
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
    public function getTargetDepartmentList()
    {
        return $this->targetDepartmentList;
    }

    /**
     *
     * @param mixed $targetWhList
     */
    public function setTargetWhList($targetWhList)
    {
        $this->targetWhList = $targetWhList;
    }

    /**
     *
     * @param mixed $targetDepartmentList
     */
    public function setTargetDepartmentList($targetDepartmentList)
    {
        $this->targetDepartmentList = $targetDepartmentList;
    }
}