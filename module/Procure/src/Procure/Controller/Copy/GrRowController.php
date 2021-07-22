<?php
namespace procure\controller;

use zend\escaper\escaper;
use zend\math\rand;
use zend\mvc\controller\abstractactioncontroller;
use zend\validator\date;
use zend\view\model\viewmodel;
use doctrine\orm\entitymanager;
use Application\Domain\Util\Pagination\Paginator;
use application\entity\nmtprocuregr;
use application\entity\nmtprocuregrrow;
use application\entity\nmtprocurepo;
use application\entity\nmtprocureporow;
use application\entity\nmtinventorytrx;
use phpoffice\phpspreadsheet\spreadsheet;

/**
 * good receipt po or pr or ap
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *        
 */
class grrowcontroller extends abstractactioncontroller
{

    protected $doctrineem;

    protected $grservice;

    /**
     *
     * @return \zend\http\response|\zend\view\model\viewmodel
     */
    public function girdtmpaction()
    {
        $request = $this->getrequest();

        if ($request->getheader('referer') == null) {
            // return $this->redirect()->toroute('access_denied');
        }

        // $pq_curpage = $_get ["pq_curpage"];
        // $pq_rpp = $_get ["pq_rpp"];

        $target_id = (int) $this->params()->fromquery('target_id');
        $token = $this->params()->fromquery('token');
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );
        $target = $this->doctrineem->getrepository('application\entity\nmtprocuregr')->findoneby($criteria);

        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new escaper();

        if ($target instanceof \application\entity\nmtprocuregr) {

            $query = 'select e from application\entity\nmtprocuregrrow e
            where e.gr=?1 and e.isactive =?2 and e.isdraft =?3 order by e.rownumber';

            $list = $this->doctrineem->createquery($query)
                ->setparameters(array(
                "1" => $target,
                "2" => 1,
                "3" => 1
            ))
                ->getresult();

            $total_records = 0;
            if (count($list) > 0) {
                $escaper = new escaper();

                $total_records = count($list);
                foreach ($list as $a) {

                    /** @var \application\entity\nmtprocuregrrow $a ;*/

                    $a_json_row["row_id"] = $a->getid();
                    $a_json_row["row_token"] = $a->gettoken();
                    $a_json_row["row_number"] = $a->getrownumber();
                    $a_json_row["row_unit"] = $a->getunit();
                    $a_json_row["row_quantity"] = $a->getquantity();

                    if ($a->getunitprice() != null) {
                        $a_json_row["row_unit_price"] = number_format($a->getunitprice(), 2);
                    } else {
                        $a_json_row["row_unit_price"] = 0;
                    }

                    if ($a->getnetamount() != null) {
                        $a_json_row["row_net"] = number_format($a->getnetamount(), 2);
                    } else {
                        $a_json_row["row_net"] = 0;
                    }

                    if ($a->gettaxrate() != null) {
                        $a_json_row["row_tax_rate"] = $a->gettaxrate();
                    } else {
                        $a_json_row["row_tax_rate"] = 0;
                    }

                    if ($a->getgrossamount() != null) {
                        $a_json_row["row_gross"] = number_format($a->getgrossamount(), 2);
                    } else {
                        $a_json_row["row_gross"] = 0;
                    }

                    $a_json_row["pr_number"] = "";
                    if ($a->getprrow() != null) {
                        if ($a->getprrow()->getpr() != null) {

                            $link = '<a target="_blank" href="/procure/pr/show?token=' . $a->getprrow()
                                ->getpr()
                                ->gettoken() . '&entity_id=' . $a->getprrow()
                                ->getpr()
                                ->getid() . '&checkum=' . $a->getprrow()
                                ->getpr()
                                ->getchecksum() . '"> ... </a>';

                            $a_json_row["pr_number"] = $a->getprrow()
                                ->getpr()
                                ->getprnumber() . $link;
                        }
                    }

                    $item_detail = "/inventory/item/show1?token=" . $a->getitem()->gettoken() . "&checksum=" . $a->getitem()->getchecksum() . "&entity_id=" . $a->getitem()->getid();
                    if ($a->getitem()->getitemname() !== null) {
                        $onclick = "showjquerydialog('detail of item: " . $escaper->escapejs($a->getitem()
                            ->getitemname()) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
                    } else {
                        $onclick = "showjquerydialog('detail of item: " . ($a->getitem()->getitemname()) . "','1200',$(window).height()-100,'" . $item_detail . "','j_loaded_data', true);";
                    }

                    if (strlen($a->getitem()->getitemname()) < 35) {
                        $a_json_row["item_name"] = $a->getitem()->getitemname() . '<a style="cursor:pointer;color:blue"  item-pic="" id="' . $a->getitem()->getid() . '" item_name="' . $a->getitem()->getitemname() . '" title="' . $a->getitem()->getitemname() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;....&nbsp;&nbsp;</a>';
                    } else {
                        $a_json_row["item_name"] = substr($a->getitem()->getitemname(), 0, 30) . '<a style="cursor:pointer;;color:blue"  item-pic="" id="' . $a->getitem()->getid() . '" item_name="' . $a->getitem()->getitemname() . '" title="' . $a->getitem()->getitemname() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</a>';
                    }

                    // $a_json_row["item_name"] = $a->getitem()->getitemname();

                    $a_json_row["item_sku"] = $a->getitem()->getitemsku();
                    $a_json_row["item_token"] = $a->getitem()->gettoken();
                    $a_json_row["item_checksum"] = $a->getitem()->getchecksum();
                    $a_json_row["fa_remarks"] = $a->getfaremarks();
                    $a_json_row["remarks"] = $a->getremarks();

                    $a_json[] = $a_json_row;
                }
            }

            $a_json_final['data'] = $a_json;
            $a_json_final['totalrecords'] = $total_records;
            // $a_json_final ['curpage'] = $pq_curpage;
        }

