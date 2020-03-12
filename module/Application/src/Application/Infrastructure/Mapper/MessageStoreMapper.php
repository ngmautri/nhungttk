<?php
namespace Application\Infrastructure\Mapper;

use Application\Domain\MessageStore\MessageSnapshot;
use Application\Entity\MessageStore;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class MessageStoreMapper
{

    /**
     *
     * @param MessageStore $entity
     * @return NULL|\Application\Domain\MessageStore\MessageSnapshot
     */
    public static function createDetailSnapshot(MessageStore $entity)
    {
        if ($entity == null)
            return null;

        $snapshot = new MessageSnapshot();

        // Mapping Date
        // =====================

        // $snapshot->createdOn= $entity->getCreatedOn();
        if (! $entity->getCreatedOn() == null) {
            $snapshot->createdOn = $entity->getCreatedOn()->format("Y-m-d H:i:s");
        }

        // $snapshot->sentOn= $entity->getSentOn();
        if (! $entity->getSentOn() == null) {
            $snapshot->sentOn = $entity->getSentOn()->format("Y-m-d H:i:s");
        }

        // $snapshot->consumedOn= $entity->getConsumedOn();
        if (! $entity->getConsumedOn() == null) {
            $snapshot->consumedOn = $entity->getConsumedOn()->format("Y-m-d H:i:s");
        }

        // $snapshot->sentToMq= $entity->getSentToMq();
        if (! $entity->getSentToMq() == null) {
            $snapshot->sentToMq = $entity->getSentToMq()->format("Y-m-d H:i:s");
        }

        // $snapshot->availableOn= $entity->getAvailableOn();
        if (! $entity->getAvailableOn() == null) {
            $snapshot->availableOn = $entity->getAvailableOn()->format("Y-m-d H:i:s");
        }

        // $snapshot->expiredOn= $entity->getExpiredOn();
        if (! $entity->getExpiredOn() == null) {
            $snapshot->expiredOn = $entity->getExpiredOn()->format("Y-m-d H:i:s");
        }

        $snapshot->id = $entity->getId();
        $snapshot->uuid = $entity->getUuid();
        $snapshot->msgHeader = $entity->getMsgHeader();
        $snapshot->msgBody = $entity->getMsgBody();
        $snapshot->queueName = $entity->getQueueName();
        $snapshot->eventName = $entity->getEventName();
        $snapshot->remarks = $entity->getRemarks();
        $snapshot->className = $entity->getClassName();
        $snapshot->triggeredBy = $entity->getTriggeredBy();
        $snapshot->entityId = $entity->getEntityId();
        $snapshot->entityToken = $entity->getEntityToken();
        $snapshot->changeLog = $entity->getChangeLog();
        $snapshot->version = $entity->getVersion();
        $snapshot->revisionNo = $entity->getRevisionNo();

        return $snapshot;
    }
}
