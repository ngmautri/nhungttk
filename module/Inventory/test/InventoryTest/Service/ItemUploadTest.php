    <?php
    namespace InventoryTest\Service;

    use Doctrine\ORM\EntityManager;
    use InventoryTest\Bootstrap;
    use Inventory\Application\Service\Upload\Item\UploadItem;
    use PHPUnit_Framework_TestCase;

    class ItemUploadTest extends PHPUnit_Framework_TestCase
    {

        protected $serviceManager;

        /**
         *
         * @var EntityManager $em;
         */
        protected $em;

        public function setUp()
        {}

        public function testOther()
        {
            $root = realpath(dirname(dirname(dirname(__FILE__))));

            /**
             *
             * @var UploadItem $uploader
             */
            $uploader = Bootstrap::getServiceManager()->get(UploadItem::class);
            $uploader->setCompanyId(1);
            $uploader->setUserId(39);
            $file = $root . "/InventoryTest/Data/ItemUpload.xlsx";

            $uploader->run($file);
        }
    }