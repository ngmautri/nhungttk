<?php
namespace Application\Application\Command\Options;

use Application\Domain\Company\CompanyVO;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostCopyFromCmdOptions extends CmdOptions
{

    private $rootEntity;

    /**
     *
     * @param int $companyId
     * @param int $userId
     * @param string $triggeredBy
     */
    public function __construct(CompanyVO $companyVO, $userId, $triggeredBy, $rootEntity)
    {
        Assert::isInstanceOf($companyVO, CompanyVO::class, sprintf("Company VO not given! %s", ''));
        Assert::notNull($rootEntity, sprintf("Source entity not found %s", __METHOD__));
        Assert::notNull($userId, sprintf("User ID not given! %s", __METHOD__));
        Assert::notNull($triggeredBy, sprintf("Trigger not found! %s", __METHOD__));

        $this->companyVO = $companyVO;
        $this->userId = $userId;
        $this->triggeredBy = $triggeredBy;
        $this->rootEntity = $rootEntity;
    }

    /**
     *
     * @return mixed
     */
    public function getRootEntity()
    {
        return $this->rootEntity;
    }
}
