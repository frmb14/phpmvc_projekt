<?php

namespace Anax\Users;
 
/**
 * A controller for users and admin related events.
 *
Get the user with most reputation
---------------------------------
SELECT M. * , COUNT( rep_giver ) AS rep
FROM phpmvc_projekt_reputation AS R
INNER JOIN phpmvc_projekt_user AS M ON M.id = R.member_id
GROUP BY R.member_id
ORDER BY rep DESC
LIMIT 5
 
 */
class UsersController implements \Anax\DI\IInjectionAware 
{
    use \Anax\DI\TInjectable;
	
	/**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function initialize()
	{
		$this->users = new \Anax\Users\User();
		$this->users->setDI($this->di);
		
		$this->form = new \Mos\HTMLForm\CForm;
		
	}

	public function loginAction(){
		$form = $this->form->create([], [

            'username' => [
                'type'        => 'text',
                'validation'  => ['not_empty'],
				'class'		  => 'form-control',
            ],
           
            'password' => [
                'type'        => 'password',
                'validation'  => ['not_empty' ,'custom_test' => ['message' => '<div class="alert alert-danger" role="alert">The username or password is invalid</div>', 'test' => array($this->users, 'login')]],
				'class'		  => 'form-control',
            ],
            'submit' => [
                'type'      => 'submit',
				'value'       => 'Sign In',
				'class'		=> 'btn btn-info',
                'callback'  => function ($form) {

                    $now = gmdate("Y-m-d H:i:s");

                    return $this->users->isAuthenticated();
                }
            ],
        ]);

        @$status = $form->check();

        if($status){
            $url = $this->url->create('');
            $this->response->redirect($url);
        }

        $this->theme->setTitle("Sign in");
        $this->views->add('default/page', [
            'title' => 'Sign In',
            'content' => $form->getHTML() . '<a href="'.$this->url->create('users/register').'">Not a member? Register now!</a>',
        ]);
	}
	
	/**
	 * Add new user.
	 *
	 * @param string $acronym of user to add.
	 *
	 * @return void
	 */
	public function registerAction()
	{
		$form = $this->form->create([], [
            'username' => [
                'type'        => 'text',
				'label'		  => 'Username',
                'required'    => true,
                'validation'  => ['not_empty','custom_test' => ['message' => '<div class="alert alert-danger" role="alert">That username already exists.</div>', 'test' => array($this->users, 'isAcronymAvailable')]],
				'class'		  => 'form-control',
            ],
            'email' => [
                'type'        => 'email',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
				'class'		  => 'form-control',
            ],
            'password' => [
                'type'        => 'password',
                'required'    => true,
                'validation'  => ['not_empty'],
				'class'		  => 'form-control',
            ],
			'password2' => [
                'type'        => 'password',
                'required'    => true,
				'label'		  => 'Enter Password Again',
                'validation'  => ['not_empty', 'match' => 'password'],
				'class'		  => 'form-control',
            ],
            'submit' => [
                'type'      => 'submit',
				'value'       => 'Register',
				'class'		=> 'btn btn-info',
                'callback'  => function ($form) {

                    $now = gmdate("Y-m-d H:i:s");

                    $this->users->save([
                        'acronym' => 	strip_tags($form->value('username')),
                        'email' => 		strip_tags($form->value('email')),
                        'password' => 	password_hash($form->value('password'), PASSWORD_DEFAULT),
                        'created' => 	$now,
                        'active' => 	$now,
                    ]);
					
					return $this->users->login();
                }
            ],
        ]);

        @$status = $form->check();

        if($status){
            $url = $this->url->create('users/id/' . $this->db->lastInsertId());
            $this->response->redirect($url);
        }

        $this->theme->setTitle("Registration");
        $this->views->add('default/page', [
            'title' => 'Register a new account',
            'content' => $form->getHTML()
        ]);
		
	}
	
	public function logoutAction(){
		$this->session->set('member_loggedIn', false);
		if(!$this->session->get('member_loggedIn')){
            $this->response->redirect($this->url->create(''));
		}
		else{
			$this->session->set('member_loggedIn', false);
		}
	}
	
	/**
     * View the users with most reputation
     *
     * @return void
     */
	public function highestReputationAction(){
		
		if(empty($users = $this->users->query('M.*, (SELECT count(*) FROM phpmvc_projekt_reputation WHERE type = "+" AND member_id = M.id)  - (SELECT count(*) FROM phpmvc_projekt_reputation WHERE type = "-" AND member_id = M.id) AS rep')
			->from('reputation AS R')
			->join('user AS M', 'M.id = R.member_id')
			->groupBy('R.member_id')
			->orderBy('rep DESC')
			->limit('5')
			->execute()))
		$users = '<i>No users</i>';
		
        $this->views->add('users/most-active', [
            'users' => $users,
        ], 'sidebar');
	}
	
	/**
	 * List all users.
	 *
	 * @return void
	 */
	public function listAction()
	{
	 
		$all = $this->users->findAll();
	 
		$this->theme->setTitle("View all Members");
		$this->views->add('users/list-all', [
			'users' => $all,
			'title' => "View all Members",
		]);
	}
	
