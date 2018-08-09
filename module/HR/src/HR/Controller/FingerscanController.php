<?php

namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Zend\Http\Headers;
use Zend\Validator\Date;
use Zend\Math\Rand;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use Application\Entity\NmtHrLeaveReason;
use Application\Entity\NmtHrFingerscan;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */

class FingerscanController extends AbstractActionController
{

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $errors = array();
            // $redirectUrl = $request->getPost('redirectUrl');
            
            $leaveReason = $request->getPost('leaveReason');
            $leaveReasonLocal = $request->getPost('leaveReasonLocal');
            $legalReference = $request->getPost('legalReference');
            $description = $request->getPost('description');
            // $condition = $request->getPost('condition');
            $isActive = (int) $request->getPost('isActive');
            
            if ($isActive !== 1) {
                $isActive = 0;
            }
            
            if ($leaveReason == null) {
                $errors[] = 'Please enter leave reason!';
            }
            
            if ($leaveReasonLocal == null) {
                $errors[] = 'Please enter leave reason in local language!';
            }
            
            $entity = new NmtHrLeaveReason();
            $entity->setLeaveReasonLocal($leaveReasonLocal);
            $entity->setLeaveReason($leaveReason);
            $entity->setLegalReference($legalReference);
            $entity->setDescription($description);
            // $entity->setCondition($condition);
            $entity->setIsActive($isActive);
            
            if (count($errors) > 0) {
                
                return new ViewModel(array(
                    'redirectUrl' => null,
                    'errors' => $errors,
                    'entity' => $entity
                ));
            }
            
            // NO ERROR
            
            $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $entity->setCreatedBy($u);
            $entity->setCreatedOn(new \DateTime());
            
