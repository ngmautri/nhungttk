// <?php

// require_once 'Zend/Auth.php';
// require_once 'Zend/Acl.php';
// require_once 'Zend/Acl/Resource.php';
// require_once 'Zend/Acl/Role.php';

// require_once 'Zend/Validate.php';
// require_once 'Zend/Validate/Int.php';
// require_once 'Zend/Validate/NotEmpty.php';

// require_once 'StreamcastMedia/Amf/VO/MessageVO.php';
// require_once 'StreamcastMedia/Amf/Constants.php';
// require_once 'StreamcastMedia/Commons.php';
// require_once 'StreamcastMedia/Amf/Service/Abstract.php';
// require_once 'StreamcastMedia/Amf/Service/Exception.php';
// require_once 'StreamcastMedia/Utils/Paginator.php';
// require_once 'StreamcastMedia/Utils/Files.php';

// require_once 'Company/models/tables/CompanyMemberCounters.php';
// require_once 'Company/models/tables/CompanyMembers.php';
// require_once 'Company/models/tables/Companies.php';
// require_once 'Company/models/tables/Branches.php';

// require_once 'User/models/tables/Users.php';
// require_once 'User/models/tables/UserPhotos.php';

// require_once 'User/VO/UserVO.php';
// require_once 'User/VO/UserPhotoVO.php';

// require_once 'Company/VO/BrancheMemberVO.php';
// require_once 'Company/VO/CompanyTelephoneVO.php';
// require_once 'Company/VO/CompanyAddressVO.php';
// require_once 'Company/VO/CompanyWebSiteVO.php';
// require_once 'Company/VO/CompanyEmailVO.php';
// require_once 'News/VO/NewsVO.php';
// require_once 'Company/VO/CompanyProjectVO.php';

// /**
//  *
//  * @author Ngmautri
//  *
//  * @category   Babelsberg
//  * @package    Company
//  * @subpackage Service
//  *
//  */
// class CompanyAdminService extends StreamcastMedia_Amf_Service_Abstract
// {
// 	const ROLE_ADMIN 	= 'Administrator';
// 	const ROLE_EDITOR 	= 'Redakteur';
// 	const ROLE_MEMBER 	= 'Mitglieder';

// 	/**
// 	 * Privilegs for member
// 	 * @var array
// 	 */
// 	private $memberPrivelegs = array('getMembersOfCompany');

// 	/**
// 	 * Privilegs for editor
// 	 * @var array
// 	 */
// 	private $editorPrivelegs =  array('dummy');

// 	/**
// 	 * Privilegs for admin
// 	 * @var array
// 	 */
// 	private $adminPrivelegs = array('deleteCompany','createCompany');

// 	// ~ Methods ===========================================================================

// 	/**
// 	 * (non-PHPdoc)
// 	 * @see StreamcastMedia/Amf/Service/StreamcastMedia_Amf_Service_Abstract#initAcl($acl)
// 	 */
// 	public function initAcl(Zend_Acl $acl)
// 	{
// 		try
// 		{
// 			//@todo: add global role; implementation of templete pattern
// 			$acl = $this->setupAcl($acl);

// 			//allow registered to involke methode, but check afterInvocation
// 			$acl->allow(StreamcastMedia_Amf_Constants::ROLE_REGISTERED, get_class($this));

// 			$acl->deny(StreamcastMedia_Amf_Constants::ROLE_REGISTERED, get_class($this),
// 			$this->adminPrivelegs);
	
// 			$currentUserId = $this->getCurrentUserId();
// 			$currentUser = $this->getCurrentUser();
	
// 			$companyMembersModel = new CompanyMembers();
// 			$companiesOfUser = $companyMembersModel->getCompaniesOf($currentUserId);

// 			if(count($companiesOfUser) > 0)
// 			{
// 				foreach($companiesOfUser as $company)
// 				{
// 					// add company as acl resource
// 					$company_res = new Zend_Acl_Resource($company->company_id);
// 					$acl->addResource($company_res);

// 					switch($company->role){
// 						case StreamcastMedia_Amf_Constants::ROLE_COMPANY_MEMBER:
// 							$acl->allow($this->getCurrentUserId(),$company_res,$this->memberPrivelegs);
// 							break;

// 						case StreamcastMedia_Amf_Constants::ROLE_COMPANY_EDITOR:
// 							$acl->allow($this->getCurrentUserId(),$company_res,$this->editorPrivelegs);
// 							break;

// 						case StreamcastMedia_Amf_Constants::ROLE_COMPANY_ADMINISTRATOR:
// 							$acl->allow($this->getCurrentUserId(),$company_res);
// 							break;
// 					}
// 				}
// 			}
// 		}catch(Execption $e){
// 			//intentionally left blank
// 		}

// 		return false;
// 	}

// 	/**
// 	 * @return unknown_type
// 	 */
// 	public function getMyCompanies()
// 	{
// 		try
// 		{
// 			$currentUserId = $this->getCurrentUserId();

// 			//Ok, checkACL successful!
// 			$companyMembersModell = new CompanyMembers();
// 			return $companyMembersModell->getCompaniesOf($currentUserId);

// 		}catch(Exception $e)
// 		{
// 			return null;
// 		}
// 	}

// 	/**
// 	 * Create Company. Only Super Admin can do this action
// 	 * @return unknown_type
// 	 */
// 	public function createCompany($company, $addCreator = true)
// 	{
// 		$message = 'undefined';
// 		$code = StreamcastMedia_Amf_Constants::FAILURE;

// 		try
// 		{
// 			$currentUserId =  $this->getCurrentUserId();

// 			//Ok, checkACL successful!

// 			return $this->_createCompany($currentUserId, $company, $addCreator);

// 		}catch(Exception $e){
// 			$message = $e->getMessage();

// 		}catch(Zend_Amf_Server_Exception $e1)
// 		{
// 			$message = $e1->getMessage();
// 			$code = StreamcastMedia_Amf_Constants::ACCESS_DENIED;
// 		}catch(StreamcastMedia_Amf_Service_Exception $e2)
// 		{
// 			$message = $e2->getMessage();
// 		}

// 		$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 		$messageVO->code = $code;
// 		$messageVO->message = $message;
// 		return $messageVO;
// 	}

// 	/**
// 	 * @param $currentUserId
// 	 * @param $company
// 	 * @param $addCreator
// 	 * @return unknown_type
// 	 * @version 0.1
// 	 */
// 	private function _createCompany($currentUserId, $company, $addCreator)
// 	{
// 		try
// 		{
// 			if(is_null($company))
// 			{
// 				throw new StreamcastMedia_Amf_Service_Exception(
// 				"Invalid input! CompanyVO expected!");
// 			}

// 			//trying to validate input

// 			$companiesModel = new Companies();

// 			$errors = array();

// 			if(!$companiesModel->isUniqueName($company->name))
// 			{
// 				$errors[] = "Company [" .  $company->name . "] exists already!";
// 			}

// 			$validName = new Zend_Validate_NotEmpty();
// 			if (!$validName->isValid($company->name)) {
// 				$errors[] = "Please provide company name!";
// 			}

// 			if (count($errors) > 0)
// 			{
// 				throw new StreamcastMedia_Amf_Service_Exception(join("; ",$errors));
// 			}

// 		}catch(Exception $e){
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}

// 		//trying to create company now

// 		$db = Zend_Registry::get('Zend_Db');
// 		$db->beginTransaction();

