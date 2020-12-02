<?php
namespace Procure\Application\Command\Options;

use Application\Domain\Shared\Command\CommandOptions;
use Webmozart\Assert\Assert;
use Application\Domain\Company\CompanyVO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CmdOptions implements CommandOptions
{

    protected $companyId;

    protected $companyVO;

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
     * @param CompanyVO $companyVO
     * @param int $userId
     * @param string $triggeredBy
     */
    public function __construct(CompanyVO $companyVO, $userId, $triggeredBy)
    {
        Assert::isInstanceOf($companyVO, CompanyVO::class, sprintf("Company VO not given! %s", ''));
        Assert::notNull($userId, sprintf("Usd ID not given! %s", $userId));
        Assert::notNull($triggeredBy, sprintf("Triggernot given! %s", $triggeredBy));

        $this->companyVO = $companyVO;
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

    /**
     *
     * @return mixed
     */
    public function getCompanyVO()
    {
        return $this->companyVO;
    }
}