            $redirectUrl = "/hr/leave-reason/list";
            
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            $this->flashMessenger()->addMessage("Leave Reason '" . $entity->getLeaveReason() . "' has been created!");
            return $this->redirect()->toUrl($redirectUrl);
        }
        
        // NO POST
        $redirectUrl = null;
        $entity = new NmtHrLeaveReason();
        $entity->setIsActive(1);
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity
        ));
    }

    /**
     * php.ini
     * memory_limit=512M
     * 
     * import fingerscan data
     */
    public function importAction()
    {
        
        // take long time
        set_time_limit ( 2500 );
        
        // 1. import excel file
        
        // 2. check file format
        
        // 3. processing file
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $errors = array();
            
            if (isset($_FILES['uploaded_file'])) {
                $file_name = $_FILES['uploaded_file']['name'];
                $file_size = $_FILES['uploaded_file']['size'];
                $file_tmp = $_FILES['uploaded_file']['tmp_name'];
                $file_type = $_FILES['uploaded_file']['type'];
                
                $file_ext = strtolower(end(explode('.', $file_name)));
                
                // attachement required?
                if ($file_tmp == "" or $file_tmp === null) {
                    
                    $errors[] = 'Attachment can\'t be empty!';
                    $this->flashMessenger()->addMessage('Something wrong!');
                    return new ViewModel(array(
                        'errors' => $errors
                    ));
                } else {
                    
                    $ext = '';
                    if (preg_match('/(jpg|jpeg)$/', $file_type)) {
                        $ext = 'jpg';
                        $isPicture = 1;
                    } else if (preg_match('/(gif)$/', $file_type)) {
                        $ext = 'gif';
                        $isPicture = 1;
                    } else if (preg_match('/(png)$/', $file_type)) {
                        $ext = 'png';
                        $isPicture = 1;
                    } else if (preg_match('/(pdf)$/', $file_type)) {
                        $ext = 'pdf';
                    } else if (preg_match('/(vnd.ms-excel)$/', $file_type)) {
                        $ext = 'xls';
                    } else if (preg_match('/(vnd.openxmlformats-officedocument.spreadsheetml.sheet)$/', $file_type)) {
                        $ext = 'xlsx';
                    } else if (preg_match('/(msword)$/', $file_type)) {
                        $ext = 'doc';
                    } else if (preg_match('/(vnd.openxmlformats-officedocument.wordprocessingml.document)$/', $file_type)) {
                        $ext = 'docx';
                    } else if (preg_match('/(x-zip-compressed)$/', $file_type)) {
                        $ext = 'zip';
                    } else if (preg_match('/(octet-stream)$/', $file_type)) {
                        $ext = $file_ext;
                    }
                    
                    $expensions = array(
                        "xlsx",
                        "xls",
                        "csv"
                    );
                    
                    if (in_array($ext, $expensions) === false) {
                        $errors[] = 'Extension file"' . $ext . '" not supported, please choose a "xlsx","xlx", "csv"!';
                    }
                    
                    if ($file_size > 2097152) {
                        $errors[] = 'File size must be  2 MB';
                    }
                    
                    if (count($errors) > 0) {
                        $this->flashMessenger()->addMessage('Something wrong!');
                        return new ViewModel(array(
                            'errors' => $errors
                        ));
                    }
                    ;
                    
                    $folder = ROOT . "/data/hr/fingerscan";
                    
                    if (! is_dir($folder)) {
                        mkdir($folder, 0777, true); // important
                    }
                    
                    // echo ("$folder/$name");
                    move_uploaded_file($file_tmp, "$folder/$file_name");
                    
                    $objPHPExcel = IOFactory::load("$folder/$file_name");
                    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                        //echo $worksheet->getTitle();
                        
                        $worksheetTitle = $worksheet->getTitle();
                        $highestRow = $worksheet->getHighestRow(); // e.g. 10
                        $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                        $nrColumns = ord($highestColumn) - 64;
                        // echo $worksheetTitle;
                        // echo $highestRow;
                        // echo $highestColumn;
                        
                        for ($row = 2; $row <= $highestRow; ++ $row) {
                            
                            $entity = new NmtHrFingerscan();
                            
                            // new A=1
                            for ($col = 1; $col < $highestColumnIndex; ++ $col) {
                                
                                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                                $val = $cell->getValue();
                                //echo $val . ';';
                                 
                                $entity->setEmployeeId(1);
                                $entity->setCreatedBy($u);
                                $entity->setCreatedOn(new \Datetime());
                                
                                if ($col == 1) {
                                    $entity->setEmployeeCode($val);
                                   //echo $val . ' code';
                                }
                                
                                if ($col == 3) {
                                    
                                    $PHPTimeStamp = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);
                                    //var_dump($PHPTimeStamp);
                                   //$dt = date('Y-m-d', $PHPTimeStamp);
                                  // $dt = new \Datetime();
                                   //$dt->setTimestamp ($PHPTimeStamp);
                                    
                                    $entity->setAttendanceDate($PHPTimeStamp);
                                    //echo date('Y-m-d', $PHPTimeStamp) . ' date';
                                    //echo $PHPTimeStamp->format("m-d-Y");
                                }
                                
                                if ($col == 4) {
                                    $entity->setClockIn($val);
                                }
                                
                                if ($col == 5) {
                                   $entity->setClockOut($val);
                                }
                            }
                            
                            
                            $this->doctrineEM->persist($entity);
                            
                            if($row % 100==0 OR $row == $highestRow){
                                $this->doctrineEM->flush();
                            }
                            
                            //echo "<br>";
                        }
                    }
                    
                    $m = sprintf("[OK] %s uploaded !", $file_name);
                    $this->flashMessenger()->addMessage($m);
                    // return $this->redirect()->toUrl($redirectUrl);
                }
            }
        }
        
        // ==================
        // NO POST
        
        $redirectUrl = null;
        if ($this->getRequest()->getHeader('Referer') !== null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }
        
        $id = (int) $this->params()->fromQuery('period_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );
        
        // $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);
        
        return new ViewModel(array(
            'redirectUrl' => $redirectUrl,
            'errors' => null
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();
        $redirectUrl = null;
        
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }
        
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtHrLeaveReason $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrLeaveReason')->findOneBy($criteria);
        
        if (! $entity == null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function show1Action()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        /*
         * if (! $request->isXmlHttpRequest()) {
         * return $this->redirect()->toRoute('access_denied');
         * }
         * ;
         */
        $this->layout("layout/user/ajax");
        
        // $entity_id = (int) $this->params()->fromQuery('entity_id');
        // $token = $this->params()->fromQuery('token');
        $criteria = array(
            'employeeCode' => 2211
        );
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrFingerscan')->findBy($criteria);
        
        return new ViewModel(array(
            'list' => $list
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            
            $errors = array();
            $redirectUrl = $request->getPost('redirectUrl');
            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            
            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );
            
            /**@var \Application\Entity\NmtHrLeaveReason $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrLeaveReason')->findOneBy($criteria);
            
            if ($entity == null) {
                
                $errors[] = 'Entity object can\'t be empty!';
                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null
                ));
                
                // might need redirect
            } else {
                
                $errors = array();
                // $redirectUrl = $request->getPost('redirectUrl');
                
                $leaveReason = $request->getPost('leaveReason');
                $leaveReasonLocal = $request->getPost('leaveReasonLocal');
                $legalReference = $request->getPost('legalReference');
                $description = $request->getPost('description');
                // $condition = $request->getPost('condition');
                $isActive = (int) $request->getPost('isActive');
                
                if ($isActive !== 1) {
                    $isActive = 0;
                }
                
                if ($leaveReason == null) {
                    $errors[] = 'Please enter leave reason!';
                }
                
                if ($leaveReasonLocal == null) {
                    $errors[] = 'Please enter leave reason in local language!';
                }
                
                if ($isActive !== 1) {
                    $isActive = 0;
                }
                
                $entity->setLeaveReasonLocal($leaveReasonLocal);
                $entity->setLeaveReason($leaveReason);
                $entity->setLegalReference($legalReference);
                $entity->setDescription($description);
                // $entity->setCondition($condition);
                $entity->setIsActive($isActive);
                
                if (count($errors) > 0) {
                    
                    return new ViewModel(array(
                        'redirectUrl' => null,
                        'errors' => $errors,
                        'entity' => $entity
                    ));
                }
                
                // NO ERROR
                
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
                
                $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                    "email" => $this->identity()
                ));
                
                $entity->setLastchangeBy($u);
                $entity->setLastchangeOn(new \DateTime());
                
                $redirectUrl = "/hr/leave-reason/list";
                
                $this->doctrineEM->persist($entity);
                $this->doctrineEM->flush();
                $this->flashMessenger()->addMessage("Leave Reason '" . $entity->getLeaveReason() . "' has been updated!");
                return $this->redirect()->toUrl($redirectUrl);
            }
        }
        
        $redirectUrl = null;
        
        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }
        
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );
        
        /**@var \Application\Entity\NmtHrLeaveReason $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtHrLeaveReason')->findOneBy($criteria);
        
        if (! $entity == null) {
            
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $criteria = array();
        
        // var_dump($criteria);
        
        $sort_criteria = array();
        
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }
        ;
        
        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        ;
        
        /**@var \Application\Repository\NmtHrFingerscanRepository $res ;  */
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtHrFingerscan');
        $list = $res->getFingerscan(null, null, 7, 2017);
        // var_dump($list);
        $total_records = count($list);
        $paginator = null;
        
        /*
         * if ($total_records > $resultsPerPage) {
         * $paginator = new Paginator($total_records, $page, $resultsPerPage);
         * $list = $this->doctrineEM->getRepository('Application\Entity\NmtHrLeaveReason')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
         * }
         */
        
        // $all = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->getAllItem();
        // var_dump (count($all));
        
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }

    /**
     * Return attachment of a target
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();
        
        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }
        ;
        
        $this->layout("layout/user/ajax");
        
        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );
        
        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);
        
        if ($target !== null) {
            
            /**
             *
             * @todo : Change Target
             */
            $criteria = array(
                'employee' => $target_id,
                'isActive' => 1,
                'markedForDeletion' => 0
            );
            
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findBy($criteria);
            $total_records = count($list);
            $paginator = null;
            
            return new ViewModel(array(
                'list' => $list,
                'total_records' => $total_records,
                'paginator' => $paginator,
                'target' => $target
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        
        /**
         *
         * @todo : update target
         */
        $query = 'SELECT e FROM Application\Entity\NmtApplicationAttachment e WHERE e.employee > :n';
        
        $list = $this->doctrineEM->createQuery($query)
            ->setParameter('n', 0)
            ->getResult();
        
        if (count($list) > 0) {
            foreach ($list as $entity) {
                /**
                 *
                 * @todo Update Targnet
                 */
                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            }
        }
        
        $this->doctrineEM->flush();
        
        $total_records = count($list);
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    
    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}
