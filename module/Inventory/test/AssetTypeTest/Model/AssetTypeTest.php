<?php
namespace AssetTypeTest\Model;

use Inventory\Model\Evaluation\ItemGr;
use Inventory\Model\Evaluation\FIFO;
use PHPUnit_Framework_TestCase;

class AssetTypeTest extends PHPUnit_Framework_TestCase
{

    /*
     * public function testAssetGroupTest() {
     * $rep = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem');
     * $list = $rep->getAllItemWithSerial1();
     * echo count($list);
     *
     * }
     */
    public function testAssetGroupTest()
    {
        $q1 = new FIFO();

        // Take beginning
        $itemGR = new ItemGr();
        $itemGR->setGrQuantity(10);
        $q1->enqueue($itemGR);

        $itemGR = new ItemGr();
        $itemGR->setGrQuantity(20);
        $q1->enqueue($itemGR);

        $itemGR = new ItemGr();
        $itemGR->setGrQuantity(30);
        $q1->enqueue($itemGR);

        $itemGR = new ItemGr();
        $itemGR->setGrQuantity(40);
        $q1->enqueue($itemGR);

        $issuedQuantity = 100;

        for ($q1->rewind(); $q1->valid(); $q1->next()) {
            try {
                /**@var  ItemGr $value ;*/
                $value = $q1->current();
                $grQuantity = $value->getGrQuantity();

                if ($issuedQuantity == 0) {
                    break;
                }

                if ($grQuantity <= $issuedQuantity) {
                    $value->setIssueQuantity($grQuantity);
                    $value->setRemainingQuantity(0);
                    $issuedQuantity = $issuedQuantity - $grQuantity;
                } else {
                    $value->setIssueQuantity($issuedQuantity);
                    $value->setRemainingQuantity($grQuantity - $issuedQuantity);
                    $issuedQuantity = 0;
                }
            } catch (\Exception $exception) {
                continue;
            }

            // ...
        }

        $n = 0;
        for ($q1->rewind(); $q1->valid(); $q1->next()) {
            try {
                $n ++;

                /**@var  ItemGr $value ;*/
                $value = $q1->current();
                $grQuantity = $value->getGrQuantity();
                echo $n . 'GR=' . $value->getGrQuantity() . '| ';
                echo $n . 'GI=' . $value->getIssueQuantity() . '| ';
                echo $n . 'Remaining=' . $value->getRemainingQuantity() . "\n";
            } catch (\Exception $exception) {
                continue;
            }

            // ...
        }

        echo $issuedQuantity;

        /*
         * print($q1->dequeue()." | ");
         * print($q1->dequeue()." | ");
         * print($q1->dequeue());
         */
    }

    /*
     * public function testAssetSearchServieTest() {
     * $resultSet = new ResultSet ();
     *
     * $sv = Bootstrap::getServiceManager ()->get ( 'Inventory\Services\AssetSearchService' );
     *
     * $sv->createIndex();
     *
     * //$sv->search("00051");
     *
     * }
     *
     */
    /*
     * public function testAssetSearchServieTest() {
     * $resultSet = new ResultSet ();
     * $sv = Bootstrap::getServiceManager ()->get ( 'Inventory\Services\SparePartsSearchService' );
     * //$sv->createIndex();
     *
     * //var_dump($sv->search('bobbin'));
     *
     * $pictures_dir = ROOT . DIRECTORY_SEPARATOR . "/data" . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "asset_22".DIRECTORY_SEPARATOR."pictures".DIRECTORY_SEPARATOR;
     *
     * $name='0002.png';
     *
     * var_dump($pictures_dir.$name);
     *
     * $im = imagecreatefrompng($pictures_dir.$name);
     *
     * $ox = imagesx($im);
     * $oy = imagesy($im);
     *
     * $final_width_of_image =450;
     *
     * $nx = $final_width_of_image;
     * $ny = floor($oy * ($final_width_of_image / $ox));
     *
     * $nm = imagecreatetruecolor($nx, $ny);
     *
     * $name_thumbnail = 'thumbnail_450_'.$name ;
     * var_dump($name_thumbnail);
     *
     * imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
     * imagepng($nm, "$pictures_dir/$name_thumbnail");
     *
     * }
     */

    /*
     * public function testSparepartCatergoryTest() {
     *
     * $tbl = Bootstrap::getServiceManager()->get('Inventory\Model\WarehouseTable');
     *
     * var_dump($tbl->fetchAll());
     * }
     */

    // $resultSet = new ResultSet ();
    // $tbl = Bootstrap::getServiceManager()->get('Inventory\Model\SparepartCategoryMemberTable');

    // $data = new SparepartCategoryMember();
    // $data->sparepart_id = 1;
    // $data->sparepart_cat_id=1;
    // var_dump($tbl->add($data));
    // $result = $tbl->getTotalMembersOfCatID(12332);
    // var_dump($result);

    /*
     * foreach ($result as $user) {
     * echo $user['name'];
     * }
     */

    // $result = $tbl->getMovements('2016-03-01','2016-03-31','OUT');

    // var_dump($result);

    // $s = '22-4-00039';
    // $p = strpos($s, "-",3) +1;

    // var_dump(strpos($s, "-",3));

    // var_dump(substr ($s,$p, strlen($s)-$p)*1);

    /*
     * var_dump($result = $tbl->fetchAll());
     * foreach ($result as $user) {
     * var_dump ($user);
     * }
     */

    /*
     * var_dump($result = $tbl->getArticles(0,0,0));
     * foreach ($result as $user) {
     * var_dump ($user);
     * }
     */

    // $sv = new EpartnerService();
    // var_dump($sv->get());

    // $table->add($assetType);

    // var_dump($tbl->getArticlesOf(39));
    // $tbl->createIndex();
    /*
     * $hits =$tbl->search('boot',2);
     *
     * $data = array();
     *
     * foreach ($hits as $key => $value)
     * {
     * $n = (int)$key;
     * $data[$n]['id'] = $value->article_id;
     * $data[$n]['name'] = $value->name;
     * $data[$n]['department_id'] = $value->department_id;
     * }
     *
     * var_dump($data);
     */
    /*
     *
     * $emailText = <<<EOT
     *
     * <p>Welcome Mascot Laos Plattform!</p>
     *
     * Your account is created.<br/>
     * click on below link to activate your account:
     * <p>
     * Regards<br/>
     * MLA Team
     * </p>
     * <p>(<em>This Email is generated by the system.</em>)</p>
     * EOT;
     *
     * $html = new MimePart($emailText);
     * //$html->type = Mime::TYPE_HTML;
     *
     * $body = new MimeMessage();
     * $body->setParts(array($html));
     *
     * // build message
     * $message = new Message ();
     * $message->addFrom ( 'mib-team@web.de' );
     * $message->addTo ('ngmautri@outlook.com');
     * $message->setSubject ( 'Mascot Laos Plattform Register' );
     *
     * $type = new ContentType();
     * $type->setType('text/html');
     *
     * $message->getHeaders()->addHeader($type);
     * $message->setBody ($emailText);
     *
     * $mailTransport = Bootstrap::getServiceManager ()->get ( 'SmtpTransportService' );
     *
     * // send message
     * $mailTransport->send ( $message );
     * echo $emailText;
     * }
     */
}