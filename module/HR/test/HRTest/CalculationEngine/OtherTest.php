<?php
namespace HRTest\CalculationEngine;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit_Framework_TestCase;

class OtherTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        $calc = Calculation::getInstance();
        print_r($calc->getImplementedFunctionNames());

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()
            ->setCellValue('B1', 'Range 1')
            ->setCellValue('B2', 2)
            ->setCellValue('B3', 8)
            ->setCellValue('B4', 10)
            ->setCellValue('B5', true)
            ->setCellValue('B6', false)
            ->setCellValue('B7', 'Text String')
            ->setCellValue('B9', '22')
            ->setCellValue('B10', 4)
            ->setCellValue('B11', 6)
            ->setCellValue('B12', 12);

        $spreadsheet->getActiveSheet()->setCellValue('B14', '=round(average(B2:B4),1)');

        print_r($spreadsheet->getActiveSheet()
            ->getCell('B14')
            ->getCalculatedValue());
    }
}