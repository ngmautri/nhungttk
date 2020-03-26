<?php
namespace Application\Application\Service\MessageStore;

use Application\Service\AbstractService;
use Application\Infrastructure\Doctrine\MessageStoreRepository;

/**
 * Message Query
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class MessageQuery extends AbstractService
{

    private $messageReposiory;

    /**
     *
     * @return \Application\Infrastructure\Doctrine\MessageStoreRepository
     */
    public function getMessageReposiory()
    {
        return $this->messageReposiory;
    }

    /**
     *
     * @param MessageStoreRepository $messageReposiory
     */
    public function setMessageReposiory(MessageStoreRepository $messageReposiory)
    {
        $this->messageReposiory = $messageReposiory;
    }

    public function getMessagesOf($entity_id, $entity_token, $sort_by, $sort, $limit, $offset)
    {
        $rep = $this->getMessageReposiory();
        return $rep->getMessages($entity_id, $entity_token, $limit, $offset);
    }

    public function getTotalMessagesOf($entity_id, $entity_token)
    {
        $rep = $this->getMessageReposiory();
        return $rep->getTotalMessages($entity_id, $entity_token);
    }
}