// 		try
// 		{
// 			$data = array(
// 				'name'				=> $company->name,
// 				'description_de'	=> $company->description_de,
// 				'description_en'	=> $company->description_en,
// 				'created_on' 		=> date('Y-m-d H:i:s')			
// 			);

// 			//create company
// 			$db->insert("mib_companies",$data);
// 			$companyId = $db->lastInsertId("mib_companies");

// 			//Add addresses
// 			if(count($company->addresses)>0)
// 			{
// 				foreach	($company->addresses as $address){
// 					$data = array(
// 				 		'company_id' 		=> $companyId,
// 				 		'street' 			=> $address->street,
// 				 		'number' 			=> $address->number,
// 				 		'postalcode' 		=> $address->postalcode,
// 				 		'town' 				=> $address->town,
// 				 		'region' 			=> $address->region,
// 				 		'country' 			=> $address->country,
// 				 		'isDefault'			=> $address->isDefault
// 					);
// 					$db->insert("mib_company_addresses",$data);
// 				}
// 			}

// 			//Add emails
// 			if(count($company->emails)>0)
// 			{
// 				foreach	($company->emails as $email){

// 					$data = array(
// 				 		'company_id' 		=> $companyId,
// 						'type' 				=> $email->type,
// 				 		'value' 			=> $email->value,
// 				 		'isDefault'			=> $email->isDefault
// 					);
// 					$db->insert("mib_company_emails",$data);
// 				}
// 			}

// 			//Add websites
// 			if(count($company->websites)>0)
// 			{
// 				foreach	($company->websites as $website){
// 					$data = array(
// 				 		'company_id' 		=> $companyId,
// 						'type' 				=> $website->type,
// 				 		'value' 			=> $website->value,
// 				 		'isDefault'			=> $website->isDefault
// 					);
// 					$db->insert("mib_company_websites",$data);
// 				}
// 			}

// 			//Add Telephones
// 			if(count($company->telephones)>0)
// 			{
// 				foreach	($company->telephones as $tele){

// 					$data = array(
// 				 		'company_id' 		=> $companyId,
// 						'type' 				=> $tele->type,
// 				 		'value' 			=> $tele->value,				 		
// 				 		'isDefault'			=> $tele->isDefault
// 					);
// 					$db->insert("mib_company_telephones",$data);
// 				}
// 			}

// 			//Add Logos
// 			if(count($company->logos)>0)
// 			{
// 				foreach	($company->logos as $logo){

// 					if($logo->size == null || $logo->size =='')
// 					{
// 						$logo->size = 'small';
// 					}

// 					$data = array(
// 				 		'company_id' 		=> $companyId,
// 				 		'url' 				=> $logo->url,
// 						'size' 				=> $logo->size
// 					);
// 					$db->insert("mib_company_logos",$data);
// 				}
// 			}

// 			//Add Branches
// 			if(count($company->branches)>0)
// 			{
// 				foreach	($company->branches as $branche){
// 					$data = array(
// 				 		'company_id' 		=> $companyId,
// 				 		'branche_id' 		=> $branche->branche_id
// 					);
// 					$db->insert("mib_branche_members",$data);
// 				}
// 			}

// 			//make directories for company
// 			$this->createCompanyFoldersById($companyId);

// 			//Initiate CompanyMemberCounter
// 			$data = array(
// 				'company_id'	=> $companyId,
// 				'members' 		=> 0
// 			);
// 			$db->insert("mib_company_member_counters",$data);

// 			if($addCreator)
// 			{
// 				$newCompanyMember = new CompanyMemberVO();
// 				$newCompanyMember->user_id 		= $currentUserId;
// 				$newCompanyMember->company_id 	= $companyId;
// 				$newCompanyMember->role 		= StreamcastMedia_Amf_Constants::ROLE_COMPANY_ADMINISTRATOR;
// 				$this->_addCompanyMember($currentUserId, $newCompanyMember);
// 			}

// 			$db->commit();

// 		}catch(Exception $e){

// 			$db->rollBack();
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}

// 		$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 		$messageVO->isSuccess = true;
// 		$messageVO->code = StreamcastMedia_Amf_Constants::SUCCESS;
// 		$messageVO->message = "Company [". $company->name . "] added successfully!";
// 		return $messageVO;
// 	}

// 	/**
// 	 *
// 	 * @param $companyId
// 	 * @param $company
// 	 * @version 0.1
// 	 * @return  StreamcastMedia_Amf_VO_MessageVO Message
// 	 */
// 	public function editCompany($companyId, $company = null)
// 	{
// 		$message= 'undefined';
// 		$code = StreamcastMedia_Amf_Constants::FAILURE;

// 		try
// 		{
// 			$currentUserId =  $this->getCurrentUserId();

// 			$this->afterInvocation(new Zend_Acl_Role($currentUserId),
// 			new Zend_Acl_Resource($companyId),'editCompany');

// 			//Ok, checkACL successful!

// 			return $this->_editCompany($companyId, $company);

// 		}catch(Exception $e){
// 			$message = $e->getMessage();

// 		}catch(Zend_Amf_Server_Exception $e1)
// 		{
// 			$message = $e1->getMessage();
// 			$code = StreamcastMedia_Amf_Constants::ACCESS_DENIED;
// 		}catch(StreamcastMedia_Amf_Service_Exception $e2)
// 		{
// 			$message = $e2->getMessage();
// 		}

// 		$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 		$messageVO->code = $code;
// 		$messageVO->message = $message;
// 		return $messageVO;
// 	}

// 	/**
// 	 *
// 	 * @param $companyId
// 	 * @param $company
// 	 * @version 0.1
// 	 * @return unknown_type
// 	 */
// 	private function _editCompany($companyId, $company)
// 	{
// 		try
// 		{
// 			$companiesModel = new Companies();
// 			$companyToEdit = $companiesModel->getCompanyToEdit($companyId);

// 			if($companyToEdit == null)
// 			{
// 				throw new StreamcastMedia_Amf_Service_Exception(
// 				"CompanyID " . $companyId . " not found or removed!");
// 			}

// 			if($company == null)
// 			{
// 				//hidden news and projects
// 				$companyToEdit->news = 'hidden';
// 				$companyToEdit->projects = 'hidden';

// 				if($companyToEdit->addresses == null)
// 				{
// 					$address = new CompanyAddressVO();
// 					$address->company_id = $companyId;
// 					$companyToEdit->addresses[] = $address;
// 				}

// 				if($companyToEdit->emails == null)
// 				{
// 					$email = new CompanyEmailVO();
// 					$email->company_id = $companyId;
// 					$companyToEdit->emails[] = $email;
// 				}

// 				if($companyToEdit->telephones == null)
// 				{
// 					$telephone = new CompanyTelephoneVO();
// 					$telephone->company_id = $companyId;
// 					$companyToEdit->telephones[] = $telephone;
// 				}
					
// 				if($companyToEdit->logos == null)
// 				{
// 					$logo = new CompanyLogoVO();
// 					$logo->company_id = $companyId;
// 					$companyToEdit->logos[] = $logo;
// 				}

// 				if($companyToEdit->websites == null)
// 				{
// 					$web = new CompanyWebsiteVO();
// 					$web->company_id = $companyId;
// 					$companyToEdit->websites[] = $web;
// 				}

// 				return $companyToEdit;
// 			}

// 			//validate input

// 			$errors = array();

// 			if($company->name == null || $company->name == '')
// 			{
// 				$company->name = $companyToEdit->name;
// 			}else{

