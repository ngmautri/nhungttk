<?php
namespace Application\Domain\Util\Collection\Render;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TestRenderAsSpreadsheet extends AbstractRenderAsSpreadsheet
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Render\AbstractRenderAsSpreadsheet::createRowValue()
     */
    protected function createRowValue($element)
    {
        return [
            'header1' => $element->value1,
            'header2' => $element->value2
        ];
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Render\AbstractRenderAsSpreadsheet::createHeader()
     */
    protected function createHeader()
    {
        $activeSheet = $this->getActiveSheet();
        $activeSheet->setCellValue("A1", "Report Start");

        $activeSheet->getStyle('A1')
            ->getFont()
            ->setBold(true);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Render\AbstractRenderAsSpreadsheet::createFooter()
     */
    protected function createFooter()
    {
        $activeSheet = $this->getActiveSheet();
        $activeSheet->setCellValue("A" . $this->getFooterStartPos(), "Report End");
    }
}
