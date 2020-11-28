<?php
namespace Procure\Application\Command\Options;

use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CreateRowCmdOptions extends CmdOptions
{

    private $rootEntity;

    private $rootEntityId;

    private $rootEntityToken;

    /**
     *
     * @param int $companyId
     * @param int $userId
     * @param string $triggeredBy
     */
    public function __construct($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, $triggeredBy)
    {
        Assert::notNull($rootEntity, sprintf("Root Entitynot given! %s", ""));
        Assert::notNull($rootEntityId, sprintf("Root Entity ID not given! %s", $rootEntityId));
        Assert::notNull($userId, sprintf("User ID not given!%s", $userId));
        Assert::notNull($triggeredBy, sprintf("Triggernot given! %s", $triggeredBy));

        $this->rootEntity = $rootEntity;
        $this->rootEntityId = $rootEntityId;
        $this->rootEntityToken = $rootEntityToken;
        $this->version = $version;
        $this->userId = $userId;
        $this->triggeredBy = $triggeredBy;
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
}
