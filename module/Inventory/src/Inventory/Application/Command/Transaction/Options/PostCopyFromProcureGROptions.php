<?php
namespace Inventory\Application\Command\Transaction\Options;

use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostCopyFromProcureGROptions implements CommandOptions
{

    private $userId;

    private $version;

    private $triggeredBy;

    private $triggeredOn;

    private $sourceObj;

    /**
     *
     * @return mixed
     */
    public function getSourceObj()
    {
        return $this->sourceObj;
    }

    /**
     *
     * @param int $companyId
     * @param int $userId
     * @param string $triggeredBy
     */
    public function __construct($companyId, $userId, $triggeredBy, $sourceObj)
    {
        if ($companyId == null) {
            throw new InvalidArgumentException(sprintf("Company ID not given! %s", $companyId));
        }

        if ($userId == null) {
            throw new InvalidArgumentException(sprintf("User ID not given! %s", $userId));
        }

        if ($triggeredBy == null || $triggeredBy == "") {
            throw new InvalidArgumentException(sprintf("Trigger not given! %s", $userId));
        }

        if ($sourceObj == null) {
            throw new InvalidArgumentException(sprintf("Source Object not given! %s", "AP Document required"));
        }

        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->triggeredBy = $triggeredBy;
        $this->sourceObj = $sourceObj;
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

    public function getCompanyVO()
    {}
}
