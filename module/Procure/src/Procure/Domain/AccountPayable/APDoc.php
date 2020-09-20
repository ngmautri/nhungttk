<?php
namespace Procure\Domain\AccountPayable;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\AccountPayable\Validator\ValidatorFactory;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Event\Ap\ApHeaderCreated;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;
use InvalidArgumentException;
use Procure\Domain\Contracts\ProcureTrxStatus;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class APDoc extends GenericAP
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\AccountPayable\GenericAP::specify()
     */
    public function specify()
    {
        $this->setDocType(ProcureDocType::INVOICE);
    }

    private static $instance = null;

    // ===================
    private function __construct()
    {}

    /**
     *
     * @return \Procure\Domain\AccountPayable\APDoc
     */
    public static function getInstance()
    {
        return new self();
    }
}