<?php
namespace Procure\Application\Command\Options;

use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PostCmdOptions extends CmdOptions
{

    private $rootEntity;

    private $rootEntityId;

    private $rootEntityToken;

    public function __construct($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, $triggeredBy)
    {
        Assert::notNull($rootEntity, sprintf("Root entity not found %s", __METHOD__));
        Assert::notNull($userId, sprintf("User ID not given! %s", __METHOD__));
        Assert::notNull($triggeredBy, sprintf("Trigger not found! %s", __METHOD__));

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
