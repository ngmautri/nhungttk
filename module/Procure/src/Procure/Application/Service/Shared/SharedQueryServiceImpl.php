<?php
namespace Procure\Application\Service\Shared;

use Application\Service\AbstractService;
use Procure\Domain\Service\Contracts\SharedQueryServiceInterface;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;
use Procure\Infrastructure\Doctrine\QRQueryRepositoryImpl;

/**
 * AP Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SharedQueryServiceImpl extends AbstractService implements SharedQueryServiceInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Contracts\SharedQueryServiceInterface::getAPQueryRepository()
     */
    public function getAPQueryRepository()
    {
        return new APQueryRepositoryImpl($this->getDoctrineEM());
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Contracts\SharedQueryServiceInterface::getQRQueryRepository()
     */
    public function getQRQueryRepository()
    {
        return new QRQueryRepositoryImpl($this->getDoctrineEM());
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Contracts\SharedQueryServiceInterface::getPRQueryRepository()
     */
    public function getPRQueryRepository()
    {
        return new PRQueryRepositoryImpl($this->getDoctrineEM());
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Contracts\SharedQueryServiceInterface::getPOQueryRepository()
     */
    public function getPOQueryRepository()
    {
        return new POQueryRepositoryImpl($this->getDoctrineEM());
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Contracts\SharedQueryServiceInterface::getGRQueryRepository()
     */
    public function getGRQueryRepository()
    {
        return new GRQueryRepositoryImpl($this->getDoctrineEM());
    }
}
