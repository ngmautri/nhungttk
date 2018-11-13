<?php
namespace HR\Controller;

use Zend\Barcode\Barcode;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class IndexController extends AbstractActionController
{   
    
    const BACKUP_FOLDER = "/data/hr/employee-code";
    

    /**
     */
    public function barcodeAction()
    {
        // take long time
        set_time_limit(1500);
        
        for ($i =1; $i <4000; $i++){
            
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
            
            $fileName = ROOT . self::BACKUP_FOLDER . '/' . $code. '.png';
             
            $store_image = imagepng($file, $fileName);
         
        }
        
        
    }
}
