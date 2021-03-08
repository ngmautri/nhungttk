<?php
namespace HR\Application\Service\Upload\Employee\Contracts;

use Application\Domain\EventBus\EventBusServiceInterface;
use Application\Service\AbstractService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractEmployeeUpload extends AbstractService
{

    protected $eventBusService;

    protected $companyId;

    protected $userId;

    abstract public function run($file);

    /**
     *
     * @return \Application\Domain\EventBus\EventBusServiceInterface
     */
    public function getEventBusService()
    {
        return $this->eventBusService;
    }

    /**
     *
     * @param EventBusServiceInterface $eventBusService
     */
    public function setEventBusService(EventBusServiceInterface $eventBusService)
    {
        $this->eventBusService = $eventBusService;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
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
     * @param mixed $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     *
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}
