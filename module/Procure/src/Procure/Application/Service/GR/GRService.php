<?php
namespace Procure\Application\Service\GR;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Service\AbstractService;
use Procure\Application\Service\FXService;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\Validator\Header\DefaultHeaderValidator;
use Procure\Domain\GoodsReceipt\Validator\Row\DefaultRowValidator;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;

/**
 * GR Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRService extends AbstractService
{

    private $queryRepository;

    public function getRootEntityOfRow($target_id, $target_token, $entity_id, $entity_token)
    {
        $rep = new GRQueryRepositoryImpl($this->getDoctrineEM());

        $rootEntity = null;
        $localEntity = null;

        $rootDTO = null;
        $localDTO = null;

        $rootEntity = $rep->getRootEntityByTokenId($target_id, $target_token);

        if (! $rootEntity == null) {
            $rootDTO = $rootEntity->makeDTOForGrid();

            $localEntity = $rootEntity->getRowbyTokenId($entity_id, $entity_token);

            if ($localEntity !== null) {
                $localDTO = $localEntity->makeDTOForGrid();
            }
        }

        return [
            "rootEntity" => $rootEntity,
            "localEntity" => $localEntity,
            "rootDTO" => $rootDTO,
            "localDTO" => $localDTO
        ];
    }

    /**
     *
     * @param GRQueryRepositoryImpl $queryRepository
     */
    public function setQueryRepository(GRQueryRepositoryImpl $queryRepository)
    {
        $this->queryRepository = $queryRepository;
    }

    /**
     *
     * @return \Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl
     */
    public function getQueryRepository()
    {
        return $this->queryRepository;
    }

    /**
     *
     * @param int $target_id
     * @param string $target_token
     * @param int $entity_id
     * @param string $entity_token
     */
    public function createFromPO($id, $token, CommandOptions $options)
    {
        $rep = new DoctrinePOQueryRepository($this->getDoctrineEM());

        $po = $rep->getPODetailsById($id, $token);

        $headerValidators = new HeaderValidatorCollection();

        $sharedSpecsFactory = new ZendSpecificationFactory($this->getDoctrineEM());
        $procureSpecsFactory = new ProcureSpecificationFactory($this->getDoctrineEM());
        $fxService = new FXService();
        $fxService->setDoctrineEM($this->getDoctrineEM());

        $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService, $procureSpecsFactory);
        $headerValidators->add($validator);

        $rowValidators = new RowValidatorCollection();
        $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
        $rowValidators->add($validator);

        $gr = GRDoc::createFromPo($po, $options, $headerValidators, $rowValidators);
        return $gr;
    }
}