// 				// if company changed, then check, if new name is valid.
// 				if($company->name != $companyToEdit->name)
// 				{
// 					if(!$companiesModel->isUniqueName($company->name))
// 					{
// 						$errors[] = "Company [" .  $company->name . "] exists already!";
// 					}
// 				}
// 			}

// 			if($company->description_de == null || $company->description_de == '')
// 			{
// 				$company->description_de = $companyToEdit->description_de;
// 			}

// 			if($company->description_en == null || $company->description_en == '')
// 			{
// 				$company->description_en = $companyToEdit->description_en;
// 			}


// 			if (count($errors) > 0)
// 			{
// 				throw new StreamcastMedia_Amf_Service_Exception(join("; ",$errors));
// 			}

// 		}catch(Exception $e)
// 		{
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}

// 		//OK, Trying to update now

// 		$db = Zend_Registry::get('Zend_Db');
// 		$db->beginTransaction();

// 		try
// 		{
// 			$data = array(
// 				'name'				=> $company->name,
// 				'description_de'	=> $company->description_de,
// 				'description_en'	=> $company->description_en,
// 				'created_on' 		=> date('Y-m-d H:i:s')			
// 			);

// 			$where = $db->quoteInto('id = ?', $companyId);
// 			$db->update("mib_companies",$data, $where);

// 			//Update or Adding new address
// 			if(count($company->addresses)>0)
// 			{
// 				foreach	($company->addresses as $address){
// 					$data = array(
// 				 		'company_id' 		=> $companyId,
// 				 		'street' 			=> $address->street,
// 				 		'number' 			=> $address->number,
// 				 		'postalcode' 		=> $address->postalcode,
// 				 		'town' 				=> $address->town,
// 				 		'region' 			=> $address->region,
// 				 		'country' 			=> $address->country,
// 				 		'isDefault'			=> $address->isDefault
// 					);

// 					if($address->id == null || $address->id == 0)
// 					{
// 						$db->insert("mib_company_addresses",$data);
// 					}else{
// 						$where = $db->quoteInto('id = ?', $address->id);
// 						$db->update("mib_company_addresses",$data, $where);
// 					}
// 				}
// 			}

// 			//Update or Adding Emails
// 			if(count($company->emails)>0)
// 			{
// 				foreach	($company->emails as $email){

// 					$data = array(
// 				 		'company_id' 		=> $companyId,
// 						'type' 				=> $email->type,
// 				 		'value' 			=> $email->value,
// 				 		'isDefault'			=> $email->isDefault
// 					);
// 					if($email->id == null || $email->id == 0)
// 					{
// 						$db->insert("mib_company_emails",$data);
// 					}else{
// 						$where = $db->quoteInto('id = ?', $email->id);
// 						$db->update("mib_company_emails",$data, $where);
// 					}
// 				}
// 			}

// 			//Update or Adding websites
// 			if(count($company->websites)>0)
// 			{
// 				foreach	($company->websites as $website){
// 					$data = array(
// 				 		'company_id' 		=> $companyId,
// 						'type' 				=> $website->type,
// 				 		'value' 			=> $website->value,
// 				 		'isDefault'			=> $website->isDefault
// 					);

// 					if($website->id == null || $website->id == 0)
// 					{
// 						$db->insert("mib_company_websites",$data);
// 					}else{
// 						$where = $db->quoteInto('id = ?', $website->id);
// 						$db->update("mib_company_websites",$data, $where);
// 					}
// 				}
// 			}

// 			//Update or Adding Telefones
// 			if(count($company->telephones)>0)
// 			{
// 				foreach	($company->telephones as $tele){

// 					$data = array(
// 				 		'company_id' 		=> $companyId,
// 				 		'value' 			=> $tele->value,
// 				 		'type' 				=> $tele->type,
// 				 		'isDefault'			=> $tele->isDefault
// 					);

// 					if($tele->id == null || $tele->id == 0)
// 					{
// 						$db->insert("mib_company_telephones",$data);
// 					}else{
// 						$where = $db->quoteInto('id = ?', $tele->id);
// 						$db->update("mib_company_telephones",$data, $where);
// 					}
// 				}
// 			}

// 			//Update or Adding Logos
// 			if(count($company->logos)>0)
// 			{
// 				foreach	($company->logos as $logo){

// 					if($logo->size == null || $logo->size =='')
// 					{
// 						$logo->size = 'small';
// 					}

// 					$data = array(
// 				 		'company_id' 		=> $companyId,
// 				 		'url' 				=> $logo->url,
// 						'size' 				=> $logo->size
// 					);
// 					if($logo->id == null || $logo->id == 0)
// 					{
// 						$db->insert("mib_company_logos",$data);
// 					}else{
// 						$where = $db->quoteInto('id = ?', $logo->id);
// 						$db->update("mib_company_logos",$data, $where);
// 					}
// 				}
// 			}

// 			//Update or Adding branches; besondere
// 			if(count($company->branches)>0)
// 			{
// 				//delete current branches of company
// 				$where = array(
// 				$db->quoteInto('company_id = ?', $companyId)
// 				);
// 				$db->delete("mib_branche_members", $where);

// 				foreach	($company->branches as $branche)
// 				{
// 					$data = array(
// 				 		'company_id' 		=> $companyId,
// 				 		'branche_id' 		=> $branche->branche_id
// 					);
// 					$db->insert("mib_branche_members",$data);
// 				}
// 			}

// 			$db->commit();

// 			$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 			$messageVO->isSuccess = true;
// 			$messageVO->code = StreamcastMedia_Amf_Constants::SUCCESS;
// 			$messageVO->message = "Company [". $company->name . "] updated successfully!";
// 			return $messageVO;

// 		}catch(Exception $e){

// 			$db->rollBack();
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getTraceAsString());
// 		}

// 	}

// 	/**
// 	 * @param $companyId
// 	 * @version 0.1
// 	 * @return StreamcastMedia_Amf_VO_MessageVO
// 	 */
// 	public function deleteCompany($companyId)
// 	{
// 		$message = 'undefined';
// 		$code = StreamcastMedia_Amf_Constants::FAILURE;

// 		try
// 		{
// 			$currentUserId =  $this->getCurrentUserId();

// 			$this->afterInvocation(new Zend_Acl_Role($currentUserId),
// 			new Zend_Acl_Resource($companyId),'deleteCompany');

// 			//Ok, checkACL succussed!

// 			return $this->_deleteCompany($companyId);

// 		}catch(Exception $e){
// 			$message = $e->getMessage();

// 		}catch(Zend_Amf_Server_Exception $e1)
// 		{
// 			$message = $e1->getMessage();
// 			$code = StreamcastMedia_Amf_Constants::ACCESS_DENIED;
// 		}catch(StreamcastMedia_Amf_Service_Exception $e2)
// 		{
// 			$message = $e2->getMessage();
// 		}

// 		$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 		$messageVO->code = $code;
// 		$messageVO->message = $message;
// 		return $messageVO;
// 	}

// 	/**
// 	 * @param $companyId
// 	 * @version 0.1
// 	 * @return unknown_type
// 	 */
// 	private function _deleteCompany($companyId)
// 	{
// 		try
// 		{
// 			$companiesModel = new Companies();
// 			$company = $companiesModel->find($companyId)->current();

// 			//delete directories of company
// 			$company_dir = ROOT.
// 			DIRECTORY_SEPARATOR . "data".
// 			DIRECTORY_SEPARATOR . "companies".
// 			DIRECTORY_SEPARATOR . "company_" . $companyId;

