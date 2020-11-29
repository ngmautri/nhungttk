<?php
namespace Procure\Application\Command\Options;

use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UpdateRowCmdOptions extends CmdOptions
{

    private $rootEntity;

    private $localEntity;

    private $entityId;

    private $entityToken;

    /**
     *
     * @param int $companyId
     * @param int $userId
     * @param string $triggeredBy
     */
    public function __construct($rootEntity, $localEntity, $entityId, $entityToken, $version, $userId, $triggeredBy)
    {
        Assert::notNull($rootEntity, sprintf("Root entity ID not given! %s", ""));
        Assert::notNull($localEntity, sprintf("Local entity not given! %s", __METHOD__));
        Assert::notNull($userId, sprintf("User ID not given! %s", $userId));
        Assert::notNull($triggeredBy, sprintf("Triggernot given! %s", $triggeredBy));

        $this->rootEntity = $rootEntity;
        $this->localEntity = $localEntity;
        $this->entityId = $entityId;
        $this->entityToken = $entityToken;
        $this->version= $version;
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
    public function getLocalEntity()
    {
        return $this->localEntity;
    }

    /**
     *
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     *
     * @return mixed
     */
    public function getEntityToken()
    {
        return $this->entityToken;
    }
}
