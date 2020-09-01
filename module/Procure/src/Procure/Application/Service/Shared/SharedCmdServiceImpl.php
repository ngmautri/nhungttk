<?php
namespace Procure\Application\Service\Shared;

use Application\Service\AbstractService;
use Procure\Domain\Service\Contracts\SharedCmdServiceInterface;
use Procure\Infrastructure\Doctrine\APCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\GRCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\POCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\PRCmdRepositoryImpl;
use Procure\Infrastructure\Doctrine\QRCmdRepositoryImpl;

/**
 * AP Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SharedCmdServiceImpl extends AbstractService implements SharedCmdServiceInterface
{

    public function getPOCmdRepository()
    {
        return new POCmdRepositoryImpl($this->getDoctrineEM());
    }

    public function getGRCmdRepository()
    {
        return new GRCmdRepositoryImpl($this->getDoctrineEM());
    }

    public function getPRCmdRepository()
    {
        return new PRCmdRepositoryImpl($this->getDoctrineEM());
    }

    public function getAPCmdRepository()
    {
        return new APCmdRepositoryImpl($this->getDoctrineEM());
    }

    public function getQRCmdRepository()
    {
        return new QRCmdRepositoryImpl($this->getDoctrineEM());
    }
}
