<?php
namespace Procure\Application\Command\PO\Options;

use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SaveCopyFromQuoteOptions implements CommandOptions
{

    private $companyId;

    private $userId;

    private $version;

    private $triggeredBy;

    private $triggeredOn;

    private $rootEntity;

    /**
     *
     * @return mixed
     */
    public function getRootEntity()
    {
        return $this->rootEntity;
    }

    /**
     *
     * @param int $companyId
     * @param int $userId
     * @param string $triggeredBy
     */
    public function __construct($companyId, $userId, $triggeredBy, $rootEntity)
    {
        if ($companyId == null) {
            throw new InvalidArgumentException(sprintf("Company ID not given! %s", __METHOD__));
        }

        if ($userId == null) {
            throw new InvalidArgumentException(sprintf("User ID not given! %s", __METHOD__));
        }

        if ($triggeredBy == null || $triggeredBy == "") {
            throw new InvalidArgumentException(sprintf("Trigger not given! %s", __METHOD__));
        }

        if ($rootEntity == null) {
            throw new InvalidArgumentException(sprintf("Root entity not given! %s", __METHOD__));
        }

        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->triggeredBy = $triggeredBy;
        $this->rootEntity = $rootEntity;
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
     * @return mixed
     */
    public function getTriggeredBy()
    {
        return $this->triggeredBy;
    }

    /**
     *
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     *
     * @return mixed
     */
    public function getTriggeredOn()
    {
        return $this->triggeredOn;
    }
}
