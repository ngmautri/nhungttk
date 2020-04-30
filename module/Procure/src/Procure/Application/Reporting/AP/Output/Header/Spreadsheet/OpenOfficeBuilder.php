<?php
namespace Procure\Application\Service\AP\Output\Spreadsheet;

use Application\Application\Service\Document\Spreadsheet\AbstractBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OpenOfficeBuilder extends AbstractBuilder
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Document\DocumentBuilderInterface::buildBody()
     */
    public function buildBody($params = null)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Document\DocumentBuilderInterface::getDocument()
     */
    public function getDocument()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Document\DocumentBuilderInterface::buildFooter()
     */
    public function buildFooter($params = null)
    {
        $docNumber = null;
        if (isset($params["docNumber"])) {
            $docNumber = $params["docNumber"];
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->getPhpSpreadsheet()->setActiveSheetIndex(0);

        $this->getPhpSpreadsheet()
            ->getActiveSheet()
            ->setAutoFilter("A7:U7");

        // Redirect output to a client's web browser (Excel2007)

        $filename_tmp = sprintf("invoice_%s.ods", $docNumber);
        $filename = sprintf('Content-Disposition: attachment;filename="%s"', $filename_tmp);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header($filename);

        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        // header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->getPhpSpreadsheet(), 'Ods');
        $objWriter->save('php://output');
        exit();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Document\DocumentBuilderInterface::buildHeader()
     */
    public function buildHeader($params = null)
    {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();

        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(ROOT . '/public/images/mascot.gif');
        $drawing->setHeight(80);

        $drawing->setWorksheet($this->getPhpSpreadsheet()
            ->getActiveSheet());

        // Set document properties
        $this->getPhpSpreadsheet()
            ->getProperties()
            ->setCreator("Nguyen Mau Tri")
            ->setLastModifiedBy("Nguyen Mau Tri")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("PO File");
    }
}
