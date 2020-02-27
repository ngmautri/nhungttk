<?php
namespace Application\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\NoResultException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class MessageStoreRepository extends AbstractDoctrineRepository
{

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
}
