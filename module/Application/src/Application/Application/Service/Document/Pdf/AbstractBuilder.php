<?php
namespace Application\Application\Service\Document\Pdf;

use Application\Application\Service\Document\DocumentBuilderInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractBuilder implements DocumentBuilderInterface
{

    protected $pdf;

    /**
     *
     * @param DefaultPdf $pdf
     */
    public function __construct(DefaultPdf $pdf = null)
    {
        /**
         *
         * @todo
         */
        define('PDF_HEADER_LOGO', ROOT . '/public/images/mascot.gif');

        // use defaut pdf with customer header and footer.
        if ($pdf == null) {
            $this->pdf = new DefaultPdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            return;
        }

        $this->pdf = $pdf;
    }

    /**
     *
     * @return \Application\Application\Service\Document\Pdf\DefaultPdf
     */
    public function getPdf()
    {
        return $this->pdf;
    }
}
