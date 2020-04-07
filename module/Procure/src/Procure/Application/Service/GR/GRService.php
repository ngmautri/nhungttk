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

/**
 * GR Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRService extends AbstractService
{

    private $cmdRepository;

    private $queryRepository;

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
       
       $validator = new DefaultHeaderValidator($sharedSpecsFactory, $fxService,$procureSpecsFactory);
       $headerValidators->add($validator);
       
          
       $rowValidators = new RowValidatorCollection();
       $validator = new DefaultRowValidator($sharedSpecsFactory, $fxService);
       $rowValidators->add($validator);
       
       $gr = GRDoc::createFromPo($po,$options,$headerValidators, $rowValidators);       
       return $gr;      
    }
}
