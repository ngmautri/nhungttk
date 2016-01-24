<?php

namespace User\Model;

/**
 *
 * @author nmt
 *        
 */
class User {
	public $id;
	public $title;
	public $firstname;
	public $lastname;
	public $password;
	public $salt;
	public $email;
	public $role;
	public $registration_key;
	public $confirmed = 0;
	public $register_date;
	public $lastvisit_date;
	public $block;
	public $isSuperAdmin = false;
	public $isSuperEditor = false;
	public $isCompanyAdmin = false;
	public function exchangeArray($data) {
		$this->id = (! empty ( $data ['id'] )) ? $data ['id'] : null;
		$this->title = (! empty ( $data ['title'] )) ? $data ['title'] : null;
		$this->firstname = (! empty ( $data ['firstname'] )) ? $data ['firstname'] : null;
		$this->lastname = (! empty ( $data ['lastname'] )) ? $data ['lastname'] : null;
		$this->salt = (! empty ( $data ['salt'] )) ? $data ['salt'] : null;
		$this->email = (! empty ( $data ['email'] )) ? $data ['email'] : null;
		$this->role = (! empty ( $data ['role'] )) ? $data ['role'] : null;
		$this->registration_key = (! empty ( $data ['registration_key'] )) ? $data ['registration_key'] : null;
		$this->confirmed = (! empty ( $data ['confirmed'] )) ? $data ['confirmed'] : null;
		$this->register_date = (! empty ( $data ['register_date'] )) ? $data ['register_date'] : null;
		$this->block = (! empty ( $data ['block'] )) ? $data ['block'] : null;
		$this->isSuperAdmin = (! empty ( $data ['isSuperAdmin'] )) ? $data ['isSuperAdmin'] : null;
		$this->isSuperEditor = (! empty ( $data ['isSuperEditor'] )) ? $data ['isSuperEditor'] : null;
		$this->isCompanyAdmin = (! empty ( $data ['isCompanyAdmin'] )) ? $data ['isCompanyAdmin'] : null;
	}
}

