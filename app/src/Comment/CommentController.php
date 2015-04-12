<?php

namespace Phpmvc\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
	
	/**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function initialize()
	{
		$this->comments = new \Phpmvc\Comment\Comments();
        $this->comments->setDI($this->di);
		
		$this->form = new \Mos\HTMLForm\CForm;
	}
	
    /**
     * View all comments.
     *
     * @return void
     */
    public function viewAction($id)
    {
		
		if(empty($comments = $this->comments->query('*, (SELECT acronym FROM '.$this->comments->getPrefix().'user WHERE '.$this->comments->getPrefix().'user.id = '.$this->comments->getPrefix().'comments.member_id ) AS member_name')->where('question_id = ?')->orderBy('timestamp DESC')->execute([$id])))
			$comments = [];
		
		return $comments;
    }
	
	/**
     * Add a comment.
     *
	 * @param int $id 
	 *
     * @return void
     */
    public function createAction($qId, $aId=null)
    {	
	
		if($this->users->isAuthenticated()){
		
			$form = $this->form->create([], [
				
				'question_id' => [
					'type'		  => 'hidden',
					'value'		  => $qId,
				],
				'answer_id'	 => [
					'type'		  => 'hidden',
					'value'		  => $aId,
				],
				'comment' => [
					'type'        => 'textarea',
					'label'       => '',
					'validation'  => ['not_empty'],
					'class'		  => 'form-control',
				],

				'submit' => [
					'type'      => 'submit',
					'value'		=> 'Post Your Comment',
					'class'		=> 'btn btn-info',
					'callback'  => function ($form) {
					
						return $this->comments->create([
							'question_id'	=> $form->value('question_id'),
							'answer_id'		=> $form->value('answer_id'),
							'content'   	=> $form->value('comment'),
							'member_id' 	=> $this->session->get('member_id'),
							'timestamp' 	=> time(),
							'edited'		=> null,
						]);
					}
				],
			]);

			@$status = $form->Check();

			if($status){
				$this->response->redirect($this->url->create('question/id/'.$form->value('question_id')));
			}

			return $form->getHTML(['use_fieldset' => false]);
		}
		else{
			return '<p><div class="alert alert-warning" role="alert">In order to post a comment you must <a href="'.$this->url->create('users/login').'">Login</a> or <a href="'.$this->url->create('users/register').'">Sign Up</a>.</div></p>';
		}

    }
	
	/**
     * Setup controller for the USERS database
     * 
     * 
     * @return void
     */
	public function setupAction(){
		
		$this->comments->setup();
	}
}