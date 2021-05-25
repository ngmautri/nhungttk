<?php
namespace Application\Application\Service\ItemAttribute;

use Application\Application\Service\ItemAttribute\Contracts\ItemAttributeServiceInterface;
use Application\Infrastructure\Persistence\Domain\Doctrine\ItemAttributeQueryRepositoryImpl;
use Application\Service\AbstractService;

class ItemAttributeService extends AbstractService implements ItemAttributeServiceInterface
{

    public function getRootEntityById($id, $locale = 'en_EN')
    {
        $rep = new ItemAttributeQueryRepositoryImpl($this->getDoctrineEM());
        return $rep->getById($id);
    }

    public function getRootEntityDetailByTokenId($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {}

    public function getRootEntityByTokenIdFromDB($id, $token, $outputStrategy = null, $locale = 'en_EN')
    {}

    public function getRootEntityOfMember($target_id, $target_token, $entity_id, $entity_token, $locale = 'en_EN')
    {}

    public function getRootEntityByIdFromDB($id, $outputStrategy = null, $locale = 'en_EN')
    {}

    public function getRootEntityByTokenId($id, $token, $locale = 'en_EN')
    {}

    public function getRootEntityByUuid($uuid, $locale = 'en_EN')
    {}
}
