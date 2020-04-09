<?php
namespace Procure\Application\Service\Output;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractSaveAsSpreadsheet implements SaveAsInterface
{

    protected $spreadSheetBuilder;

    /**
     *
     * @return \Procure\Application\Service\Output\AbstractSpreadsheetBuilder
     */
    public function getSpreadSheetBuilder()
    {
        return $this->spreadSheetBuilder;
    }

    public function __construct(AbstractSpreadsheetBuilder $builder)
    {
        $this->spreadSheetBuilder = $builder;
    }
}
