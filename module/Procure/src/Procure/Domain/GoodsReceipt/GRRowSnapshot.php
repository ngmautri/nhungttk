<?php
namespace Procure\Domain\GoodsReceipt;

use Procure\Domain\RowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRRowSnapshot extends RowSnapshot
{

    public $grDate;

    public $reversalReason;

    public $reversalDoc;

    public $flow;

    public $gr;

    public $apInvoiceRow;

    public $poRow;

    public $poId;

    public $poToken;

    public $apId;

    public $apToken;

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
}