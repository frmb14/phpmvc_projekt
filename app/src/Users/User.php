<?php

namespace Anax\Users;
 
/**
 * Model for Users.
 *
 */
class User extends \Anax\MVC\CDatabaseModel
{
	
    /**
     * Returns true if the acronym sent is unique else false
     * 
     * @param <string> $acronym 
     * 
     * @return bool
     */
	public function isAcronymAvailable($acronym){
		$this->db->select()
		         ->from($this->getSource())
			     ->where("acronym = ?");

		$this->db->execute([$acronym]);
		return empty($this->db->fetchInto($this)) ? true : false;
	} 
	
	public function login($validation=null){
		
		$username = $this->request->getPost('username');
		$password = $this->request->getPost('password');
		
		$member = $this->find($username, 'user', 'acronym');
		if(!empty($member)){
			$member = $member->getProperties();
			
			if(password_verify($password, $member['password'])){
				$this->session->set('member_id', $member['id']);
				$this->session->set('member_loggedIn', true);
				return true;
			}
			else{
				return false;
			}
			
		}
		else{
			return false;
		}
	}
	
	/**
	 * Function to check if the user is logged in
	 *
	 * @return bool if the user is logged in.
	 */
	public function isAuthenticated(){
		if($this->session->get('member_loggedIn')){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function userNavbar($isAuthenticated=false, $memberId=null){
		
		$nav = [];
		if($isAuthenticated){
			$nav = [ 
				'myaccount' => [
					'text'  => 'My Account',
					'url'   => $this->url->create('users/id/'.$memberId),
					'title' => 'My Account'
				],
				'logout' => [
					'text'	=> 'Sign Out',
					'url'	=> $this->url->create('users/logout'),
					'title'	=> 'Logout'
				],
			];
		}
		else{
			$nav = [ 
				'login' => [
					'text'	=> 'Login',
					'url'	=> $this->url->create('users/login'),
					'title'	=> 'Login',
				]
			];
		}
		
		return $nav;
		
	}
	
    /**
     * Creates a clean user table
     * 
     * 
     * @return void
     */
	public function setup(){
	
		$this->db->dropTableIfExists('user')->execute();
	 
		$this->db->createTable(
			'user',
			[
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'acronym' => ['varchar(20)', 'unique', 'not null'],
				'email' => ['varchar(80)'],
				'password' => ['varchar(255)'],
				'created' => ['datetime'],
				'updated' => ['datetime'],
				'deleted' => ['datetime'],
				'active' => ['datetime'],
			]
		)->execute();
		
		$this->db->insert(
			'user',
			['acronym', 'email', 'password', 'created', 'active']
		);
	 
		$now = gmdate('Y-m-d H:i:s');
	 
		$this->db->execute([
			'admin',
			'admin@dbwebb.se',
			password_hash('admin', PASSWORD_DEFAULT),
			$now,
			$now
		]);
	}
}