// 			StreamcastMedia_Utils_Files::deleteDirectory($company_dir);

// 			$adapter = $companiesModel->getAdapter();
// 			$where = array(
// 			$adapter->quoteInto('id = ?', $companyId)
// 			);
// 			$num = $companiesModel->delete($where);

// 			if($num>0)
// 			{
// 				$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 				$messageVO->isSuccess = true;
// 				$messageVO->code = StreamcastMedia_Amf_Constants::SUCCESS;
// 				$messageVO->message = $num . " company(s) removed successfully!";

// 				return $messageVO;
// 			}else{
// 				throw new StreamcastMedia_Amf_Service_Exception("No data row(s) deleted!");
// 			}

// 		}catch(Exception $e){
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}
// 	}

// 	/**
// 	 *
// 	 * @param $userId
// 	 * @param $companyId
// 	 * @param $role
// 	 * @param $position
// 	 * @param $sendMail
// 	 * @version 0.1
// 	 * @return unknown_type
// 	 */
// 	public function addMember($userId, $companyId, $role = self::ROLE_MEMBER,
// 	$position = null, $sendMail = false)
// 	{
// 		$message = 'undefined';
// 		$code = StreamcastMedia_Amf_Constants::FAILURE;

// 		try
// 		{
// 			$currentUserId = $this->getCurrentUserId();

// 			$this->afterInvocation(new Zend_Acl_Role($currentUserId),
// 			new Zend_Acl_Resource($companyId),'addMember');

// 			//Ok, checkACL successful!

// 			switch($role)
// 			{
// 				case self::ROLE_ADMIN:
// 					$roleInCompany = StreamcastMedia_Amf_Constants::ROLE_COMPANY_ADMINISTRATOR;
// 					break;

// 				case self::ROLE_EDITOR:
// 					$roleInCompany = StreamcastMedia_Amf_Constants::ROLE_COMPANY_EDITOR;
// 					break;

// 				default:
// 					$roleInCompany = StreamcastMedia_Amf_Constants::ROLE_COMPANY_MEMBER;
// 			}

// 			$newCompanyMember = new CompanyMemberVO();
// 			$newCompanyMember->user_id 		= $userId;
// 			$newCompanyMember->company_id 	= $companyId;
// 			$newCompanyMember->role 		= $roleInCompany;
// 			$newCompanyMember->position		= $position;

// 			return $this->_addCompanyMember($currentUserId,$newCompanyMember, $sendMail);

// 		}catch(Zend_Amf_Server_Exception $e){
// 			$message = $e->getMessage();
// 			$code = StreamcastMedia_Amf_Constants::ACCESS_DENIED;

// 		}catch(StreamcastMedia_Amf_Service_Exception $e1){
// 			$message = $e1->getMessage();
// 		}

// 		$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 		$messageVO->code = $code;
// 		$messageVO->message = $message;
// 		return $messageVO;
// 	}

// 	/**
// 	 * @param $companyId
// 	 * @param $firstName
// 	 * @param $lastName
// 	 * @param $email
// 	 * @param $role
// 	 * @param $position
// 	 * @version 0.1
// 	 * @return unknown_type
// 	 */
// 	public function inviteMemberViaEmail($companyId, $firstName, $lastName, $email,
// 	$role = self::ROLE_MEMBER, $position = null)
// 	{
// 		$message = 'undefined!';
// 		$code = StreamcastMedia_Amf_Constants::FAILURE;

// 		try
// 		{
// 			$currentUserId = $this->getCurrentUserId();

// 			parent::afterInvocation(new Zend_Acl_Role($currentUserId),
// 			new Zend_Acl_Resource($companyId),'inviteMemberViaEmail');

// 			//Ok, checkACL successful!

// 			switch($role)
// 			{
// 				case self::ROLE_ADMIN:
// 					$roleInCompany = StreamcastMedia_Amf_Constants::ROLE_COMPANY_ADMINISTRATOR;
// 					break;

// 				case self::ROLE_EDITOR:
// 					$roleInCompany = StreamcastMedia_Amf_Constants::ROLE_COMPANY_EDITOR;
// 					break;

// 				default:
// 					$roleInCompany = StreamcastMedia_Amf_Constants::ROLE_COMPANY_MEMBER;
// 			}

// 			$usersModel = new Users();
// 			$user = $usersModel->getUserByEmail($email);

// 			if($user == null)
// 			{
// 				$newUserId = $this->_autoAddUser($firstName,$lastName,$email, $role ,$companyId);

// 				// create new Member
// 				$newCompanyMember = new CompanyMemberVO();
// 				$newCompanyMember->user_id 		= $newUserId;
// 				$newCompanyMember->company_id 	= $companyId;
// 				$newCompanyMember->role 		= $roleInCompany;
// 				$newCompanyMember->position		= $position;

// 				return $this->_addCompanyMember($currentUserId, $newCompanyMember,false);
// 			}else{
// 				// create new Member
// 				$newCompanyMember = new CompanyMemberVO();
// 				$newCompanyMember->user_id 		= $user->id;
// 				$newCompanyMember->company_id 	= $companyId;
// 				$newCompanyMember->role 		= $roleInCompany;
// 				$newCompanyMember->position 	= $position;

// 				//granting role, but sendMail required
// 				return $this->_addCompanyMember($currentUserId, $newCompanyMember,true);
// 			}

// 		}catch(Zend_Amf_Server_Exception $e)
// 		{
// 			$message = $e->getMessage();
// 			$code = StreamcastMedia_Amf_Constants::ACCESS_DENIED;

// 		}catch(StreamcastMedia_Amf_Service_Exception $e1)
// 		{
// 			$message = $e1->getMessage();
// 		}

// 		$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 		$messageVO->code = $code;
// 		$messageVO->message = $message;
// 		return $messageVO;
// 	}

// 	/**
// 	 *
// 	 * @param $member
// 	 * @param $sendMail
// 	 * @return unknown_type
// 	 */
// 	private function _addCompanyMember($currentUserId, $member, $sendMail = false)
// 	{
// 		try
// 		{
// 			if(is_null($member))
// 			{
// 				throw new StreamcastMedia_Amf_Service_Exception('Required input not supplied!');
// 			}

// 			$companyMembersModel = new CompanyMembers();

// 			$errors = array();

// 			// user_id must not be empty
// 			$validUid = new Zend_Validate_NotEmpty();
// 			if (!$validUid->isValid($member->user_id))
// 			{
// 				$errors[] = "Please provide a user id!";
// 			}

// 			$validIntUserId = new Zend_Validate_Int();
// 			if (!$validIntUserId->isValid($member->user_id))
// 			{
// 				$errors[] = "User ID must be integer!";
// 			}

// 			// CompanyId must not be empty
// 			$validComId = new Zend_Validate_NotEmpty();
// 			if (!$validComId->isValid($member->company_id)){
// 				$errors[] = "Please provide a company id!";
// 			}

// 			// company id must be integer
// 			$validIntComId = new Zend_Validate_Int();
// 			if (!$validIntComId->isValid($member->company_id))
// 			{
// 				$errors[] = "Company ID must be integer!";
// 			}

// 			$companiesModel = new Companies();
// 			$company = $companiesModel->find($member->company_id)->current();

// 			if($company == null)
// 			{
// 				$errors[] = "Company ID " . $member->company_id . " not found or removed!";
// 			}

