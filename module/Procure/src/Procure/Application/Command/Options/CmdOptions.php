<?php
namespace Procure\Application\Command\Options;

use Application\Domain\Shared\Command\CommandOptions;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CmdOptions implements CommandOptions
{

    protected $companyId;

    protected $userId;

    protected $version;

    protected $triggeredBy;

    protected $triggeredOn;

    protected $locale;

    /**
     *
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     *
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     *
     * @param int $companyId
     * @param int $userId
     * @param string $triggeredBy
     */
    public function __construct($companyId, $userId, $triggeredBy)
    {
        Assert::notNull($companyId, sprintf("Company ID not given! %s", $companyId));
        Assert::notNull($userId, sprintf("Company ID not given! %s", $userId));
        Assert::notNull($triggeredBy, sprintf("Triggernot given! %s", $companyId));

        $this->companyId = $companyId;
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
