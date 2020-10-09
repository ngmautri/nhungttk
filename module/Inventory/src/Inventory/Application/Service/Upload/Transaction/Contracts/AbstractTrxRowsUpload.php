<?php
namespace Inventory\Application\Service\Upload\Transaction\Contracts;

use Application\Application\Service\Shared\FXServiceImpl;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Service\AbstractService;
use Inventory\Application\Service\Item\FIFOServiceImpl;
use Inventory\Application\Specification\Inventory\InventorySpecificationFactoryImpl;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Service\TrxPostingService;
use Inventory\Domain\Service\TrxValuationService;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Infrastructure\Doctrine\TrxCmdRepositoryImpl;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractTrxRowsUpload extends AbstractService implements TrxRowsUploadInterface
{

    /**
     *
     * @param GenericTrx $trx
     * @param string $file
     */
    abstract protected function run(GenericTrx $trx, $file, SharedService $sharedService);

    /**
     *
     * @param GenericTrx $trx
     * @param string $file
     * @throws \InvalidArgumentException
     */
    protected function setUpSharedService()
    {
        $sharedSpecsFactory = new ZendSpecificationFactory($this->getDoctrineEM());

        $fxService = new FXServiceImpl();
        $fxService->setDoctrineEM($this->getDoctrineEM());

        $cmdRepository = new TrxCmdRepositoryImpl($this->getDoctrineEM());
        $postingService = new TrxPostingService($cmdRepository);

        $fifoService = new FIFOServiceImpl();
        $fifoService->setDoctrineEM($this->getDoctrineEM());
        $valuationService = new TrxValuationService($fifoService);

        $cmdRepository = new TrxCmdRepositoryImpl($this->getDoctrineEM());
        $postingService = new TrxPostingService($cmdRepository);

        // create share service.
        $sharedService = new SharedService($sharedSpecsFactory, $fxService, $postingService);
        $sharedService->setValuationService($valuationService);
        $sharedService->setDomainSpecificationFactory(new InventorySpecificationFactoryImpl($this->getDoctrineEM()));
        $sharedService->setLogger($this->getLogger());

        return $sharedService;
    }

    /**
     *
     * @param GenericTrx $trx
     * @param string $file
     */
    public function doUploading(GenericTrx $trx, $file)
    {
        if (! $trx instanceof GenericTrx) {
            throw new \InvalidArgumentException("GenericTrx not found!");
        }

        try {

            // take long time
            set_time_limit(2500);

            $sharedService = $this->setUpSharedService();
            $this->run($trx, $file, $sharedService);
        } catch (\Exception $e) {
            $this->logException($e, false);
            throw new \RuntimeException("Upload failed. The file might be not wrong.");
        }
    }
}
