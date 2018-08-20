<?php
namespace Finance\Model\JournalEntry;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractJournalEntryStrategy
{

    protected $contextService;

    abstract public function check($trx, $item, $u);

    abstract public function doPosting($entity, $u, $isFlush = false);

    abstract public function reverse($entity, $u, $reversalDate, $isFlush = false);

    /**
     *
     * @return \Application\Service\AbstractService
     */
    public function getContextService()
    {
        return $this->contextService;
    }

    /**
     *
     * @param \Application\Service\AbstractService $contextService
     */
    public function setContextService(\Application\Service\AbstractService $contextService)
    {
        $this->contextService = $contextService;
    }
}