<?php
namespace Application\Application\Service\AccountChart\Upload;

use Application\Application\Command\Options\CreateMemberCmdOptions;
use Application\Domain\Company\CompanyVO;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Service\SharedService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface AccountChartUploadInterface
{

    public function run(CompanyVO $companyVO, BaseChart $rootEntity, $file, CreateMemberCmdOptions $options, SharedService $sharedService);
}
