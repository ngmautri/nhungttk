<?php
namespace Application\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Application\Infrastructure\Mapper\MessageStoreMapper;
use Application\Infrastructure\Persistence\MessageStoreRepositoryInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class MessageStoreRepository extends AbstractDoctrineRepository implements MessageStoreRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Infrastructure\Persistence\MessageStoreRepositoryInterface::getMessages()
     */
    public function getMessages($entityId, $entityToken = null, $limit = null, $offset = null)
    {
        $results = $this->_getMessages($entityId, $entityToken, $limit, $offset);

        if (count($results) == null) {
            return null;
        }

        $resultList = array();
        foreach ($results as $r) {

            /**@var \Application\Entity\MessageStore $entity ;*/
            $entity = $r;

            $snapShot = MessageStoreMapper::createDetailSnapshot($entity, $this->getDoctrineEM());

            if ($snapShot == null) {
                continue;
            }
            $resultList[] = $snapShot;
        }

        return $resultList;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Infrastructure\Persistence\MessageStoreRepositoryInterface::getMessages()
     */
    public function getTotalMessages($entityId, $entityToken = null)
    {
        $results = $this->_getMessages($entityId, $entityToken, null, null);
        return count($results);
    }

    /**
     *
     * @param array $messages
     */
    public function setSentDate(array $messages)
    {
        if (count($messages) == 0) {
            return;
        }

        foreach ($messages as $m) {

            /**
             *
             * @var \Application\Entity\MessageStore $entity ;
             */
            $entity = $this->getDoctrineEM()
                ->getRepository('\Application\Entity\MessageStore')
                ->find($m);
            if ($entity !== null) {
                $entity->setSentOn(new \Datetime());
                $this->getDoctrineEM()->persist($entity);
            }
        }

        $this->getDoctrineEM()->flush();
    }

    public function markAsSent($message)
    {
        /**
         *
         * @var \Application\Entity\MessageStore $entity ;
         */
        $entity = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\MessageStore')
            ->find($message);
        if ($entity !== null) {
            $entity->setSentOn(new \Datetime());
            $this->getDoctrineEM()->persist($entity);
        }

        $this->getDoctrineEM()->flush();
    }

    /**
     *
     * @return NULL|array|mixed|\Doctrine\DBAL\Driver\Statement
     */
    public function getUnsentMessage()
    {
        return $this->_getUnsentMessage();
    }

    private function _getUnsentMessage()
    {
        $sql = "SELECT * FROM message_store
WHERE message_store.sent_on IS NULL";

        $sql = $sql . ";";

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\MessageStore', 'message_store');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    private function _getMessages($entityId, $entityToken, $limit, $offset)
    {
        $sql = 'SELECT * FROM message_store
WHERE entity_id=%s AND entity_token="%s"  order by created_on desc ';

        $sql = sprintf($sql, $entityId, $entityToken);

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        $sql = $sql . ";";

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\MessageStore', 'message_store');
            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