// 			$usersModel = new Users();
// 			$user = $usersModel->find($member->user_id)->current();

// 			if($user == null)
// 			{
// 				$errors[] = "User ID " . $member->user_id . " is not found or removed!";
// 			}

// 			if($companyMembersModel->isMember($member->user_id, $member->company_id))
// 			{
// 				$errors[] = "UserID ". $member->user_id." is already member of CompanyID " .
// 				$member->company_id;
// 			}

// 			if (count($errors) > 0)
// 			{
// 				throw new StreamcastMedia_Amf_Service_Exception(join("; ",$errors));
// 			}

// 		}catch(Exception $e){
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}

// 		// Validation passed; Adding Member

// 		try
// 		{
// 			if ($member->role=='' || $member->role==null){
// 				$member->role = StreamcastMedia_Amf_Constants::ROLE_COMPANY_MEMBER;
// 			}

// 			if ($member->position=='' || $member->position==null){
// 				$member->position = 'keine Angabe';
// 			}

// 			$data = array(
// 				'user_id'		=> $member->user_id,
// 				'company_id'	=> $member->company_id,
// 				'role'			=> $member->role,
// 				'position'		=> $member->position,
// 				'since' 		=> date('Y-m-d H:i:s')
// 			);

// 			if($sendMail == true)
// 			{
// 				$emailText = <<<EOT
				
// <p>Hallo {$user->firstname} {$user->lastname},</p>
// <br/>
// <p>Sie sind als Mitglieder der Firma <em>{$company->name}</em> nominiert worden.</p>

// Ihr Status:
// <ul>
// <li>Rolle	: {$member->role}</li>
// <li>Position: {$member->position}</li>
// </ul> 

// <p>
// Für die erfolgreiche Zusammenarbeit in der Firma wünscht das mein.babelsberg Team
// vielen Erfolg!
// </p>
// <p>(<em>Diese Email wurde automatisch vom System gesendet.</em>)</p>
// EOT;

// 				$config = Zend_Registry::get('Zend_Config');

// 				$mailConfig = array(
// 				'auth' => 'login',
//                 'username' => $config->email->webmaster->username,
//                 'password' => $config->email->webmaster->password
// 				);

// 				$transport = new Zend_Mail_Transport_Smtp($config->email->webmaster->smtp, $mailConfig);

// 				$mail = new Zend_Mail();
// 				$mail->setBodyHtml($emailText);
// 				$mail->setFrom($config->email->webmaster->username, $config->email->webmaster->displayname);
// 				$mail->addTo($user->email, 'User');
// 				$mail->setSubject('Ihr Zugriffrecht für die Firma: ' . $company->name);
// 				$mail->send($transport);
// 			}

// 			$rowId =  $companyMembersModel->insert($data);

// 			//update member counter
// 			$companyMemberCounters = new CompanyMemberCounters();
// 			$companyMemberCounters->updateCounter($member->company_id, CompanyMemberCounters::ACTION_INSERT);

// 			$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 			$messageVO->isSuccess = true;
// 			$messageVO->code = StreamcastMedia_Amf_Constants::SUCCESS;
// 			$messageVO->message = "Member added successfully at row " . $rowId;

// 			return $messageVO;

// 		}catch(Exception $e){
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}
// 	}

// 	/**
// 	 *
// 	 * @param $firstName
// 	 * @param $lastName
// 	 * @param $email
// 	 * @param $role
// 	 * @param $companyId
// 	 * @version 0.1
// 	 * @return int
// 	 */
// 	private function _autoAddUser($firstName,$lastName,$email, $role, $companyId)
// 	{
// 		try
// 		{
// 			$errors = array();

// 			//muss not check email

// 			$validFirstName = new Zend_Validate_NotEmpty();
// 			if (!$validFirstName->isValid($firstName))
// 			{
// 				$errors[] = "Please provide first name!";
// 			}

// 			$validLastName = new Zend_Validate_NotEmpty();
// 			if (!$validLastName->isValid($lastName))
// 			{
// 				$errors[] = "Please provide last name!";
// 			}

// 			if (! Zend_Validate::is($email,'EmailAddress')) {
// 				$errors[] = "Invalid e-mail address!";
// 			}

// 			// CompanyId must not be empty
// 			$validComId = new Zend_Validate_NotEmpty();
// 			if (!$validComId->isValid($companyId)){
// 				$errors[] = "Please provide a company id!";
// 			}

// 			// company id must be integer
// 			$validIntComId = new Zend_Validate_Int();
// 			if (!$validIntComId->isValid($companyId))
// 			{
// 				$errors[] = "Company ID be integer!";
// 			}

// 			$companiesModel = new Companies();
// 			$company = $companiesModel->find($companyId)->current();

// 			if($company == null)
// 			{
// 				$errors[] = "Company ID " . $member->company_id . " is not found or removed!";
// 			}

// 			if (count($errors) > 0)
// 			{
// 				throw new StreamcastMedia_Amf_Service_Exception(join("; ",$errors));
// 			}

// 		}catch (Exception $e) {
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}

// 		// input are now validated!

// 		$db = Zend_Registry::get('Zend_Db');
// 		$db->beginTransaction();

// 		try
// 		{
// 			$model = new Users();
// 			$initialPassword = "123456";

// 			$registrationKey = StreamcastMedia_Commons::generate_random_string();

// 			$emailText = <<<EOT
			
// <p>Hallo {$firstName} {$lastName},</p>
// <br/>
// <p>Sie sind als {$role} der Firma <em>{$company->name}</em> nominiert worden.</p>

// Dazu wurde ein neues Konto für Sie angelegt. Das neue Konto lautet:<br/>
// 	<ul> 
// 	<li>Benutzername /Email : {$email}</li>
// 	<li>Kennwort 			: {$initialPassword} (das Initialkennwort bitte unbeding ändern!)</li>
// 	</ul>

// Um dies zu bestätigen, klicken Sie bitte auf folgenden Link:

// 	<p><a href="http://mibbeta.dyndns.org/MIB/trunk/backend/public/index.php/user/profile/completeregister/key/{$registrationKey}/email/{$email}/name/{$lastName}"/>
// 	http://mibbeta.dyndns.org/MIB/trunk/backend/public/index.php/user/profile/completeregister/key/{$registrationKey}/email/{$email}/name/{$lastName}</a>
// 	</p>

// <p>
// Viele Grüße <br/>
// Ihr mein.babelsberg Team.</p>

// <p>(<em>Diese Email wurde automatisch vom System gesendet.</em>)</p>
// EOT;

// 			$config = Zend_Registry::get('Zend_Config');

// 			$mailConfig = array(
// 				'auth' => 'login',
//                 'username' => $config->email->webmaster->username,
//                 'password' => $config->email->webmaster->password
// 			);

// 			$transport = new Zend_Mail_Transport_Smtp($config->email->webmaster->smtp, $mailConfig);

// 			$mail = new Zend_Mail();
// 			$mail->setBodyHtml($emailText);
// 			$mail->setFrom($config->email->webmaster->username, $config->email->webmaster->displayname);
// 			$mail->addTo($email, 'User');
// 			$mail->setSubject('Ihr Zugriffrecht für die Firma: ' . $company->name);
// 			$mail->send($transport);

