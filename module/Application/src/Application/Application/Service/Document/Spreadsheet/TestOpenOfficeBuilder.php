<?php
namespace Application\Application\Service\Document\Spreadsheet;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TestOpenOfficeBuilder extends AbstractBuilder
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
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->getPhpSpreadsheet(), 'Ods');
        $objWriter->save("C:/NMT/text.ods");
        // exit();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Document\DocumentBuilderInterface::buildHeader()
     */
    public function buildHeader($params = null)
    {

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
