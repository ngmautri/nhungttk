<?php
namespace Procure\Domain\AccountPayable;

use Procure\Domain\RowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APRowSnapshot extends RowSnapshot
{

    public $instance;

    public $reversalReason;

    public $reversalDoc;

    public $isReversable;

    public $grRow;

    public $poRow;

    /**
     *
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
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
    public function getIsReversable()
    {
        return $this->isReversable;
    }

    /**
     *
     * @return mixed
     */
    public function getGrRow()
    {
        return $this->grRow;
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