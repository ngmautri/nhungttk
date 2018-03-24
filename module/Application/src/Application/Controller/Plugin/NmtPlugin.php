<?php
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Doctrine\ORM\EntityManager;

/**
 *
 * @author nmt
 *        
 */
class NmtPlugin extends AbstractPlugin
{

    protected $doctrineEM;

    /**
     *
     * @param unknown $o1
     * @param unknown $o2
     * @return NULL|string[][]|NULL[][]|mixed[][]|unknown[][]
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
                    // var_dump($v1);
                    
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
        
        return $diffArray;
    }

    /**
     *
     * @var \Doctrine\ORM\EntityManager $doctrineEM ;
     *      $doctrineEM = $this->NmtPlugin()->doctrineEM();
     * @return unknown
     */
    public function doctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}
