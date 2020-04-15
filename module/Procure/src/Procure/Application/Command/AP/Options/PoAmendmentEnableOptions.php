<?php
namespace Procure\Application\Command\PO\Options;

use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Exception\PoAmendmentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoAmendmentEnableOptions implements CommandOptions
{

    private $rootEntity;

    private $rootEntityId;

    private $rootEntityToken;

    private $userId;

    private $version;

    private $triggeredBy;

    private $triggeredOn;

    public function __construct($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, $triggeredBy)
    {
        if ($rootEntity == null) {
            throw new PoAmendmentException(sprintf("Root Entity not given! %s", $rootEntity));
        }

        if ($userId == null) {
            throw new PoAmendmentException(sprintf("User ID not given! %s", $userId));
        }

        if ($triggeredBy == null || $triggeredBy == "") {
            throw new PoAmendmentException(sprintf("Trigger not given! %s", $userId));
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
    public function getVersion()
    {
        return $this->version;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandOptions::getTriggeredBy()
     */
    public function getTriggeredBy()
    {
        return $this->triggeredBy;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Command\CommandOptions::getTriggeredOn()
     */
    public function getTriggeredOn()
    {
        return $this->triggeredOn;
    }
}
