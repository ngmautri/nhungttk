<?php
namespace Procure\Application\Service\Upload\Contracts;

use Application\Domain\Company\CompanyVO;
use Procure\Domain\GenericDoc;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ProcureRowsUploadInterface
{

    public function doUploading(CompanyVO $companyVO, GenericDoc $doc, $file);
}
