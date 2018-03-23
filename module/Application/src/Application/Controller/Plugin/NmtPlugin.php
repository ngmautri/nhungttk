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
   * @return NULL|string[][]|NULL[][]|unknown[][]|mixed[][]
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
            
            //echo $key . "\n";
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
