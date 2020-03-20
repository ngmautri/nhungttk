<?php
namespace Procure\Application\Command\PO\Options;

use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Exception\PoRowCreateException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoRowCreateOptions implements CommandOptions
{

    private $rootEntity;

    private $rootEntityId;

    private $rootEntityToken;

    private $userId;

    private $version;

    private $triggeredBy;

    /**
     *
     * @param int $companyId
     * @param int $userId
     * @param string $triggeredBy
     */
    public function __construct($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, $triggeredBy)
    {
        if ($rootEntity == null) {
            throw new PoRowCreateException(sprintf("$rootEntity not given! %s", $rootEntity));
        }

        if ($userId == null) {
            throw new PoRowCreateException(sprintf("User ID not given! %s", $userId));
        }

        if ($triggeredBy == null || $triggeredBy == "") {
            throw new PoRowCreateException(sprintf("Trigger not given! %s", $userId));
        }

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

    /**
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
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
     * @return string
     */
    public function getTriggeredBy()
    {
        return $this->triggeredBy;
    }


   
}
