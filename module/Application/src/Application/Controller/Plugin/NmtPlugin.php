<?php
namespace Application\Controller\Plugin;

use Application\Entity\NmtInventoryItemPicture;
use Doctrine\ORM\EntityManager;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

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

    public function getItemPic1($id)
    {

        /** @var \Application\Entity\NmtInventoryItemPicture $pic ;*/
        $pic = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy(array(
            'item' => $id,
            'isActive' => 1
        ));

        if ($pic instanceof NmtInventoryItemPicture) {

            return true;
        }

        return false;
    }

    public function getItemPic($id)
    {

        /** @var \Application\Entity\NmtInventoryItemPicture $pic ;*/
        $pic = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy(array(
            'item' => $id,
            'isActive' => 1
        ));

        $thumbnail_file = '/images/no-pic1.jpg';
        if ($pic instanceof NmtInventoryItemPicture) {

            $thumbnail_file = "/thumbnail/item/" . $pic->getFolderRelative() . "thumbnail_200_" . $pic->getFileName();
            $thumbnail_file = str_replace('\\', '/', $thumbnail_file); // Important for UBUNTU

            return $thumbnail_file;
        }

        return $thumbnail_file;
    }

    public function getBigItemPic($id)
    {

        /** @var \Application\Entity\NmtInventoryItemPicture $pic ;*/
        $pic = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy(array(
            'item' => $id,
            'isActive' => 1
        ));

        $thumbnail_file = '/images/no-pic1.jpg';
        if ($pic instanceof NmtInventoryItemPicture) {

            $thumbnail_file = "../../data/inventory/item/pictures/" . $pic->getFolderRelative() . "" . $pic->getFileName();
            $thumbnail_file = str_replace('\\', '/', $thumbnail_file); // Important for UBUNTU

            return $thumbnail_file;
        }

        return $thumbnail_file;
    }

    /**
     * Return User List
     *
     * @return array
     */
    public function getUserList()
    {
        $criteria = array(
            'block' => 0
        );

        $sort_criteria = array();

        $list = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findBy($criteria, $sort_criteria);
        return $list;
    }

    /**
     * to check, if user1 is parent of user2
     *
     * @param \Application\Entity\MlaUsers $user1
     * @param \Application\Entity\MlaUsers $user2
     */
    public function isParent(\Application\Entity\MlaUsers $user1, \Application\Entity\MlaUsers $user2)
    {
        $result = array();

        if (! $user1 instanceof \Application\Entity\MlaUsers or ! $user2 instanceof \Application\Entity\MlaUsers) {

            $result['result'] = 0;
            $result['message'] = ' User not found';
            return $result;
        }

        if ($user1 === $user2) {
            $result['result'] = 1;
            $result['message'] = 'Owner operation';
            return $result;
        }

        /**@var \Application\Repository\MlaUsersRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\MlaUsers');
        $isAdmin = $res->isAdministrator($user1);

        if ($isAdmin == true) {
            $result['result'] = 1;
            $result['message'] = ' User is administrator.';
            return $result;
        }

        // Get of User 1
        $criteria = array(
            'user' => $user1
        );

        /**@var \Application\Entity\NmtApplicationAclUserRole $role1 ;*/
        $role1 = $this->doctrineEM->getRepository('\Application\Entity\NmtApplicationAclUserRole')->findOneBy($criteria);

        if ($role1 == null) {
            $result['result'] = 0;
            $result['message'] = ' User not found';
            return $result;
        }

        // Get of User 2
        $criteria = array(
            'user' => $user2
        );
        /**@var \Application\Entity\NmtApplicationAclUserRole $role2 ;*/
        $role2 = $this->doctrineEM->getRepository('\Application\Entity\NmtApplicationAclUserRole')->findOneBy($criteria);
        if ($role2 == null) {
            $result['result'] = 0;
            $result['message'] = ' User not found';
            return $result;
        }

        $path_array = explode("/", $role2->getRole()->getPath());
        $role_level = array();
        $test = '';

        if (count($path_array) > 0) {
            $level = 0;
            foreach ($path_array as $a) {
                $level ++;
                $tmp = array(
                    $a => $level
                );
                $role_level[] = $tmp;

                $test = $test . '/' . $a;
            }
        }

        $ck_level = $this->_isParent($role1->getRole()
            ->getId(), $role_level);

        if ($ck_level > 0 and $ck_level <= $role2->getRole()->getPath()) {
            $result['result'] = 1;
            $result['message'] = $role1->getRole()->getId() . ' allowed ' . $test;
        } else {

            $result['result'] = 0;
            $result['message'] = $role1->getRole()->getId() . ' not allowed ' . $test;
        }

        return $result;
    }

    /**
     *
     * @param int $roleId
     * @param array $roles
     * @return int;
     */
    private function _isParent($roleId, array $role_level)
    {
        if (count($role_level) == 0) {
            return - 1;
        }

        foreach ($role_level as $l) {

            foreach ($l as $k => $v) {

                if ($k == $roleId) {
                    return $v;
                }
            }
        }
        return - 1;
    }

    /**
     * Return List of Currency
     *
     * @return array
     */
    public function getPaymentTerms()
    {
        $criteria = array(
            'isActive' => 1
        );

        $sort_criteria = array();

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationPmtTerm')->findBy($criteria, $sort_criteria);
        return $list;
    }

    /**
     * Return List of Currency
     *
     * @return array
     */
    public function getPmtMethodList()
    {
        $criteria = array();
        $sort_criteria = array();

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationPmtMethod')->findBy($criteria, $sort_criteria);
        return $list;
    }

    /**
     * Return List of Currency
     *
     * @return array
     */
    public function incotermList()
    {
        $criteria = array();
        $sort_criteria = array(
            'incoterm' => 'ASC'
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationIncoterms')->findBy($criteria, $sort_criteria);
        return $list;
    }

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
     * Return List of Currency
     *
     * @return array
     */
    public function warehouseList()
    {
        $criteria = array();
        $sort_criteria = array(
            'whCode' => 'ASC'
        );

        $wh_list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->findBy($criteria, $sort_criteria);
        return $wh_list;
    }

    /**
     * Return List of Currency
     *
     * @return array
     */
    public function costCenterList()
    {
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'costCenterName' => 'ASC'
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\FinCostCenter')->findBy($criteria, $sort_criteria);
        return $list;
    }

    public function associationList()
    {
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'associationName' => 'ASC'
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryAssociation')->findBy($criteria, $sort_criteria);
        return $list;
    }

    /**
     * Return List of Currency
     *
     * @return array
     */
    public function itemGroupList()
    {
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'groupName' => 'ASC'
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemGroup')->findBy($criteria, $sort_criteria);
        return $list;
    }

    /**
     * Return List of Currency
     *
     * @return array
     */
    public function uomList()
    {
        $criteria = array(
            // 'isActive' => 1
        );
        $sort_criteria = array(
            'uomCode' => 'ASC'
        );

        $list = $this->doctrineEM->getRepository('\Application\Entity\NmtApplicationUom')->findBy($criteria, $sort_criteria);
        return $list;
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

    public function departmentList()
    {
        $criteria = array(
            'nodeParentId' => 1
        );
        $sort_criteria = array(
            'nodeName' => 'ASC'
        );
        $wh_list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDepartment')->findBy($criteria, $sort_criteria);
        return $wh_list;
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
     * @param object $entity
     * @return string|NULL
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

            $this->doctrineEM->persist($docNumber);
            $currentDoc = $currentDoc . $tmp . $current_no;
            return $currentDoc;
        }

        return null;
    }

    /**
     *
     * @param string $text
     * @return string
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
     *
     * @return mixed
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     *
     * @param mixed $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     *
     * @return mixed
     */
    public function getDbConfig()
    {
        return $this->dbConfig;
    }

    /**
     *
     * @param mixed $dbConfig
     */
    public function setDbConfig($dbConfig)
    {
        $this->dbConfig = $dbConfig;
    }

    /**
     *
     * @return \Zend\Mail\Transport\Smtp
     */
    public function getStmpOutlook()
    {
        return $this->stmpOutlook;
    }

    /**
     *
     * @param SmtpTransport $stmpOutlook
     */
    public function setStmpOutlook(SmtpTransport $stmpOutlook)
    {
        $this->stmpOutlook = $stmpOutlook;
    }
}
