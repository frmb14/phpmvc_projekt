<?php

namespace Phpmvc\Answer;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class AnswerController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
	
	/**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function initialize()
	{
		$this->answers = new \Phpmvc\Answer\Answers();
        $this->answers->setDI($this->di);
		
		$this->form = new \Mos\HTMLForm\CForm;
	}
	
    /**
     * View all comments.
     *
     * @return void
     */
    public function viewAction($index)
    {
		
		if(empty($answers = $this->answers->query('*, (SELECT acronym FROM '.$this->answers->getPrefix().'user WHERE '.$this->answers->getPrefix().'user.id = '.$this->answers->getPrefix().'answers.member_id ) AS member_name')
			->where('question_id = ?')
			->execute([$index])))
		$answers = [];
		
		$comments = $this->dispatcher->forward([
			'controller' => 'comment',
			'action'     => 'view',
			'params'	 => ['id' => $index],
		]);
		
		// Inserting reputation into the array
		$newAnswers = [];
		foreach($answers as $answer){
			$answer = $answer->getProperties();
			$rep = $this->reputation->getRep($answer['question_id'], $answer['id']);
			$merged = array_merge($answer, ['reputation' => $rep]);
			array_push($newAnswers, $merged);
		}
		//Sorting the array after amount of reputation
		usort($newAnswers, function ($item1, $item2) {
			return $item2['reputation'] - $item1['reputation'];
		});
		
        $this->views->add('answer/answers', [
            'answers' => $newAnswers,
			'comments' => $comments
        ]);
		
		$this->createAction($index);
    }

	/**
     * Add a comment.
     *
	 * @param int $id 
	 *
     * @return void
     */
    private function createAction($id)
    {	
		$userStatus=null;
		if(!$this->users->isAuthenticated())
			$userStatus = '<div class="alert alert-warning" role="alert">In order to post an answer you must <a href="'.$this->url->create('users/login').'">Login</a> or <a href="'.$this->url->create('users/register').'">Sign Up</a>.</div>';
	
        $form = $this->form->create([], [
			
			'question_id' => [
				'type'		  => 'hidden',
				'value'		  => $id,
			],
            'answer' => [
                'type'        => 'textarea',
                'label'       => '',
				'description' => '<small>Markdown Syntax is accepted</small>' . $userStatus,
				'class'		  => 'form-control',
            ],

            'submit' => [
                'type'      => 'submit',
				'value'		=> 'Post Your Answer',
				'class'		=> 'btn btn-info',
                'callback'  => function ($form) {
					
					if($this->users->isAuthenticated()){
						return $this->answers->create([
							'question_id'	=> $form->value('question_id'),
							'content'   	=> $form->value('answer'),
							'member_id'		=> $this->session->get('member_id'),
							'timestamp' 	=> time(),
							'edited'		=> null,
						]);
					}
					else{
						return false;
					}
                }
            ],
        ]);

        @$status = $form->Check();

        if($status){
			$this->response->redirect($this->url->create('question/id/'.$form->value('question_id')));
        }

        $this->views->add('default/page', [
            'title' => '<h4>Your Answer</h4>',
            'content' => $form->getHTML(['use_fieldset' => false])
        ]);

    }
	
    /**
     * Delete a comment
     * 
     * @param int $id 
     * 
     * @return redirects the user to the previous location
     */
	public function deleteAction($id){
		
		$this->answers->delete($id);
        $this->response->redirect($this->request->getPost('redirect'));
	}
	
	/**
     * Setup controller for the USERS database
     * 
     * 
     * @return void
     */
	public function setupAction(){
		
		$this->answers->setup();
	}
}