// 			$data = array(
// 				'title'				=> '',
// 				'firstname'			=> $firstName,
// 				'lastname'			=> $lastName,
// 				'password'			=> md5($initialPassword),
// 				'salt'				=> '',
// 				'email'				=> $email,
// 				'role'				=> StreamcastMedia_Amf_Constants::ROLE_REGISTERED,
// 				'registration_key'	=> $registrationKey,
// 				'confirmed'			=> 0,			
// 				'register_date'		=> date('Y-m-d H:i:s'),
// 				'lastvisit_date'	=> date('Y-m-d H:i:s'),
// 				'block' 			=> 0			
// 			);

// 			$db->insert('mib_users', $data);
// 			$uid = $db->lastInsertId("mib_users");

// 			//create unser news counter, because no trigger allowed.
// 			$data1 = array(
// 				'author_id'				=> $uid,
// 				'group_news'			=> 0,
// 				'company_news'			=> 0,
// 				'babelsberg_news'		=> 0,
// 				'total_news'			=> 0
// 			);

// 			$db->insert('mib_user_news_counters', $data1);

// 			//create folder for user
// 			$this->createUserFolders($email);

// 			$db->commit();

// 		}catch (Exception $e) {

// 			$db->rollBack();
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}
// 	}

// 	/**
// 	 * Block member of a company
// 	 * @param $userId
// 	 * @param $companyId
// 	 * @return StreamcastMedia_Amf_VO_MessageVO : Message
// 	 */
// 	public function blockMember($userId, $companyId)
// 	{
// 		$companyMemberVO = new CompanyMemberVO();
// 		$companyMemberVO->user_id;
// 		$companyMemberVO->company_id;
// 		$companyMemberVO->block = 1;

// 		return $this->editMember($userId, $companyId, $companyMemberVO);
// 	}

// 	/**
// 	 * Unblock a member in company
// 	 * @param $userId
// 	 * @param $companyId
// 	 * @return StreamcastMedia_Amf_VO_MessageVO : Message
// 	 */
// 	public function unblockMember($userId, $companyId)
// 	{
// 		$companyMemberVO = new CompanyMemberVO();
// 		$companyMemberVO->user_id;
// 		$companyMemberVO->company_id;
// 		$companyMemberVO->block = 0;

// 		return $this->editMember($userId, $companyId, $companyMemberVO);
// 	}

// 	/**
// 	 * Make a member as a contact person
// 	 * @param $userId
// 	 * @param $companyId
// 	 * @return StreamcastMedia_Amf_VO_MessageVO : Message
// 	 */
// 	public function setContactPerson($userId, $companyId)
// 	{
// 		$companyMemberVO = new CompanyMemberVO();
// 		$companyMemberVO->user_id;
// 		$companyMemberVO->company_id;
// 		$companyMemberVO->is_contact_person = 1;

// 		return $this->editMember($userId, $companyId, $companyMemberVO);
// 	}

// 	/**
// 	 * @param $userIDs
// 	 * @param $company
// 	 * @return unknown_type
// 	 */
// 	public function setContactPersons($userIDs, $companyId)
// 	{
// 		$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 		$message = 'undefined';
// 		$code = StreamcastMedia_Amf_Constants::FAILURE;

// 		try
// 		{
// 			//unset all member
// 			$companyMembersModel = new CompanyMembers();
// 			$companyMembersModel->unsetContactPersonsOf($companyId);

// 			foreach($userIDs as $userId)
// 			{
// 				$this->setContactPerson($userId, $companyId);
// 			}
// 			$messageVO->isSuccess = true;
// 			$messageVO->code = StreamcastMedia_Amf_Constants::SUCCESS;
// 			$messageVO->message = join("; ",$userIDs) .
// 			" now contact persons of CompanyID "	. $companyId;

// 			return $messageVO;

// 		}catch(Zend_Amf_Server_Exception $e){
// 			$message = $e->getMessage();
// 			$code = StreamcastMedia_Amf_Constants::ACCESS_DENIED;
// 		}catch(StreamcastMedia_Amf_Service_Exception $e1){
// 			$message = $e1->getMessage();
// 		}catch(Exception $e2){
// 			$message = $e2->getMessage();
// 		}

// 		$messageVO->code = $code;
// 		$messageVO->message = $message;
// 		return $messageVO;
// 	}


// 	/**
// 	 * Unset contact person
// 	 * @param $userId
// 	 * @param $companyId
// 	 * @return StreamcastMedia_Amf_VO_MessageVO : Message
// 	 */
// 	public function unsetContactPerson($userId, $companyId)
// 	{
// 		$companyMemberVO = new CompanyMemberVO();
// 		$companyMemberVO->user_id;
// 		$companyMemberVO->company_id;
// 		$companyMemberVO->is_contact_person = 0;

// 		return $this->editMember($userId, $companyId, $companyMemberVO);
// 	}

// 	/**
// 	 * @param $userIDs
// 	 * @param $company
// 	 * @return unknown_type
// 	 */
// 	public function unsetContactPersons($userIDs, $companyId)
// 	{
// 		$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 		$message = 'undefined';
// 		$code = StreamcastMedia_Amf_Constants::FAILURE;

// 		try
// 		{
// 			foreach($userIDs as $userId)
// 			{
// 				$this->unsetContactPerson($userId, $companyId);
// 			}

// 			$messageVO->isSuccess = true;
// 			$messageVO->code = StreamcastMedia_Amf_Constants::SUCCESS;
// 			$messageVO->message = join("; ",$userIDs) .
// 			" are not contact persons of CompanyID " . $companyId;

// 			return $messageVO;

// 		}catch(Zend_Amf_Server_Exception $e){
// 			$message = $e->getMessage();
// 			$code = StreamcastMedia_Amf_Constants::ACCESS_DENIED;

// 		}catch(StreamcastMedia_Amf_Service_Exception $e1){
// 			$message = $e1->getMessage();

// 		}catch(Exception $e2){
// 			$message = $e2->getMessage();
// 		}

// 		$messageVO->code = $code;
// 		$messageVO->message = $message;
// 		return $messageVO;

// 	}
// 	/**
// 	 * Edit member
// 	 * @param $userId
// 	 * @param $companyId
// 	 * @param $member
// 	 * @return StreamcastMedia_Amf_VO_MessageVO : Message
// 	 */
// 	public function editMember($userId, $companyId, $member = null)
// 	{
// 		$message = 'undefined';
// 		$code = StreamcastMedia_Amf_Constants::FAILURE;

// 		try
// 		{
// 			$currentUserId =  $this->getCurrentUserId();

// 			$this->afterInvocation(new Zend_Acl_Role($currentUserId),
// 			new Zend_Acl_Resource($companyId),'editMember');

// 			//Ok, checkACL successful!

// 			return $this->_editMember($currentUserId, $userId, $companyId, $member);

// 		}catch(Exception $e){
// 			$message = $e->getMessage();

// 		}catch(Zend_Amf_Server_Exception $e1)
// 		{
// 			$message = $e1->getMessage();
// 			$code = StreamcastMedia_Amf_Constants::ACCESS_DENIED;
// 		}catch(StreamcastMedia_Amf_Service_Exception $e2)
// 		{
// 			$message = $e2->getMessage();
// 		}

// 		$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 		$messageVO->code = $code;
// 		$messageVO->message = $message;
// 		return $messageVO;
// 	}

// 	/**
// 	 * @param $userId
// 	 * @param $companyId
// 	 * @param $member
// 	 * @return StreamcastMedia_Amf_VO_MessageVO : Message
// 	 */
// 	private function _editMember($currentUserId, $userId, $companyId, $member)
// 	{
// 		try
// 		{
// 			$companyMembersModel = new CompanyMembers();
// 			$companyMemberToEdit = $companyMembersModel->getMemberDetailsOf($userId, $companyId);

