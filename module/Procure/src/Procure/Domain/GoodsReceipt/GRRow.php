<?php
namespace Procure\Domain\GoodsReceipt;

use Procure\Domain\AbstractRow;

/**
 * Goods Receipt Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRRow extends AbstractRow
{

    protected $grDate;

    protected $transactionType;

    protected $isReversed;

    protected $reversalDate;

    protected $reversalReason;

    protected $reversalDoc;

    protected $flow;

    protected $gr;

    protected $apInvoiceRow;

    protected $glAccount;

    protected $costCenter;

    protected $lastchangedBy;

    protected $poRow;
    
    
    /**
     * @return mixed
     */
    public function getGrDate()
    {
        return $this->grDate;
    }

    /**
     * @return mixed
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @return mixed
     */
    public function getIsReversed()
    {
        return $this->isReversed;
    }

    /**
     * @return mixed
     */
    public function getReversalDate()
    {
        return $this->reversalDate;
    }

    /**
     * @return mixed
     */
    public function getReversalReason()
    {
        return $this->reversalReason;
    }

    /**
     * @return mixed
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }

    /**
     * @return mixed
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     * @return mixed
     */
    public function getGr()
    {
        return $this->gr;
    }

    /**
     * @return mixed
     */
    public function getApInvoiceRow()
    {
        return $this->apInvoiceRow;
    }

    /**
     * @return mixed
     */
    public function getGlAccount()
    {
        return $this->glAccount;
    }

    /**
     * @return mixed
     */
    public function getCostCenter()
    {
        return $this->costCenter;
    }

    /**
     * @return mixed
     */
    public function getLastchangedBy()
    {
        return $this->lastchangedBy;
    }

    /**
     * @return mixed
     */
    public function getPoRow()
    {
        return $this->poRow;
    }

}