        $response = $this->getresponse();
        $response->getheaders()->addheaderline('content-type', 'application/json');
        $response->setcontent(json_encode($a_json_final));
        return $response;
    }

    /**
     *
     * @return \zend\http\response|\zend\view\model\viewmodel
     */
    public function updaterowtmpaction()
    {
        $a_json_final = array();
        $escaper = new escaper();

        /*
         * $pq_curpage = $_get ["pq_curpage"];
         * $pq_rpp = $_get ["pq_rpp"];
         */
        $sent_list = json_decode($_post['sent_list'], true);
        // echo json_encode($sent_list);

        $to_update = $sent_list['updatelist'];
        foreach ($to_update as $a) {
            $criteria = array(
                'id' => $a['row_id'],
                'token' => $a['row_token']
            );

            /** @var \application\entity\finvendorinvoicerowtmp $entity */
            $entity = $this->doctrineem->getrepository('application\entity\finvendorinvoicerowtmp')->findoneby($criteria);

            if ($entity != null) {
                $entity->setfaremarks($a['fa_remarks']);
                $entity->setrownumber($a['row_number']);
                $entity->setquantity($a['row_quantity']);
                $entity->setunitprice($a['row_unit_price']);
                $entity->settaxrate($a['row_tax_rate']);

                $entity->setnetamount($a['row_quantity'] * $entity->getunitprice());
                $entity->settaxamount($entity->getnetamount() * $entity->gettaxrate() / 100);
                $entity->setgrossamount($entity->getnetamount() + $entity->gettaxamount());

                // $a_json_final['updatelist']=$a['row_id'] . 'has been updated';
                $this->doctrineem->persist($entity);
            }
        }
        $this->doctrineem->flush();

        // $a_json_final["updatelist"]= json_encode($sent_list["updatelist"]);

        $response = $this->getresponse();
        $response->getheaders()->addheaderline('content-type', 'application/json');
        $response->setcontent(json_encode($sent_list));
        return $response;
    }

    /**
     *
     * @return \zend\view\model\viewmodel|\zend\http\response
     */
    public function addaction()
    {
        $this->layout("procure/layout-fullscreen");

        /**@var \application\controller\plugin\nmtplugin $nmtplugin ;*/
        $nmtplugin = $this->nmtplugin();
        $currency_list = $nmtplugin->currencylist();
        $gl_list = $nmtplugin->glaccountlist();
        $cost_center_list = $nmtplugin->costcenterlist();

        $request = $this->getrequest();

        // is posting .................
        // ============================
        if ($request->ispost()) {
            $errors = array();
            $redirecturl = $request->getpost('redirecturl');
            $gr_id = $request->getpost('gr_id');
            $gr_token = $request->getpost('gr_token');

            /**@var \application\repository\nmtprocureporepository $res ;*/
            $res = $this->doctrineem->getrepository('application\entity\nmtprocurepo');
            $gr = $res->getgr($gr_id, $gr_token);

            if ($gr == null) {
                return $this->redirect()->toroute('access_denied');
            }

            $target = null;
            if ($gr[0] instanceof nmtprocuregr) {

                /**@var \application\entity\nmtprocurepo $target ;*/
                $target = $gr[0];
            }

            if ($target == null) {

                $errors[] = 'gr object can\'t be empty. or token key is not valid!';
                $viewmodel = new viewmodel(array(
                    'redirecturl' => $redirecturl,
                    'errors' => $errors,
                    'target' => null,
                    'entity' => null,
                    'currency_list' => $currency_list,
                    'cost_center_list' => $cost_center_list,
                    'gl_list' => $gl_list,
                    'total_row' => 0,
                    'max_row_number' => 0,
                    'active_row' => 0
                ));

                $viewmodel->settemplate("procure/gr-row/add-row");
                return $viewmodel;
            }

            $entity = new nmtprocuregrrow();
            $entity->setgr($target);

            // goods receipt, invoice not receipt
            $entity->settransactiontype(\application\model\constants::procure_transaction_type_grni);
            $entity->settransactionstatus(\application\model\constants::procure_transaction_status_pending);

            try {
                $data = $this->params()->frompost();
                $errors = $this->grservice->validaterow($target, $entity, $data);
            } catch (\exception $e) {
                $errors[] = $e->getmessage();
            }

            if (count($errors) > 0) {

                $viewmodel = new viewmodel(array(
                    'redirecturl' => $redirecturl,
                    'errors' => $errors,
                    'target' => $target,
                    'entity' => $entity,
                    'currency_list' => $currency_list,
                    'gl_list' => $gl_list,
                    'cost_center_list' => $cost_center_list,

                    'total_row' => $gr['total_row'],
                    'max_row_number' => $gr['max_row_number'],
                    'active_row' => $gr['active_row']
                ));

                $viewmodel->settemplate("procure/gr-row/add-row");
                return $viewmodel;
            }
            ;

            // no error
            // saving into database..........
            // ++++++++++++++++++++++++++++++

            $u = $this->doctrineem->getrepository('application\entity\mlausers')->findoneby(array(
                'email' => $this->identity()
            ));

            try {
                $data = $this->params()->frompost();
                $errors = $this->grservice->saverow($target, $entity, $u, true);
            } catch (\exception $e) {
                $errors[] = $e->getmessage();
            }

            if (count($errors) > 0) {

                $viewmodel = new viewmodel(array(
                    'redirecturl' => $redirecturl,
                    'errors' => $errors,
                    'target' => $target,
                    'entity' => $entity,
                    'currency_list' => $currency_list,
                    'gl_list' => $gl_list,
                    'cost_center_list' => $cost_center_list,

                    'total_row' => $gr['total_row'],
                    'max_row_number' => $gr['max_row_number'],
                    'active_row' => $gr['active_row']
                ));

                $viewmodel->settemplate("procure/gr-row/add-row");
                return $viewmodel;
            }
            ;

            $redirecturl = "/procure/gr-row/add?token=" . $target->gettoken() . "&target_id=" . $target->getid();
            $m = sprintf("[ok] gr line: %s created!", $entity->getid());
            $this->flashmessenger()->addmessage($m);

            return $this->redirect()->tourl($redirecturl);
        }

        // no post
        // initiate.....................
        // ==============================

        $redirecturl = null;

        if ($request->getheader('referer') == null) {
            return $this->redirect()->toroute('access_denied');
        }

        $redirecturl = $this->getrequest()
            ->getheader('referer')
            ->geturi();

        $id = (int) $this->params()->fromquery('target_id');
        $token = $this->params()->fromquery('token');

        /**@var \application\repository\nmtprocureporepository $res ;*/
        $res = $this->doctrineem->getrepository('application\entity\nmtprocurepo');
        $gr = $res->getgr($id, $token);

        if ($gr == null) {
            return $this->redirect()->toroute('access_denied');
        }

        $target = null;
        if ($gr[0] instanceof nmtprocuregr) {
            $target = $gr[0];
        }

        if ($target == null) {
            return $this->redirect()->toroute('access_denied');
        }

        $entity = new nmtprocuregrrow();

        // set null
        $entity->setisactive(1);
        $entity->setconversionfactor(1);
        $entity->setunit("each");

        $viewmodel = new viewmodel(array(
            'redirecturl' => $redirecturl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'currency_list' => $currency_list,
            'gl_list' => $gl_list,
            'cost_center_list' => $cost_center_list,
            'total_row' => $gr['total_row'],
            'max_row_number' => $gr['max_row_number'],
            'active_row' => $gr['active_row']
        ));

        $viewmodel->settemplate("procure/gr-row/add-row");
        return $viewmodel;
    }

    /**
     *
     * @return \zend\http\response|\zend\view\model\viewmodel
     */
    public function showaction()
    {
        $request = $this->getrequest();

        if ($request->getheader('referer') == null) {
            return $this->redirect()->toroute('access_denied');
        }
        $redirecturl = $this->getrequest()
            ->getheader('referer')
            ->geturi();

        $id = (int) $this->params()->fromquery('entity_id');
        $token = $this->params()->fromquery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $entity = $this->doctrineem->getrepository('application\entity\nmtfinpostingperiod')->findoneby($criteria);
        if ($entity !== null) {
            return new viewmodel(array(
                'redirecturl' => $redirecturl,
                'entity' => $entity,
                'errors' => null
            ));
        } else {
            return $this->redirect()->toroute('access_denied');
        }
    }

    /**
     *
     * @return \zend\http\response|\zend\view\model\viewmodel
     */
    public function processaction()
    {
        $request = $this->getrequest();

        if ($request->getheader('referer') == null) {
            return $this->redirect()->toroute('access_denied');
        }
        $redirecturl = $this->getrequest()
            ->getheader('referer')
            ->geturi();

        $id = (int) $this->params()->fromquery('entity_id');
        $token = $this->params()->fromquery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $entity = $this->doctrineem->getrepository('application\entity\nmtfinpostingperiod')->findoneby($criteria);
        if ($entity !== null) {
            return new viewmodel(array(
                'redirecturl' => $redirecturl,
                'entity' => $entity,
                'errors' => null
            ));
        } else {
            return $this->redirect()->toroute('access_denied');
        }
    }

    /**
     *
     * @return \zend\view\model\viewmodel|\zend\http\response
     */
    public function editaction()
    {
        $this->layout("procure/layout-fullscreen");
        $request = $this->getrequest();

        /**@var \application\controller\plugin\nmtplugin $nmtplugin ;*/
        $nmtplugin = $this->nmtplugin();
        $currency_list = $nmtplugin->currencylist();
        $gl_list = $nmtplugin->glaccountlist();
        $cost_center_list = $nmtplugin->costcenterlist();

        // is posting .................
        // ============================

        if ($request->ispost()) {

            $errors = array();
            $redirecturl = $request->getpost('redirecturl');

            $entity_id = (int) $request->getpost('entity_id');
            $token = $request->getpost('token');
            $ntry = $request->getpost('n');

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /** @var \application\entity\nmtprocuregrrow $entity ; */
            $entity = $this->doctrineem->getrepository('application\entity\nmtprocuregrrow')->findoneby($criteria);

            if ($entity == null) {
                $errors[] = 'entity object can\'t be empty. or token key is not valid!';
                $this->flashmessenger()->addmessage('something wrong!');
                return new viewmodel(array(
                    'redirecturl' => $redirecturl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => null,
                    'currency_list' => $currency_list,
                    'gl_list' => $gl_list,
                    'cost_center_list' => $cost_center_list,
                    'n' => $ntry
                ));
            }

            $oldentity = clone ($entity);

            try {
                $data = $this->params()->frompost();
                $errors = $this->grservice->validaterow($target, $entity, $data);
            } catch (\exception $e) {
                $errors[] = $e->getmessage();
            }

            $changearray = $nmtplugin->objectsareidentical($oldentity, $entity);

            if (count($changearray) == 0) {
                $ntry ++;
                $errors[] = sprintf('nothing changed! n = %s', $ntry);
            }

            if ($ntry >= 3) {
                $errors[] = sprintf('do you really want to edit "ap row. %s"?', $entity->getrowidentifer());
            }

            if ($ntry == 5) {
                $m = sprintf('you might be not ready to edit ap row (%s). please try later!', $entity->getrowidentifer());
                $this->flashmessenger()->addmessage($m);
                return $this->redirect()->tourl($redirecturl);
            }

            if (count($errors) > 0) {

                $viewmodel = new viewmodel(array(
                    'redirecturl' => $redirecturl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $entity->getgr(),
                    'currency_list' => $currency_list,
                    'gl_list' => $gl_list,
                    'cost_center_list' => $cost_center_list,
                    'n' => $ntry
                ));

                return $viewmodel;
            }

            // no error
            // saving into database..........
            // ++++++++++++++++++++++++++++++

            $u = $this->doctrineem->getrepository('application\entity\mlausers')->findoneby(array(
                'email' => $this->identity()
            ));

            try {
                $data = $this->params()->frompost();
                $errors = $this->grservice->saverow($target, $entity, $u);
            } catch (\exception $e) {
                $errors[] = $e->getmessage();
            }

            if (count($errors) > 0) {

                $viewmodel = new viewmodel(array(
                    'redirecturl' => $redirecturl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $entity->getgr(),
                    'currency_list' => $currency_list,
                    'gl_list' => $gl_list,
                    'cost_center_list' => $cost_center_list,
                    'n' => $ntry
                ));

                return $viewmodel;
            }

            $redirecturl = sprintf('/procure/gr/review?token=%s&entity_id=%s', $entity->getgr()->gettoken(), $entity->getgr()->getid());
            $m = sprintf("[ok] gr line: %s created!", $entity->getid());
            $this->flashmessenger()->addmessage($m);

            return $this->redirect()->tourl($redirecturl);
        }

        // no post
        // initiate.....................
        // ==============================
        $redirecturl = null;
        if ($this->getrequest()->getheader('referer') !== null) {
            $redirecturl = $this->getrequest()
                ->getheader('referer')
                ->geturi();
        }

        $id = (int) $this->params()->fromquery('entity_id');
        $token = $this->params()->fromquery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /** @var \application\entity\nmtprocuregrrow $entity ; */
        $entity = $this->doctrineem->getrepository('application\entity\nmtprocuregrrow')->findoneby($criteria);

        if ($entity == null) {
            return $this->redirect()->toroute('access_denied');
        }

        return new viewmodel(array(
            'redirecturl' => $redirecturl,
            'errors' => null,
            'entity' => $entity,
            'target' => $entity->getgr(),
            'currency_list' => $currency_list,
            'gl_list' => $gl_list,
            'cost_center_list' => $cost_center_list,
            'n' => 0
        ));
    }

    /**
     *
     * @return \zend\http\response|\zend\view\model\viewmodel
     */
    public function girdaction()
    {
        $request = $this->getrequest();

        if ($request->getheader('referer') == null) {
            // return $this->redirect()->toroute('access_denied');
        }

        // $pq_curpage = $_get ["pq_curpage"];
        // $pq_rpp = $_get ["pq_rpp"];

        $target_id = (int) $this->params()->fromquery('target_id');
        $token = $this->params()->fromquery('token');
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );
        $target = $this->doctrineem->getrepository('application\entity\nmtprocuregr')->findoneby($criteria);

        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new escaper();

        if ($target instanceof \application\entity\nmtprocuregr) {

            $query = 'select e from application\entity\nmtprocuregrrow e
            where e.gr=?1 and e.isactive =?2 order by e.rownumber';

            $list = $this->doctrineem->createquery($query)
                ->setparameters(array(
                "1" => $target,
                "2" => 1
            ))
                ->getresult();

            $total_records = 0;
            if (count($list) > 0) {
                $escaper = new escaper();

                $total_records = count($list);
                foreach ($list as $a) {

                    /** @var \application\entity\nmtprocuregrrow $a ;*/

                    $a_json_row["row_id"] = $a->getid();
                    $a_json_row["row_token"] = $a->gettoken();
                    $a_json_row["row_number"] = $a->getrownumber();
                    $a_json_row["row_unit"] = $a->getunit();
                    $a_json_row["row_quantity"] = $a->getquantity();

                    if ($a->getunitprice() != null) {
                        $a_json_row["row_unit_price"] = number_format($a->getunitprice(), 2);
                    } else {
                        $a_json_row["row_unit_price"] = 0;
                    }

                    if ($a->getnetamount() != null) {
                        $a_json_row["row_net"] = number_format($a->getnetamount(), 2);
                    } else {
                        $a_json_row["row_net"] = 0;
                    }

                    if ($a->gettaxrate() != null) {
                        $a_json_row["row_tax_rate"] = $a->gettaxrate();
                    } else {
                        $a_json_row["row_tax_rate"] = 0;
                    }

                    if ($a->getgrossamount() != null) {
                        $a_json_row["row_gross"] = number_format($a->getgrossamount(), 2);
                    } else {
                        $a_json_row["row_gross"] = 0;
                    }

                    $a_json_row["pr_number"] = "";
                    if ($a->getprrow() != null) {
                        if ($a->getprrow()->getpr() != null) {

                            $link = '<a target="_blank" href="/procure/pr/show?token=' . $a->getprrow()
                                ->getpr()
                                ->gettoken() . '&entity_id=' . $a->getprrow()
                                ->getpr()
                                ->getid() . '&checkum=' . $a->getprrow()
                                ->getpr()
                                ->getchecksum() . '"> ... </a>';

                            $a_json_row["pr_number"] = $a->getprrow()
                                ->getpr()
                                ->getprnumber() . $link;
                        }
                    }

                    $item_detail = "/inventory/item/show1?token=" . $a->getitem()->gettoken() . "&checksum=" . $a->getitem()->getchecksum() . "&entity_id=" . $a->getitem()->getid();
                    if ($a->getitem()->getitemname() !== null) {
                        $onclick = "showjquerydialog('detail of item: " . $escaper->escapejs($a->getitem()
                            ->getitemname()) . "','1310',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                    } else {
                        $onclick = "showjquerydialog('detail of item: " . ($a->getitem()->getitemname()) . "','1310',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                    }

                    if (strlen($a->getitem()->getitemname()) < 35) {
                        $a_json_row["item_name"] = $a->getitem()->getitemname() . '<a style="cursor:pointer;color:blue"  item-pic="" id="' . $a->getitem()->getid() . '" item_name="' . $a->getitem()->getitemname() . '" title="' . $a->getitem()->getitemname() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;....&nbsp;&nbsp;</a>';
                    } else {
                        $a_json_row["item_name"] = substr($a->getitem()->getitemname(), 0, 30) . '<a style="cursor:pointer;;color:blue"  item-pic="" id="' . $a->getitem()->getid() . '" item_name="' . $a->getitem()->getitemname() . '" title="' . $a->getitem()->getitemname() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</a>';
                    }

                    // $a_json_row["item_name"] = $a->getitem()->getitemname();

                    $a_json_row["item_sku"] = $a->getitem()->getitemsku();
                    $a_json_row["item_token"] = $a->getitem()->gettoken();
                    $a_json_row["item_checksum"] = $a->getitem()->getchecksum();
                    $a_json_row["fa_remarks"] = $a->getfaremarks();
                    $a_json_row["remarks"] = $a->getremarks();

                    if ($a->getglaccount() !== null) {
                        $a_json_row["gl_account"] = $a->getglaccount()->getaccountnumber();
                    } else {
                        $a_json_row["gl_account"] = "n/a";
                    }

                    $a_json[] = $a_json_row;
                }
            }

            $a_json_final['data'] = $a_json;
            $a_json_final['totalrecords'] = $total_records;
            // $a_json_final ['curpage'] = $pq_curpage;
        }

        $response = $this->getresponse();
        $response->getheaders()->addheaderline('content-type', 'application/json');
        $response->setcontent(json_encode($a_json_final));
        return $response;
    }

    /**
     */
    public function downloadaction()
    {
        $request = $this->getrequest();
        if ($request->getheader('referer') == null) {
            return $this->redirect()->toroute('access_denied');
        }

        $target_id = (int) $this->params()->fromquery('target_id');
        $token = $this->params()->fromquery('token');

        /**@var \application\repository\nmtprocureporepository $res ;*/
        $res = $this->doctrineem->getrepository('application\entity\nmtprocurepo');
        $rows = $res->downloadvendorpo($target_id, $token);

        if ($rows !== null) {

            $target = null;
            if (count($rows) > 0) {
                $pr_row_1 = $rows[0];
                if ($pr_row_1 instanceof nmtprocureporow) {
                    $target = $pr_row_1->getpo();
                }

                // create new phpexcel object
                $objphpexcel = new spreadsheet();

                // set document properties
                $objphpexcel->getproperties()
                    ->setcreator("nguyen mau tri")
                    ->setlastmodifiedby("nguyen mau tri")
                    ->settitle("office 2007 xlsx test document")
                    ->setsubject("office 2007 xlsx test document")
                    ->setdescription("test document for office 2007 xlsx, generated using php classes.")
                    ->setkeywords("office 2007 openxml php")
                    ->setcategory("test result file");

                // add some data
                $objphpexcel->setactivesheetindex(0)->setcellvalue('b1', $target->getinvoiceno());

                // add some data
                $objphpexcel->setactivesheetindex(0)->setcellvalue('c1', $target->getinvoicedate());

                $header = 3;
                $i = 0;

                $objphpexcel->setactivesheetindex(0)->setcellvalue('a1', "contract/po:" . $target->getsysnumber());

                $objphpexcel->setactivesheetindex(0)->setcellvalue('a' . $header, "fa remarks");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('b' . $header, "#");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('c' . $header, "sku");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('d' . $header, "item");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('e' . $header, "unit");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('f' . $header, "quantity");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('g' . $header, "unit price");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('h' . $header, "net amount");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('i' . $header, "tax rate");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('j' . $header, "tax amount");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('k' . $header, "gross amount");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('l' . $header, "pr number");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('m' . $header, "pr date");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('n' . $header, "requested q/ty");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('o' . $header, "requested name");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('p' . $header, "rowno.");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('q' . $header, "remarks");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('r' . $header, "ref.no.");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('s' . $header, "item.no.");
                $objphpexcel->setactivesheetindex(0)->setcellvalue('t' . $header, "po.item name");

                foreach ($rows as $r) {

                    /**@var \application\entity\nmtprocureporow $a ;*/
                    $a = $r;

                    $i ++;
                    $l = $header + $i;
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('a' . $l, $a->getfaremarks());
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('b' . $l, $i);

                    if ($a->getitem() !== null) {
                        $objphpexcel->setactivesheetindex(0)->setcellvalue('c' . $l, $a->getitem()
                            ->getitemsku());
                        $objphpexcel->setactivesheetindex(0)->setcellvalue('d' . $l, $a->getitem()
                            ->getitemname());
                    } else {
                        $objphpexcel->setactivesheetindex(0)->setcellvalue('c' . $l, "na");
                        $objphpexcel->setactivesheetindex(0)->setcellvalue('d' . $l, "na");
                    }
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('e' . $l, $a->getunit());
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('f' . $l, $a->getquantity());
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('g' . $l, $a->getunitprice());
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('h' . $l, $a->getnetamount());
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('i' . $l, $a->gettaxrate());
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('j' . $l, $a->gettaxamount());
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('k' . $l, $a->getgrossamount());

                    if ($a->getprrow() !== null) {

                        if ($a->getprrow()->getpr() !== null) {
                            $objphpexcel->setactivesheetindex(0)->setcellvalue('l' . $l, $a->getprrow()
                                ->getpr()
                                ->getprnumber());
                            $objphpexcel->setactivesheetindex(0)->setcellvalue('m' . $l, $a->getprrow()
                                ->getpr()
                                ->getsubmittedon());
                        }
                        $objphpexcel->setactivesheetindex(0)->setcellvalue('n' . $l, $a->getprrow()
                            ->getquantity());
                        $objphpexcel->setactivesheetindex(0)->setcellvalue('o' . $l, $a->getprrow()
                            ->getrowname());
                    } else {
                        $objphpexcel->setactivesheetindex(0)->setcellvalue('l' . $l, "na");
                        $objphpexcel->setactivesheetindex(0)->setcellvalue('m' . $l, "na");
                        $objphpexcel->setactivesheetindex(0)->setcellvalue('n' . $l, 0);
                        $objphpexcel->setactivesheetindex(0)->setcellvalue('o' . $l, "");
                    }

                    $objphpexcel->setactivesheetindex(0)->setcellvalue('p' . $l, $a->getrownumber());
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('q' . $l, $a->getremarks());
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('r' . $l, $a->getrowidentifer());
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('s' . $l, $a->getitem()
                        ->getsysnumber());
                    $objphpexcel->setactivesheetindex(0)->setcellvalue('t' . $l, $a->getvendoritemcode());
                }

                // rename worksheet
                $objphpexcel->getactivesheet()->settitle("contract-po");

                $objphpexcel->getactivesheet()->setautofilter("a" . $header . ":t" . $header);

                // set active sheet index to the first sheet, so excel opens this as the first sheet
                $objphpexcel->setactivesheetindex(0);

                // redirect output to a client’s web browser (excel2007)
                header('content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('content-disposition: attachment;filename="invoice' . $target->getid() . '.xlsx"');
                header('cache-control: max-age=0');
                // if you're serving to ie 9, then the following may be needed
                header('cache-control: max-age=1');

                // if you're serving to ie over ssl, then the following may be needed
                header('expires: mon, 26 jul 1997 05:00:00 gmt'); // date in the past
                header('last-modified: ' . gmdate('d, d m y h:i:s') . ' gmt'); // always modified
                header('cache-control: cache, must-revalidate'); // http/1.1
                header('pragma: public'); // http/1.0

                $objwriter = \phpoffice\phpspreadsheet\iofactory::createwriter($objphpexcel, 'xlsx');
                $objwriter->save('php://output');
                exit();
            }
        }
        return $this->redirect()->toroute('access_denied');
    }

    /**
     *
     * @return \zend\view\helper\viewmodel
     */
    public function listaction()
    {
        $request = $this->getrequest();

        // accepted only ajax request
        if (! $request->isxmlhttprequest()) {
            return $this->redirect()->toroute('access_denied');
        }
        ;

        $this->layout("layout/user/ajax");

        $invoice_id = (int) $this->params()->fromquery('target_id');
        $invoice_token = $this->params()->fromquery('token');

        $criteria = array(
            'id' => $invoice_id,
            'token' => $invoice_token
        );

        /** @var \application\entity\finvendorinvoice $target ;*/
        $target = $this->doctrineem->getrepository('application\entity\finvendorinvoice')->findoneby($criteria);

        if ($target !== null) {

            $criteria = array(
                'invoice' => $invoice_id,
                'isactive' => 1
            );

            $query = 'select e from application\entity\finvendorinvoicerow e
            where e.invoice=?1 and e.isactive =?2';

            $list = $this->doctrineem->createquery($query)
                ->setparameters(array(
                "1" => $target,
                "2" => 1
            ))
                ->getresult();
            return new viewmodel(array(
                'list' => $list,
                'total_records' => count($list),
                'paginator' => null
            ));
        }

        return $this->redirect()->toroute('access_denied');
    }

    /**
     *
     * @return \zend\http\response|\zend\view\model\viewmodel
     */
    public function poofitemaction()
    {
        $request = $this->getrequest();
        // accepted only ajax request
        if (! $request->isxmlhttprequest()) {
            return $this->redirect()->toroute('access_denied');
        }
        $this->layout("layout/user/ajax");

        $item_id = (int) $this->params()->fromquery('item_id');
        $token = $this->params()->fromquery('token');

        /**@var \application\repository\nmtprocureporepository $res ;*/
        $res = $this->doctrineem->getrepository('application\entity\nmtprocurepo');
        $rows = $res->getpoofitem($item_id, $token);
        return new viewmodel(array(
            'rows' => $rows
        ));
    }

    /**
     *
     * @return \zend\http\response|\zend\view\model\viewmodel
     */
    public function updaterowaction()
    {
        $a_json_final = array();
        $escaper = new escaper();

        /*
         * $pq_curpage = $_get ["pq_curpage"];
         * $pq_rpp = $_get ["pq_rpp"];
         */
        $sent_list = json_decode($_post['sent_list'], true);
        // echo json_encode($sent_list);

        $to_update = $sent_list['updatelist'];
        foreach ($to_update as $a) {
            $criteria = array(
                'id' => $a['row_id'],
                'token' => $a['row_token']
            );

            /** @var \application\entity\nmtprocuregrrow $entity */
            $entity = $this->doctrineem->getrepository('application\entity\nmtprocuregrrow')->findoneby($criteria);

            if ($entity != null) {
                $entity->setquantity($a['row_quantity']);
                $entity->setfaremarks($a['fa_remarks']);
                $entity->setrownumber($a['row_number']);
                // $a_json_final['updatelist']=$a['row_id'] . 'has been updated';
                $this->doctrineem->persist($entity);
            }
        }
        $this->doctrineem->flush();

        // $a_json_final["updatelist"]= json_encode($sent_list["updatelist"]);

        $response = $this->getresponse();
        $response->getheaders()->addheaderline('content-type', 'application/json');
        $response->setcontent(json_encode($sent_list));
        return $response;
    }

    /**
     *
     * @return \zend\view\model\viewmodel
     */
    public function updatetokenaction()
    {
        $criteria = array();
        $sort_criteria = array();

        $list = $this->doctrineem->getrepository('application\entity\nmtfinpostingperiod')->findby($criteria, $sort_criteria);

        if (count($list) > 0) {
            foreach ($list as $entity) {
                $entity->settoken(rand::getstring(10, \application\model\constants::char_list, true) . "_" . rand::getstring(21, \application\model\constants::char_list, true));
            }
        }

        $this->doctrineem->flush();

        return new viewmodel(array(
            'list' => $list
        ));
    }

    /**
     *
     * @return \doctrine\orm\entitymanager
     */
    public function getdoctrineem()
    {
        return $this->doctrineem;
    }

    /**
     *
     * @param entitymanager $doctrineem
     * @return \pm\controller\indexcontroller
     */
    public function setdoctrineem(entitymanager $doctrineem)
    {
        $this->doctrineem = $doctrineem;
        return $this;
    }
    
    /**
     *
     *  @return \procure\service\grservice
     */
    public function getgrservice()
    {
        return $this->grservice;
    }
    
    /**
     *
     *  @param \procure\service\grservice $grservice
     */
    public function setgrservice(\procure\service\grservice $grservice)
    {
        $this->grservice = $grservice;
    }
}
