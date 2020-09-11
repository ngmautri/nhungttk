<?php
namespace HRTest\Barcode;

use Zend\Barcode\Barcode;
use PHPUnit_Framework_TestCase;

class GenerateBarcodeTest extends PHPUnit_Framework_TestCase

{

    const BACKUP_FOLDER = "/data/hr/employee-code";

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        $root = realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
        $folder = $root . self::BACKUP_FOLDER;

        for ($i = 4000; $i < 6000; $i ++) {

            $maxLen = 4;
            $currentLen = strlen($i);

            $tmp = "";
            for ($j = 0; $j < $maxLen - $currentLen; $j ++) {

                $tmp = $tmp . "0";
            }

            $code = $tmp . $i;

            $barcodeConf = array(
                'text' => $code
            );

            $renderConf = array(
                'imageType' => 'png'
            );

            $file = Barcode::draw('code39', 'image', $barcodeConf, $renderConf);

            $fileName = $folder . '/' . $code . '.png';

            $store_image = imagepng($file, $fileName);
        }
    }
}