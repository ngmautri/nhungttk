<?php
namespace Application\Domain\MessageStore;

use Application\Domain\Shared\AbstractEntity;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class MessageSnapshot extends AbstractEntity
{

    public $id;

    public $uuid;

    public $msgHeader;

    public $msgBody;

    public $createdOn;

    public $availableOn;

    public $expiredOn;

    public $createdBy;

    public $sentOn;

    public $consumedOn;

    public $queueName;

    public $eventName;

    public $remarks;

    public $className;

    public $triggeredBy;

    public $entityId;

    public $entityToken;

    public $sentToMq;

    public $changeLog;

    public $version;

    public $revisionNo;
}