// 			//			if($currentUserId == $userId)
// 			//			{
// 			//				throw new StreamcastMedia_Amf_Service_Exception("Invalid action!");
// 			//			}

// 			if($companyMemberToEdit == null)
// 			{
// 				throw new StreamcastMedia_Amf_Service_Exception(
// 				"UserID " . $userId . " not found or not member of CompanyID ".$companyId ." yet!");
// 			}

// 			if($member == null)
// 			{
// 				// return member to edit
// 				return $companyMemberToEdit;
// 			}
// 		}catch(Exception $e)
// 		{
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}

// 		// Update
// 		try
// 		{
// 			if(is_null($member->role) || $member->role == '')
// 			{
// 				$member->role = $companyMemberToEdit->role;

// 			}else{
// 				switch($member->role)
// 				{
// 					case self::ROLE_ADMIN:
// 						$member->role = StreamcastMedia_Amf_Constants::ROLE_COMPANY_ADMINISTRATOR;
// 						break;

// 					case self::ROLE_EDITOR:
// 						$member->role = StreamcastMedia_Amf_Constants::ROLE_COMPANY_EDITOR;
// 						break;

// 					default:
// 						$member->role = StreamcastMedia_Amf_Constants::ROLE_COMPANY_MEMBER;
// 				}
// 			}

// 			// if nothing changed, or blank
// 			if(is_null($member->position) || $member->position == '')
// 			{
// 				$member->position = $companyMemberToEdit->position;
// 			}

// 			//if nothing changed, or blank
// 			if(is_null($member->block))
// 			{
// 				$member->block = $companyMemberToEdit->block;
// 			}

// 			//if nothing changed, or blank
// 			if(is_null($member->is_contact_person))
// 			{
// 				$member->is_contact_person = $companyMemberToEdit->is_contact_person;
// 			}

// 			$data = array(
// 					'role'				=> $member->role,
// 					'position'			=> $member->position,
// 					'is_contact_person'	=> $member->is_contact_person,
// 					'block'				=> $member->block,
// 					'lastmodified'		=> date('Y-m-d H:i:s')			
// 			);

// 			$gid = $companyMembersModel->update($data, 'id=' . $companyMemberToEdit->id);

// 			$messageVO = new StreamcastMedia_Amf_VO_MessageVO();

// 			if($gid > 0)
// 			{
// 				$messageVO->isSuccess = true;
// 				$messageVO->code = StreamcastMedia_Amf_Constants::SUCCESS;
// 				$messageVO->message = $gid . " row(s) updated successfully!";
// 				return $messageVO;

// 			}else{
// 				throw new StreamcastMedia_Amf_Service_Exception("No data row(s) updated!");
// 			}

// 		}catch(Exception $e)
// 		{
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}
// 	}

// 	/**
// 	 * @return array Standard roles
// 	 */
// 	public function getRoles()
// 	{
// 		return array(self::ROLE_ADMIN,self::ROLE_EDITOR,self::ROLE_MEMBER);
// 	}

// 	/**
// 	 * @param $companyId
// 	 * @param $page
// 	 * @param $perPage
// 	 * @return unknown_type
// 	 */
// 	public function getMembersOfCompany($companyId, $page=1, $perPage=20)
// 	{
// 		try
// 		{
// 			$currentUserId = $this->getCurrentUserId();

// 			$this->afterInvocation(new Zend_Acl_Role($userCurrentId),
// 			new Zend_Acl_Resource($companyId),'getMembersOfCompany');

// 			//Ok, checkACL successful!

// 			$companyMemberCounters = new CompanyMemberCounters();
// 			$totals = $companyMemberCounters->getTotalMembersOf($companyId);

// 			if($totals == 0)
// 			{
// 				return null;
// 			}

// 			$paginator = new StreamcastMedia_Utils_Paginator($totals,$page,$perPage);

// 			$db = Zend_Registry::get('Zend_Db');
// 			$select = new Zend_Db_Select($db);

// 			/*
// 			 SELECT * FROM mib_company_members as t1
// 			 inner join mib_users as t2 on t2.id = t1.user_id
// 			 inner join mib_companies as t3 on t3.id = t1.company_id
// 			 where t1.company_id = 21
// 			 */

// 			$select->from(array('t1'=>'mib_company_members'))
// 			->columns(array('company_member_id'=>'t1.id',
// 			'company_role'=>'t1.role','company_name'=>'t3.name',
// 			'company_block'=>'t1.block'))
// 			->joinInner(array('t2' => 'mib_users'),'t2.id = t1.user_id')
// 			->joinInner(array('t3' => 'mib_companies'),'t3.id = t1.company_id')
// 			->where('t1.company_id = ?', $companyId)
// 			->limit(($paginator->getMaxInPage() - $paginator->getMinInPage())+1,$paginator->getMinInPage()-1)
// 			->order(array('lastname'));

// 			$stmt = $db->query($select);
// 			$results = $stmt->fetchAll();

// 			$obj = new stdClass();
// 			$obj->totals 		= $totals;
// 			$obj->page 			= $paginator->getPage();
// 			$obj->totalPages 	= $paginator->getTotalPages();
// 			$obj->minInPageSet	= $paginator->getMinInPageSet();
// 			$obj->maxInPageSet	= $paginator->getMaxInPageSet();
// 			$obj->members = null ;

// 			if(count($results)>0)
// 			{
// 				$output = array();
// 				foreach($results as $result)
// 				{
// 					$companyMemberVO = new CompanyMemberVO();
// 					$companyMemberVO->id 				= $result['company_member_id'];
// 					$companyMemberVO->user_id 			= $result['user_id'];
// 					$companyMemberVO->company_id 		= $result['company_id'];
// 					$companyMemberVO->position 			= $result['position'];
// 					$companyMemberVO->role 				= $result['company_role'];
// 					$companyMemberVO->is_contact_person	= $result['is_contact_person'];
// 					$companyMemberVO->since 			= $result['since'];
// 					$companyMemberVO->block 			= $result['company_block'];
// 					$companyMemberVO->lastmodified 		= $result['lastmodified'];

// 					$companyMemberVO->title 			= $result['title'];
// 					$companyMemberVO->firstname 		= $result['firstname'];
// 					$companyMemberVO->lastname 			= $result['lastname'];
// 					$companyMemberVO->email 			= $result['email'];
// 					$companyMemberVO->company_name 		= $result['company_name'];

// 					$output[] = $companyMemberVO;
// 				}

// 				if(count($output)>0)
// 				{
// 					$obj->members= $output;
// 				}
// 			}

// 			return $obj;

// 		}catch(Zend_Amf_Server_Exception $e)
// 		{
// 			//nothing
// 		}catch(StreamcastMedia_Amf_Service_Exception $e1)
// 		{
// 			//nothing
// 		}
// 		return null;
// 	}


// 	/**
// 	 * @param $userId
// 	 * @param $companyId
// 	 * @return unknown_type
// 	 */
// 	public function removeMember($userId, $companyId)
// 	{
// 		$message = 'undefined';
// 		$code = StreamcastMedia_Amf_Constants::FAILURE;

// 		try
// 		{
// 			$currentUserId = $this->getCurrentUserId();

