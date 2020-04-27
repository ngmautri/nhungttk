<?php
namespace Application\Application\Event;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultParameter
{

    protected $targetId;

    protected $targetToken;

    protected $targetDocVersion;

    protected $targetRrevisionNo;

    protected $userId;

    protected $triggeredBy;

    /**
     *
     * @return mixed
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetToken()
    {
        return $this->targetToken;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetDocVersion()
    {
        return $this->targetDocVersion;
    }

    /**
     *
     * @return mixed
     */
    public function getTargetRrevisionNo()
    {
        return $this->targetRrevisionNo;
    }

    /**
     *
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     *
     * @return mixed
     */
    public function getTriggeredBy()
    {
        return $this->triggeredBy;
    }

    /**
     *
     * @param mixed $targetId
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;
    }

    /**
     *
     * @param mixed $targetToken
     */
    public function setTargetToken($targetToken)
    {
        $this->targetToken = $targetToken;
    }

    /**
     *
     * @param mixed $targetDocVersion
     */
    public function setTargetDocVersion($targetDocVersion)
    {
        $this->targetDocVersion = $targetDocVersion;
    }

    /**
     *
     * @param mixed $targetRrevisionNo
     */
    public function setTargetRrevisionNo($targetRrevisionNo)
    {
        $this->targetRrevisionNo = $targetRrevisionNo;
    }

    /**
     *
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     *
     * @param mixed $triggeredBy
     */
    public function setTriggeredBy($triggeredBy)
    {
        $this->triggeredBy = $triggeredBy;
    }
}
