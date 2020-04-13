<?php
namespace Application\Domain\Shared;

use Application\Notification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractDTO
{

    private $notification;

    /**
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return $this->getNotification()->hasErrors();
    }

    /**
     * 
     * @return array|\Application\multitype:
     */
    public function getErrors()
    {
        return $this->getNotification()->getErrors();
    }

    /**
     *
     * @param string $err
     * @return \Application\Domain\Shared\AbstractDTO
     */
    public function addError($err)
    {
        if ($err == null)
            return $this;

        $notification = $this->getNotification();
        $notification->addError($err);
        $this->notification = $notification;
        return $this;
    }

    /**
     *
     * @param array $errs
     * @return \Application\Domain\Shared\AbstractDTO
     */
    public function addErrorArray(array $errs)
    {
        if (count($errs) == 0) {
            return $this;
        }

        $notification = $this->getNotification();

        foreach ($errs as $err) {
            $notification->addError($err);
        }

        $this->notification = $notification;

        return $this;
    }

    /**
     *
     * @param string $mes
     * @return \Application\Domain\Shared\AbstractDTO
     */
    public function addWaring($mes)
    {
        if ($mes == null)
            return $this;

        $notification = $this->getNotification();
        $this->notification = $notification->addWarning($mes);
        return $this;
    }

    /**
     *
     * @param string $mes
     * @return \Application\Domain\Shared\AbstractDTO
     */
    public function addSuccess($mes)
    {
        if ($mes == null)
            return $this;

        $notification = $this->getNotification();
        $this->notification = $notification->addSuccess($mes);
        return $this;
    }

    /**
     *
     * @return \Application\Notification
     */
    public function getNotification()
    {
        if (! $this->notification instanceof Notification) {
            return new Notification();
        }

        return $this->notification;
    }

    /**
     *
     * @param Notification $notification
     */
    public function setNotification(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function compare(AbstractDTO $o2)
    {
        $o1 = $this;
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
}
