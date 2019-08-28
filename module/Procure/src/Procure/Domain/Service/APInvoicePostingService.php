<?php
namespace Procure\Domain\Service;

use Application\Notification;
use Procure\Domain\APInvoice\APInvoiceCmdRepositoryInterface;
use Procure\Domain\APInvoice\APInvoiceQueryRepositoryInterface;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\APInvoice\GenericAPInvoice;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APInvoicePostingService
{

    protected $apInvoiceCmdRepository;

    protected $apInvoiceQueryRepository;

    public function __construct(APInvoiceCmdRepositoryInterface $apInvoiceCmdRepository, APInvoiceQueryRepositoryInterface $apInvoiceQueryRepository)
    {
        if ($apInvoiceCmdRepository == null) {
            throw new InvalidArgumentException("Transaction cmd repository not set!");
        }

        if ($apInvoiceQueryRepository == null) {
            throw new InvalidArgumentException("Transaction query repository not set!");
        }

        $this->transactionCmdRepository = $apInvoiceCmdRepository;
        $this->transactionQueryRepository = $apInvoiceQueryRepository;
    }

   
}
