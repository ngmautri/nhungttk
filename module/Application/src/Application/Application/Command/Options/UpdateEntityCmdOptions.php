<?php
namespace Application\Application\Command\Options;

use Application\Domain\Company\CompanyVO;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateEntityCmdOptions extends CmdOptions
{

    private $rootEntity;

    private $rootEntityId;

    private $rootEntityToken;

    private $isPosting;

    public function __construct(CompanyVO $companyVO, $rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, $triggeredBy, $isPosting = false)
    {
        Assert::isInstanceOf($companyVO, CompanyVO::class, sprintf("Company VO not given! %s", ''));
        Assert::notNull($rootEntity, sprintf("Root entity not given %s", __METHOD__));
        Assert::notNull($userId, sprintf("User ID not given! %s", __METHOD__));
        Assert::notNull($triggeredBy, sprintf("Trigger not found! %s", __METHOD__));

        $this->companyVO = $companyVO;
        $this->rootEntity = $rootEntity;
        $this->rootEntityId = $rootEntityId;
        $this->rootEntityToken = $rootEntityToken;
        $this->version = $version;
        $this->userId = $userId;
        $this->triggeredBy = $triggeredBy;
        $this->isPosting = $isPosting;
    }

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
     * @return mixed
     */
    public function getRootEntityId()
    {
        return $this->rootEntityId;
    }

    /**
     *
     * @return mixed
     */
    public function getRootEntityToken()
    {
        return $this->rootEntityToken;
    }

    /**
     *
     * @return string
     */
    public function getIsPosting()
    {
        return $this->isPosting;
    }
}