// 			parent::afterInvocation(new Zend_Acl_Role($currentUserId),
// 			new Zend_Acl_Resource($companyId),'removeMember');

// 			// Ok, checkACL succussed!

// 			return $this->_removeMember($currentUserId, $userId, $companyId);

// 		}catch(Zend_Amf_Server_Exception $e){
// 			$message = $e->getMessage();
// 			$code = StreamcastMedia_Amf_Constants::ACCESS_DENIED;
// 		}catch(StreamcastMedia_Amf_Service_Exception $e1){
// 			$message = $e1->getMessage();
// 		}

// 		$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 		$messageVO->code = $code;
// 		$messageVO->message = $message;
// 		return $messageVO;
// 	}

// 	/**
// 	 *
// 	 * @param $userId
// 	 * @param $companyId
// 	 * @return unknown_type
// 	 */
// 	private function _removeMember($currentUserId, $userId, $companyId)
// 	{
// 		try
// 		{
// 			if($currentUserId == $userId)
// 			{
// 				throw new StreamcastMedia_Amf_Service_Exception("Caution! Suicide. hehe");
// 			}

// 			$companyMembersModel = new CompanyMembers();
// 			$adapter = $companyMembersModel->getAdapter();
// 			$where = array(
// 			$adapter->quoteInto('user_id = ?', $userId),
// 			$adapter->quoteInto('company_id = ?', $companyId)
// 			);

// 			$num = $companyMembersModel->delete($where);

// 			$messageVO = new StreamcastMedia_Amf_VO_MessageVO();
// 			$messageVO->isSuccess = true;
// 			$messageVO->code = StreamcastMedia_Amf_Constants::SUCCESS;
// 			$messageVO->message = $num . " member(s) removed from companyID " . $companyId;

// 			if($num>0)
// 			{
// 				//update member counter
// 				$companyMemberCounters = new CompanyMemberCounters();
// 				$companyMemberCounters->updateCounter($companyId, CompanyMemberCounters::ACTION_DELETE, $num);
// 				return $messageVO;

// 			}else{
// 				throw new StreamcastMedia_Amf_Service_Exception("No data row(s) deleted!");
// 			}
// 		}catch(Exception $e)
// 		{
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}
// 	}

// 	/**
// 	 *
// 	 * @param $name
// 	 * @return unknown_type
// 	 */
// 	public function createCompanyFoldersById($id)
// 	{
// 		try
// 		{
// 			$company_folders_tpl = ROOT.
// 			DIRECTORY_SEPARATOR ."data".
// 			DIRECTORY_SEPARATOR ."templates".
// 			DIRECTORY_SEPARATOR ."company_folders";

// 			$company_dir = ROOT.
// 			DIRECTORY_SEPARATOR ."data".
// 			DIRECTORY_SEPARATOR ."companies".
// 			DIRECTORY_SEPARATOR ."company_" . $id;

// 			if(is_dir($company_dir))
// 			{
// 				StreamcastMedia_Utils_Files::recursiveCopy($company_folders_tpl,$company_dir);
// 				return true;

// 			}else{
// 				if(mkdir($company_dir))
// 				{
// 					StreamcastMedia_Utils_Files::recursiveCopy($company_folders_tpl,$company_dir);
// 					return true;
// 				}
// 			}
// 			return false;

// 		}catch(Exception $e)
// 		{
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}
// 	}

// 	/**
// 	 * @param $name
// 	 * @return unknown_type
// 	 */
// 	protected function createCompanyFolders($name)
// 	{
// 		try
// 		{
// 			$company_folders_tpl = ROOT.
// 			DIRECTORY_SEPARATOR ."data".
// 			DIRECTORY_SEPARATOR ."templates".
// 			DIRECTORY_SEPARATOR ."company_folders";

// 			$company_dir = ROOT.
// 			DIRECTORY_SEPARATOR ."data".
// 			DIRECTORY_SEPARATOR ."companies".
// 			DIRECTORY_SEPARATOR .StreamcastMedia_Utils_Files::getAcronim($name);

// 			if(is_dir($company_dir))
// 			{
// 				$company_dir = $company_dir.DIRECTORY_SEPARATOR.$name;
// 				if(mkdir($company_dir))
// 				{
// 					StreamcastMedia_Utils_Files::recursiveCopy($company_folders_tpl,$company_dir);
// 					return true;
// 				}

// 			}else{
// 				if(mkdir($company_dir))
// 				{
// 					$company_dir = $company_dir.DIRECTORY_SEPARATOR.$name;
// 					if(mkdir($company_dir)){
// 						StreamcastMedia_Utils_Files::recursiveCopy($company_folders_tpl,$company_dir);
// 						return true;
// 					}
// 				}
// 			}
// 			return false;
// 		}catch(Exception $e)
// 		{
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}
// 	}

// 	/**
// 	 * Get Root dir of company
// 	 * @param $name
// 	 * @return unknown_type
// 	 */
// 	public function getRootDirById($id)
// 	{
// 		try
// 		{
// 			$company_dir = ROOT.
// 			DIRECTORY_SEPARATOR ."data".
// 			DIRECTORY_SEPARATOR ."companies".
// 			DIRECTORY_SEPARATOR ."company_" . $id;
// 			return scandir($company_dir);

// 		}catch(Exception $e)
// 		{
// 			return null;
// 		}
// 	}

// 	/**
// 	 * Get Root dir of company
// 	 * @param $name
// 	 * @return unknown_type
// 	 */
// 	public function getRootDirOf($name)
// 	{
// 		try
// 		{
// 			$company_dir = ROOT.
// 			DIRECTORY_SEPARATOR ."data".
// 			DIRECTORY_SEPARATOR ."companies".
// 			DIRECTORY_SEPARATOR .StreamcastMedia_Utils_Files::getAcronim($name).
// 			DIRECTORY_SEPARATOR.$name;
// 			return scandir($company_dir);

// 		}catch(Exception $e)
// 		{
// 			return null;
// 		}
// 	}

// 	/**
// 	 * @param $email
// 	 * @return unknown_type
// 	 */
// 	protected function createUserFolders($name)
// 	{
// 		try
// 		{
// 			$user_folders_tpl = ROOT.
// 			DIRECTORY_SEPARATOR ."data".
// 			DIRECTORY_SEPARATOR ."templates".
// 			DIRECTORY_SEPARATOR ."user_folders";

// 			$user_dir = ROOT.
// 			DIRECTORY_SEPARATOR ."data".
// 			DIRECTORY_SEPARATOR ."users".
// 			DIRECTORY_SEPARATOR .StreamcastMedia_Utils_Files::getAcronim($name);

// 			if(is_dir($user_dir))
// 			{
// 				$user_dir = $user_dir.DIRECTORY_SEPARATOR.$name;
// 				if(mkdir($user_dir))
// 				{
// 					StreamcastMedia_Utils_Files::recursiveCopy($user_folders_tpl,$user_dir);
// 					return true;
// 				}

// 			}else{
// 				if(mkdir($user_dir))
// 				{
// 					$user_dir = $user_dir.DIRECTORY_SEPARATOR.$name;
// 					if(mkdir($user_dir)){
// 						StreamcastMedia_Utils_Files::recursiveCopy($user_folders_tpl,$user_dir);
// 						return true;
// 					}
// 				}
// 			}
// 			return false;

// 		}catch(Exception $e)
// 		{
// 			throw new StreamcastMedia_Amf_Service_Exception($e->getMessage());
// 		}
// 	}
// }