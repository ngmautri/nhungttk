<?php
namespace Inventory\Application\Command\Warehouse\Options;

use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WhCreateOptions implements CommandOptions
{

    private $companyId;

    private $localCurrencyId;

    private $version;

    private $triggeredBy;

    private $triggeredOn;

    /**
     *
     * @return mixed
     */
    public function getLocalCurrencyId()
    {
        return $this->localCurrencyId;
    }

    /**
     *
     * @param int $companyId
     * @param int $userId
     * @param string $triggeredBy
     */
    public function __construct($companyId, $localCurrencyId, $userId, $triggeredBy)
    {
        if ($companyId == null) {
            throw new InvalidArgumentException(sprintf("Company ID not given! %s", $companyId));
        }

        if ($localCurrencyId == null) {
            throw new InvalidArgumentException(sprintf("Local currency not given! %s", $localCurrencyId));
        }

        if ($userId == null) {
            throw new InvalidArgumentException(sprintf("User ID not given! %s", $userId));
        }

        if ($triggeredBy == null || $triggeredBy == "") {
            throw new InvalidArgumentException(sprintf("Trigger not given! %s", $userId));
        }

        $this->companyId = $companyId;
        $this->localCurrencyId = $localCurrencyId;
        $this->userId = $userId;
        $this->triggeredBy = $triggeredBy;
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
