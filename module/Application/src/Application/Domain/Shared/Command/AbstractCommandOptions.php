<?php
namespace Procure\Application\Command\PO\Options;

use Application\Domain\Shared\Command\CommandOptions;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractCommandOptions implements CommandOptions
{

    private $userId;

    private $version;

    private $triggeredBy;

    private $triggeredOn;
    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return mixed
     */
    public function getTriggeredBy()
    {
        return $this->triggeredBy;
    }

    /**
     * @return mixed
     */
    public function getTriggeredOn()
    {
        return $this->triggeredOn;
    }


    
 
}
