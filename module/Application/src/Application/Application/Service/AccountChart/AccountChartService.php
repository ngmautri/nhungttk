<?php
namespace Application\Application\Service\AccountChart;

use Application\Application\Service\AccountChart\Contracts\AccountChartServiceInterface;
use Application\Infrastructure\Persistence\Domain\Doctrine\ChartQueryRepositoryImpl;
use Application\Service\AbstractService;

class AccountChartService extends AbstractService implements AccountChartServiceInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Contracts\EntityServiceInterface::getRootEntityById()
     */
    public function getRootEntityById($id, $locale = 'en_EN')
    {
        $rep = new ChartQueryRepositoryImpl($this->getDoctrineEM());
        $result = $rep->getById($id);
        return $result;
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
