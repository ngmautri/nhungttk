<?php
namespace Application\Application\Service\AccountChart\Upload;

use Application\Application\Command\Options\CreateMemberCmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Company\CompanyVO;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Service\AbstractService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractAccountChartUpload extends AbstractService implements AccountChartUploadInterface
{

    protected function setUpSharedService()
    {}

    /**
     *
     * @param BaseChart $rootEntity
     * @param string $file
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function doUploading(CompanyVO $companyVO, BaseChart $rootEntity, $file, CreateMemberCmdOptions $options)
    {
        if (! $rootEntity instanceof BaseChart) {
            throw new \InvalidArgumentException("BaseChart not found!");
        }

        try {
            // take long time
            set_time_limit(2500);

            $sharedService = SharedServiceFactory::createForCompany($this->getDoctrineEM());
            $this->run($companyVO, $rootEntity, $file, $options, $sharedService);
        } catch (\Exception $e) {
            $this->logException($e, false);
            throw new \RuntimeException("Upload failed. The file might be not wrong.");
        }
    }
}
