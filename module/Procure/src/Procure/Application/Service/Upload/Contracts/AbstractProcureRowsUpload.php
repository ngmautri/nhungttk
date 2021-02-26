<?php
namespace Procure\Application\Service\Upload\Contracts;

use Application\Domain\Company\CompanyVO;
use Application\Service\AbstractService;
use Procure\Domain\GenericDoc;
use Procure\Domain\Service\SharedService;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractProcureRowsUpload extends AbstractService implements ProcureRowsUploadInterface
{

    abstract protected function run(CompanyVO $companyVO, GenericDoc $doc, $file, SharedService $sharedService);

    abstract protected function setUpSharedService();

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Upload\Contracts\ProcureRowsUploadInterface::doUploading()
     */
    public function doUploading(CompanyVO $companyVO, GenericDoc $doc, $file)
    {
        Assert::isInstanceOf($doc, GenericDoc::class);

        try {

            // take long time
            set_time_limit(2500);

            $sharedService = $this->setUpSharedService();
            $this->run($companyVO, $doc, $file, $sharedService);
        } catch (\Exception $e) {
            $this->logException($e, false);
            throw new \RuntimeException("Upload failed. The file might be not wrong.");
        }
    }
}
