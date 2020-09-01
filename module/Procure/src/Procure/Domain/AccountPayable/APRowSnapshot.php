<?php
namespace Procure\Domain\AccountPayable;

use Procure\Domain\RowSnapshot;
use Procure\Domain\Service\Contracts\SharedQueryServiceInterface as ProcureQueryService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APRowSnapshot extends RowSnapshot
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\RowSnapshot::updateProcureRef()
     */
    public function updateProcureRef(ProcureQueryService $queryService)
    {}

    public $reversalReason;

    public $reversalDoc;

    public $isReversable;

    public $grRow;

    public $poRow;

    public $poId;

    public $poToken;

    public $grId;

    public $grToken;

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
    public function getGrId()
    {
        return $this->grId;
    }

    /**
     *
     * @return mixed
     */
    public function getGrToken()
    {
        return $this->grToken;
    }

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