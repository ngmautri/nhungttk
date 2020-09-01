<?php
namespace Procure\Domain\Service;

use Procure\Domain\APInvoice\Repository\ApQueryRepositoryInterface;
use Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface;
use Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface;
use Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface;
use Procure\Domain\QuotationRequest\Repository\QrQueryRepositoryInterface;

/**
 * Shared Query Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SharedQueryService
{

    private $apQueryRepository;

    private $prQueryRepository;

    private $poQueryRepository;

    private $grQueryRepository;

    private $qrQueryRepository;

    /**
     *
     * @return \Procure\Domain\APInvoice\Repository\ApQueryRepositoryInterface
     */
    public function getApQueryRepository()
    {
        return $this->apQueryRepository;
    }

    /**
     *
     * @return \Procure\Domain\PurchaseRequest\Repository\PrQueryRepositoryInterface
     */
    public function getPrQueryRepository()
    {
        return $this->prQueryRepository;
    }

    /**
     *
     * @return \Procure\Domain\PurchaseOrder\Repository\POQueryRepositoryInterface
     */
    public function getPoQueryRepository()
    {
        return $this->poQueryRepository;
    }

    /**
     *
     * @return \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface
     */
    public function getGrQueryRepository()
    {
        return $this->grQueryRepository;
    }

    /**
     *
     * @return \Procure\Domain\QuotationRequest\Repository\QrQueryRepositoryInterface
     */
    public function getQrQueryRepository()
    {
        return $this->qrQueryRepository;
    }

    /**
     *
     * @param ApQueryRepositoryInterface $apQueryRepository
     */
    public function setApQueryRepository(ApQueryRepositoryInterface $apQueryRepository)
    {
        $this->apQueryRepository = $apQueryRepository;
    }

    /**
     *
     * @param PrQueryRepositoryInterface $prQueryRepository
     */
    public function setPrQueryRepository(PrQueryRepositoryInterface $prQueryRepository)
    {
        $this->prQueryRepository = $prQueryRepository;
    }

    /**
     *
     * @param POQueryRepositoryInterface $poQueryRepository
     */
    public function setPoQueryRepository(POQueryRepositoryInterface $poQueryRepository)
    {
        $this->poQueryRepository = $poQueryRepository;
    }

    /**
     *
     * @param GrQueryRepositoryInterface $grQueryRepository
     */
    public function setGrQueryRepository(GrQueryRepositoryInterface $grQueryRepository)
    {
        $this->grQueryRepository = $grQueryRepository;
    }

    /**
     *
     * @param QrQueryRepositoryInterface $qrQueryRepository
     */
    public function setQrQueryRepository(QrQueryRepositoryInterface $qrQueryRepository)
    {
        $this->qrQueryRepository = $qrQueryRepository;
    }
}
