<?php
namespace Procure\Domain\GoodsReceipt;

use Procure\Domain\GenericRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class BaseRow extends GenericRow
{

    // Specific Attributes
    // =================================
    protected $grDate;

    protected $reversalReason;

    protected $reversalDoc;

    protected $flow;

    protected $gr;

    protected $apInvoiceRow;

    protected $poRow;

    protected $poId;

    protected $poToken;

    protected $apId;

    protected $apToken;

    /**
     *
     * @param mixed $grDate
     */
    protected function setGrDate($grDate)
    {
        $this->grDate = $grDate;
    }

    /**
     *
     * @param mixed $reversalReason
     */
    protected function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;
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
     * @param mixed $gr
     */
    protected function setGr($gr)
    {
        $this->gr = $gr;
    }

    /**
     *
     * @param mixed $apInvoiceRow
     */
    protected function setApInvoiceRow($apInvoiceRow)
    {
        $this->apInvoiceRow = $apInvoiceRow;
    }

    /**
     *
     * @param mixed $poRow
     */
    protected function setPoRow($poRow)
    {
        $this->poRow = $poRow;
    }

    /**
     *
     * @param mixed $poId
     */
    protected function setPoId($poId)
    {
        $this->poId = $poId;
    }

    /**
     *
     * @param mixed $poToken
     */
    protected function setPoToken($poToken)
    {
        $this->poToken = $poToken;
    }

    /**
     *
     * @param mixed $apId
     */
    protected function setApId($apId)
    {
        $this->apId = $apId;
    }

    /**
     *
     * @param mixed $apToken
     */
    protected function setApToken($apToken)
    {
        $this->apToken = $apToken;
    }

    /**
     *
     * @return mixed
     */
    public function getGrDate()
    {
        return $this->grDate;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalReason()
    {
        return $this->reversalReason;
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
    public function getGr()
    {
        return $this->gr;
    }

    /**
     *
     * @return mixed
     */
    public function getApInvoiceRow()
    {
        return $this->apInvoiceRow;
    }

    /**
     *
     * @return mixed
     */
    public function getPoRow()
    {
        return $this->poRow;
    }

    /**
     *
     * @return mixed
     */
    public function getPoId()
    {
        return $this->poId;
    }

    /**
     *
     * @return mixed
     */
    public function getPoToken()
    {
        return $this->poToken;
    }

    /**
     *
     * @return mixed
     */
    public function getApId()
    {
        return $this->apId;
    }

    /**
     *
     * @return mixed
     */
    public function getApToken()
    {
        return $this->apToken;
    }
}
