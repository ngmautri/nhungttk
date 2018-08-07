<?php
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Doctrine\ORM\EntityManager;
use Zend\Mail\Transport\Smtp as SmtpTransport;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class NmtPlugin extends AbstractPlugin
{

    protected $doctrineEM;
    protected $translator;
    protected $dbConfig;
    protected $stmpOutlook;

    /**
     * Return List of Currency
     *
     * @return array
     */
    public function currencyList()
    {
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );
        
        $currency_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
        return $currency_list;
    }
    
    
    /**
     * Return List of GL Account
     *
     * @return array
     */
    public function glAccountList()
    {
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'accountNumber' => 'ASC'
        );
        
        $currency_list = $this->doctrineEM->getRepository('Application\Entity\FinAccount')->findBy($criteria, $sort_criteria);
        return $currency_list;
    }

    /**
     * Return List of Country
     *
     * @return array
     */
    public function countryList()
    {
        $criteria = array(
            "isActive" => 1
        );
        $sort_criteria = array(
            "countryName" => "ASC"
        );
        
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->findBy($criteria, $sort_criteria);
        return $list;
    }

    /**
     *
     * @param object $o1
     * @param object $o2
     * @return array
     */
    public function objectsAreIdentical($o1, $o2)
    {
        $diffArray = array();
        
        if (get_class($o1) !== get_class($o2)) {
            return null;
        }
        
        // Now do strict(er) comparison.
        $objReflection1 = new \ReflectionObject($o1);
        $objReflection2 = new \ReflectionObject($o2);
        
        $arrProperties1 = $objReflection1->getProperties();
        
        foreach ($arrProperties1 as $p1) {
            if ($p1->isStatic()) {
                continue;
            }
            $key = sprintf('%s::%s', $p1->getDeclaringClass()->getName(), $p1->getName());
            
            // echo $key . "\n";
            $p1->setAccessible(true);
            
            $v1 = $p1->getValue($o1);
            $p2 = $objReflection2->getProperty($p1->getName());
            
            $p2->setAccessible(true);
            $v2 = $p2->getValue($o2);
            
            // Compare $v1 and $v2
            if ($v1 == null && $v2 == null) {
                continue;
            }
            
            if ($v1 == null) {
                // +++++ $v1 == null && $v2 != null +++++++
                
                if (! is_object($v2)) {
                    
                    $diffArray[$key] = array(
                        "className" => $p2->getDeclaringClass()->getName(),
                        "fieldName" => $p2->getName(),
                        "fieldType" => gettype($v2),
                        "oldValue" => $v1,
                        "newValue" => $v2
                    );
                } else {
                    
                    if ($v2 instanceof \Datetime) {
                        
                        $diffArray[$key] = array(
                            "className" => $p2->getDeclaringClass()->getName(),
                            "fieldName" => $p2->getName(),
                            "fieldType" => gettype($v2),
                            "oldValue" => null,
                            "newValue" => $v2->format("Y-m-d H:i:s")
                        );
                    } else {
                        
                        try {
                            
                            // to handle the proxie object in doctrine
                            // $className2 = $this->doctrineEM->getClassMetadata(get_class($v2));
                            
                            $objV2_1 = new \ReflectionObject($v2);
                            $objV2 = $objV2_1->getParentClass();
                            
                            if ($objV2 != null) {
                                $p12 = $objV2->getProperty("id");
                                $p12->setAccessible(true);
                                $v12 = $p12->getValue($v2);
                            } else {
                                $p12 = $objV2_1->getProperty("id");
                                $p12->setAccessible(true);
                                $v12 = $p12->getValue($v2);
                            }
                            
                            if (null != $v12) {
                                $diffArray[$key] = array(
                                    "className" => $p2->getDeclaringClass()->getName(),
                                    "fieldName" => $p1->getName(),
                                    "fieldType" => gettype($v2),
                                    "oldValue" => null,
                                    "newValue" => $v12
                                );
                            }
                        } catch (\Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                }
            } else {
                // +++++ $v1 != null +++++++
                
                if ($v2 == null) {
                    // +++++ $v1 != null && $v2 == null +++++++
                    
                    if (! is_object($v1)) {
                        
                        $diffArray[$key] = array(
                            "className" => $p1->getDeclaringClass()->getName(),
                            "fieldName" => $p1->getName(),
                            "fieldType" => gettype($v1),
                            "oldValue" => $v1,
                            "newValue" => null
                        );
                    } else {
                        
                        if ($v1 instanceof \Datetime) {
                            
                            $diffArray[$key] = array(
                                "className" => $p1->getDeclaringClass()->getName(),
                                "fieldName" => $p1->getName(),
                                "fieldType" => gettype($v1),
                                "oldValue" => $v1->format("Y-m-d H:i:s"),
                                "newValue" => null
                            );
                        } else {
                            
                            try {
                                
                                // to handle the proxie object
                                // $className1 = $this->doctrineEM->getClassMetadata(get_class($v1));
                                $objV1_1 = new \ReflectionObject($v1);
                                $objV1 = $objV1_1->getParentClass();
                                
                                if ($objV1 != null) {
                                    $p11 = $objV1->getProperty("id");
                                    $p11->setAccessible(true);
                                    $v11 = $p11->getValue($v1);
                                } else {
                                    $p11 = $objV1_1->getProperty("id");
                                    $p11->setAccessible(true);
                                    $v11 = $p11->getValue($v1);
                                }
                                
                                if ($v11 != null) {
                                    $diffArray[$key] = array(
                                        "className" => $p1->getDeclaringClass()->getName(),
                                        "fieldName" => $p1->getName(),
                                        "fieldType" => gettype($v1),
                                        "oldValue" => $v11,
                                        "newValue" => null
                                    );
                                }
                            } catch (\Exception $e) {
                                echo $e->getMessage();
                            }
                        }
                    }
                } else {
                    // ========= $v2, $v2 !==null
                    
                    if (! is_object($v1)) {
                        
                        if ($v1 != $v2) {
                            $diffArray[$key] = array(
                                "className" => $p1->getDeclaringClass()->getName(),
                                "fieldName" => $p1->getName(),
                                "fieldType" => gettype($v1),
                                "oldValue" => $v1,
                                "newValue" => $v2
                            );
                        }
                    } else {
                        
                        if ($v1 instanceof \Datetime) {
                            
                            if ($v1->format("Y-m-d H:i:s") != $v2->format("Y-m-d H:i:s"))
                                $diffArray[$key] = array(
                                    "className" => $p1->getDeclaringClass()->getName(),
                                    "fieldName" => $p1->getName(),
                                    "fieldType" => gettype($v1),
                                    "oldValue" => $v1->format("Y-m-d H:i:s"),
                                    "newValue" => $v2->format("Y-m-d H:i:s")
                                );
                        } else {
                            
                            try {
                                
                                // to handle the proxie object
                                // $className1 = $this->doctrineEM->getClassMetadata(get_class($v1));
                                
                                $objV1_1 = new \ReflectionObject($v1);
                                $objV1 = $objV1_1->getParentClass();
                                
                                if ($objV1 != null) {
                                    $p11 = $objV1->getProperty("id");
                                    $p11->setAccessible(true);
                                    $v11 = $p11->getValue($v1);
                                } else {
                                    $p11 = $objV1_1->getProperty("id");
                                    $p11->setAccessible(true);
                                    $v11 = $p11->getValue($v1);
                                }
                                
                                // to handle the proxie object in doctrine
                                // $className2 = $this->doctrineEM->getClassMetadata(get_class($v2));
                                
                                $objV2_1 = new \ReflectionObject($v2);
                                $objV2 = $objV2_1->getParentClass();
                                
                                if ($objV2 != null) {
                                    $p12 = $objV2->getProperty("id");
                                    $p12->setAccessible(true);
                                    $v12 = $p12->getValue($v2);
                                } else {
                                    $p12 = $objV2_1->getProperty("id");
                                    $p12->setAccessible(true);
                                    $v12 = $p12->getValue($v2);
                                }
                                
                                if ($v11 != $v12) {
                                    var_dump($v11);
                                    $diffArray[$key] = array(
                                        "className" => $p1->getDeclaringClass()->getName(),
                                        "fieldName" => $p1->getName(),
                                        "fieldType" => gettype($v1),
                                        "oldValue" => $v11,
                                        "newValue" => $v12
                                    );
                                }
                            } catch (\Exception $e) {
                                echo $e->getMessage();
                            }
                        }
                    }
                }
            }
        }
        
        return $diffArray;
    }

   /**
    * 
    *  @param object $entity
    *  @return string|NULL
    */
    public function getDocNumber($entity)
    {
        $criteria = array(
            'isActive' => 1,
            'subjectClass' => get_class($entity)
        );
        
        /** @var \Application\Entity\NmtApplicationDocNumber $docNumber ; */
        $docNumber = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDocNumber')->findOneBy($criteria);
        
        if ($docNumber instanceof \Application\Entity\NmtApplicationDocNumber) {
            
            $maxLen = strlen($docNumber->getToNumber());
            $currentLen = 1;
            $currentDoc = $docNumber->getPrefix();
            $current_no = $docNumber->getCurrentNumber();
            
            if ($current_no == null) {
                $current_no = $docNumber->getFromNumber();
            } else {
                $current_no ++;
                $currentLen = strlen($current_no);
            }
            
            $docNumber->setCurrentNumber($current_no);
            
            $tmp = "";
            for ($i = 0; $i < $maxLen - $currentLen; $i ++) {
                
                $tmp = $tmp . "0";
            }
            
            $currentDoc = $currentDoc . $tmp . $current_no;
            return $currentDoc;
        }
        
        return null;
    }
    
    /**
     * 
     *  @param string $text
     *  @return string
     */
    public function translate($text)
    {
        return $this->translator->translate($text);
    }

    /**
     *
     * @var \Doctrine\ORM\EntityManager $doctrineEM ;
     *      $doctrineEM = $this->NmtPlugin()->doctrineEM();
     * @return \Doctrine\ORM\EntityManager
     */
    public function doctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
    /**
     * @return mixed
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param mixed $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }
    /**
     * @return mixed
     */
    public function getDbConfig()
    {
        return $this->dbConfig;
    }

    /**
     * @param mixed $dbConfig
     */
    public function setDbConfig($dbConfig)
    {
        $this->dbConfig = $dbConfig;
    }
    
   /**
    * 
    *  @return \Zend\Mail\Transport\Smtp
    */
    public function getStmpOutlook()
    {
        return $this->stmpOutlook;
    }

   /**
   * 
   *  @param SmtpTransport $stmpOutlook
   */
    public function setStmpOutlook(SmtpTransport $stmpOutlook)
    {
        $this->stmpOutlook = $stmpOutlook;
    }



}
