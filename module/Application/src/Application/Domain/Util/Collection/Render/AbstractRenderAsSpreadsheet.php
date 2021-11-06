<?php
namespace Application\Domain\Util\Collection\Render;

use Application\Application\Service\Document\Spreadsheet\AbstractBuilder;
use Application\Domain\Util\ExcelColumnMap;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractRenderAsSpreadsheet extends AbstractCollectionRender
{

    protected $builder;

    protected $headerStartPos = 1;

    protected $bodyStartPos = 7;

    protected $footerStartPos;

    protected $headerParams = [];

    protected $footerParams = [];

    protected $activeSheet;

    protected $phpSpreadSheet;

    /*
     * |=============================
     * |Abstract function
     * |=============================
     */
    protected abstract function createHeader();

    protected abstract function createRowValue($element);

    protected abstract function createFooter();

    protected function getActiveSheet()
    {
        $spreadSheetObj = $this->getBuilder()->getPhpSpreadsheet();
        return $spreadSheetObj->setActiveSheetIndex(0);
    }

    protected function getPhpSpreadSheet()
    {
        return $this->getBuilder()->getPhpSpreadsheet();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Contracts\CollectionRenderInterface::execute()
     */
    public function execute()
    {
        $this->assertBuilder();

        /*
         * |=============================
         * |Build Excel Header
         * |=============================
         */
        $this->getBuilder()->buildHeader($this->getHeaderParams());

        // create header
        $this->createHeader();

        /*
         * |=============================
         * |Build Body
         * |=============================
         */

        $rowStartPos = $this->getBodyStartPos();
        $cols = ExcelColumnMap::COLS;
        $headerCreated = false;
        $headerValues = [];

        $i = 0;
        foreach ($this->getCollection() as $r) {

            if ($r == null) {
                continue;
            }

            // format
            $this->getFormatter()->format($r);

            $i ++;
            $l = $rowStartPos + $i;
            $columnValues = $this->createRowValue($r);

            if ($columnValues == null) {
                throw new \Exception("Spreadsheet row value not set!");
            }

            $j = 0;
            foreach ($columnValues as $k => $v) {

                // create header
                if (! $headerCreated && $headerValues == null) {
                    $headerValues = array_keys($columnValues);
                    $headerCreated = TRUE;
                }

                $this->getActiveSheet()->setCellValue($cols[$j] . $l, $v);
                $j ++;
            }
        }

        $n = 0;
        foreach ($headerValues as $v) {
            $this->getActiveSheet()->setCellValue($cols[$n] . $rowStartPos, $v);
            $n ++;
        }

        // Footer Start Postion
        $this->footerStartPos = $this->bodyStartPos + $i + 3;

        if ($headerValues == null) {
            throw new \Exception("Value for Spreadsheet Header not set!");
        }

        // create header
        $this->createFooter();

        /*
         * |=============================
         * |Build Footer and export.
         * |=============================
         */
        $this->getBuilder()->buildFooter($this->getFooterParams());
    }

    /*
     * |=============================
     * |Private Helper
     * |=============================
     */
    private function assertBuilder()
    {
        if ($this->getBuilder() == null) {
            throw new \Exception("Spreadsheet Builder not found!");
        }
    }

    /*
     * |=============================
     * |Getter and Setter
     * |=============================
     */

    /**
     *
     * @return \Application\Application\Service\Document\Spreadsheet\AbstractBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     *
     * @param AbstractBuilder $builder
     */
    public function setBuilder(AbstractBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     *
     * @return multitype:
     */
    public function getHeaderParams()
    {
        return $this->headerParams;
    }

    /**
     *
     * @return multitype:
     */
    public function getFooterParams()
    {
        return $this->footerParams;
    }

    /**
     *
     * @param multitype: $headerParams
     */
    public function setHeaderParams($headerParams)
    {
        $this->headerParams = $headerParams;
    }

    /**
     *
     * @param multitype: $footerParams
     */
    public function setFooterParams($footerParams)
    {
        $this->footerParams = $footerParams;
    }

    /**
     *
     * @return number
     */
    public function getHeaderStartPos()
    {
        return $this->headerStartPos;
    }

    /**
     *
     * @return number
     */
    public function getBodyStartPos()
    {
        return $this->bodyStartPos;
    }

    /**
     *
     * @return number
     */
    public function getFooterStartPos()
    {
        return $this->footerStartPos;
    }

    /**
     *
     * @param number $headerStartPos
     */
    public function setHeaderStartPos($headerStartPos)
    {
        $this->headerStartPos = $headerStartPos;
    }

    /**
     *
     * @param number $bodyStartPos
     */
    public function setBodyStartPos($bodyStartPos)
    {
        $this->bodyStartPos = $bodyStartPos;
    }

    /**
     *
     * @param number $footerStartPos
     */
    public function setFooterStartPos($footerStartPos)
    {
        $this->footerStartPos = $footerStartPos;
    }
}