	/**
	 * List user with id.
	 *
	 * @param int $id of user to display
	 *
	 * @return void
	 */
	public function idAction($id = null)
	{
		!is_null($id) or die("Invalid url");
		$id = explode('-', $id)[0];
		
		
		$user = $this->users->query('M.*, (SELECT count(*) FROM phpmvc_projekt_reputation WHERE type = "+" AND member_id = M.id)  - (SELECT count(*) FROM phpmvc_projekt_reputation WHERE type = "-" AND member_id = M.id) AS rep')
				->from('reputation AS R')
				->rightJoin('user AS M', 'M.id = R.member_id')
				->where('M.id = ?')
				->groupBy('R.member_id')
				->orderBy('rep DESC')
				->limit('5')
				->execute([$id]);
				
		$user = $user[0]->getProperties();
		
		if(empty($questions = $this->users->query()
			->from('questions')
			->where('member_id = ?')
			->limit('10')
			->orderBy('timestamp DESC')
			->execute([$id])))
		$questions = [];
		
		if(empty($answers = $this->users->query('A.*, Q.title')
			->from('answers AS A')
			->join('questions AS Q', 'Q.id = A.question_id')
			->where('A.member_id = ?')
			->limit('10')
			->orderBy('timestamp DESC')
			->execute([$id])))
		$answers = [];
		
		$this->theme->setTitle("Viewing Profile");
		$this->views->add('users/view', [
			'user' => $user,
			'questions' => $questions,
			'answers'	=> $answers
		]);
	}
	
	/**
	 * List all active and not deleted users.
	 *
	 * @return void
	 */
	public function activeAction()
	{
		$all = $this->users->query()
			->where('active IS NOT NULL')
			->andWhere('deleted is NULL')
			->execute();
	 
		$this->theme->setTitle("Active Users");
		$this->views->add('users/list-all', [
			'users' => $all,
			'title' => "Active Users",
		]);
	}
	
	/**
	 * List all deleted users.
	 *
	 * @return void
	 */
	public function InTrashbinAction()
	{
		$all = $this->users->query()
			->where('deleted IS NOT NULL')
			->execute();
	 
		$this->theme->setTitle("Trashbin");
		$this->views->add('users/list-all', [
			'users' => $all,
			'title' => "Members in Trashbin",
		]);
	}
	
	/**
	 * List all inactive
	 *
	 * @return void
	 */
	public function inactiveAction()
	{
		$all = $this->users->query()
			->where('active IS NULL')
			->execute();
	 
		$this->theme->setTitle("Inactive Members");
		$this->views->add('users/list-all', [
			'users' => $all,
			'title' => "Inactive Members",
		]);
	}
	
    /**
     * Update an existing user
     * 
     * @param int $id  
     * 
     * @return void
     */
	public function updateAction($id = null)
    {
        if (!isset($id)) {
			die("Missing id");
		}

        $user = $this->users->find($id);

        $form = $this->form->create([], [
            'id' => [
                'type'        => 'hidden',
                'value'       => base64_encode($user->id),
            ],
            'email' => [
                'type'        => 'text',
				'label'		  => 'Update your email: ',
                'validation'  => ['email_adress'],
                'value'       => $user->email,
				'class'		  => 'form-control',
            ],
			'password' => [
                'type'        => 'password',
				'label'		  => 'Update your password: ',
				'class'		  => 'form-control',
            ],
            'submit' => [
                'type'      => 'submit',
				'class'		=> 'btn btn-info',
                'callback'  => function ($form) {

                    $user = $this->users->find(base64_decode($form->value('id')));
					
					$now = gmdate("Y-m-d H:i:s");

                    return $this->users->save([
                        'email' => 		strip_tags($form->value('email')),
                        'password' => 	!empty($form->value('password')) ? password_hash($form->value('password'), PASSWORD_DEFAULT) : $user->password,
                        'updated' => 	$now,
                    ]);
                }
            ],
        ]);

        @$status = $form->Check();

        if($status){
            $url = $this->url->create('users/id/'.$user->id);
            $this->response->redirect($url);
        }


        $this->theme->setTitle("Updating Profile");
        $this->views->add('default/page', [
            'title' => $user->acronym . ' - Updating Profile',
            'content' => $form->getHTML()
        ]);
    }
	
	/**
	 * Delete user.
	 *
	 * @param integer $id of user to delete.
	 *
	 * @return void
	 */
	public function deleteAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$res = $this->users->delete($id);
	 
		$url = $this->url->create('users');
		$this->response->redirect($url);
	}
	
	/**
	 * Delete (soft) user.
	 *
	 * @param integer $id of user to delete.
	 *
	 * @return void
	 */
	public function softDeleteAction($id = null)
	{
		if (!isset($id)) {
			die("Missing id");
		}
	 
		$now = gmdate('Y-m-d H:i:s');
	 
		$user = $this->users->find($id);
	 
		$user->deleted = $now;
		$user->save();
	 
		$url = $this->url->create('users/list/');
        $this->response->redirect($url);
	}
	
	public function toggleActiveAction($id = null)
    {
        if(!$id){
            die("missing id");
		}
		
        $now = gmdate('Y-m-d H:i:s');

        $user = $this->users->find($id);

        if(!$user->active || $user->deleted){
            $user->active = $now;
            $user->deleted = null;
        }
        else{
            $user->active = null;
        }

        $user->save();

		$this->response->redirect($this->request->getPost('redirect'));
    } 
	
    /**
     * Setup controller for the USERS database
     * 
     * 
     * @return void
     */
	public function setupAction(){
		
		$this->users->setup();
	}
}
