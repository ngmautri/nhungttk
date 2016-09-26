<?php

namespace Application\Service;

use Zend\Permissions\Acl\Acl;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\ControllerManager;
use MLA\Service\AbstractService;
use \PHPExcel;
use \PHPExcel_Writer_Excel2007;

/*
 * @author nmt
 *
 */
class ExcelService extends AbstractService {
	protected $moduleManager;
	protected $controllerManager;
	
	public function initAcl(Acl $acl) {
		// TODO
	}
	
	public function test() {
	// Create new PHPExcel object
	echo date('H:i:s') . " Create new PHPExcel object\n";
	
	include_once '\vendor\PHPExcel_1.8.0_doc\Classes\PHPExcel.php';
	$objPHPExcel = new \PHPExcel();
	
	// Set properties
	echo date('H:i:s') . " Set properties\n";
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
	$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
	$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
	
	
	// Add some data
	echo date('H:i:s') . " Add some data\n";
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Hello');
	$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'world!');
	$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Hello');
	$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'world!');
	
	// Rename sheet
	echo date('H:i:s') . " Rename sheet\n";
	$objPHPExcel->getActiveSheet()->setTitle('Simple');
	
	
	// Save Excel 2007 file
	include_once '\vendor\PHPExcel_1.8.0_doc\Classes\PHPExcel\Writer\Excel2007.php';
		echo date('H:i:s') . " Write to Excel2007 format\n";
	$objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
	
	// Echo done
	echo date('H:i:s') . " Done writing file.\r\n";
	}
}