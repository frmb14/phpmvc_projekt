<?php

namespace Phpmvc\Question;

class QuestionController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
	
	private $questionId;
	
	/**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function initialize()
	{
		$this->questions = new \Phpmvc\Question\Questions();
        $this->questions->setDI($this->di);
		
		$this->form = new \Mos\HTMLForm\CForm;
	}
	
    /**
     * View all questions.
     *
     * @return void
     */
    public function viewAction($view='homequestions')
    {
		
		if(empty($questions = $this->questions->query($this->questions->getPrefix().'questions.*, GROUP_CONCAT(T.tag) AS tags, LOWER( CONCAT( CONCAT( '.$this->questions->getPrefix().'questions.id, "-" ) , REPLACE( '.$this->questions->getPrefix().'questions.title, " ", "-" ) ) ) AS url, (SELECT acronym FROM '.$this->questions->getPrefix().'user WHERE '.$this->questions->getPrefix().'user.id = '.$this->questions->getPrefix().'questions.member_id ) AS member_name')
			->leftJoin('questions2tags AS C2T', $this->questions->getPrefix().'questions.id = C2T.comment')
			->join('tags AS T', 'T.id = C2T.tag')
			->groupBy($this->questions->getPrefix().'questions.id')
			->orderBy('timestamp DESC')
			->limit('30')
			->execute()))
		$questions = '<i>No questions made..</i>';
		
        $this->views->add('questions/'.$view, [
            'questions' => $questions,
        ]);
    }
	
    /**
     * View all questions.
     *
     * @return void
     */
    public function idAction($id=null)
    {	
	
		!is_null($id) or die("Invalid url");
		
		$id = explode('-', $id)[0];
		
		$this->theme->setTitle($this->questions->find($id)->getProperties()['title']);
		
		if(empty($question = $this->questions->query($this->questions->getPrefix().'questions.*, GROUP_CONCAT(T.tag) AS tags, (SELECT acronym FROM '.$this->questions->getPrefix().'user WHERE '.$this->questions->getPrefix().'user.id = '.$this->questions->getPrefix().'questions.member_id ) AS member_name')
			->leftJoin('questions2tags AS C2T', $this->questions->getPrefix().'questions.id = C2T.comment')
			->join('tags AS T', 'T.id = C2T.tag')
			->where($this->questions->getPrefix().'questions.id = ?')
			->groupBy($this->questions->getPrefix().'questions.id')
			->execute([$id])))
		$question = '<i>No questions made..</i>';
		
		$comments = $this->dispatcher->forward([
			'controller' => 'comment',
			'action'     => 'view',
			'params'	 => ['qId' => $id],
		]);
		
		$this->dispatcher->forward([
			'controller' => 'comment',
			'action'     => 'create',
			'params'	 => ['id' => $id],
		]);
		
        $this->views->add('questions/question', [
            'questions' => $question,
			'comments'	=> $comments,
        ]);
		
		$this->dispatcher->forward([
			'controller' => 'answer',
			'action'     => 'view',
			'params'	 => ['index' => $id],
		]);
    }
	
    /**
     * View the most used tags
     * 
     * @return void
     */
	public function popularTagsAction(){
		
		if(empty($tags = $this->questions->query('tags.tag, count( * ) AS Used')
			->from('questions2tags AS C2T')
			->join('tags AS tags', 'tags.id = C2T.tag')
			->groupBy('tags.tag')
			->orderBy('count( * ) DESC')
			->limit('5')
			->execute()))
		$tags = '<i>No tags</i>';
		
        $this->views->add('questions/populartags', [
			'title'	=> null,
            'tags' => $tags,
        ], 'sidebar');
	}
	
	public function tagsAction(){
		
		if(empty($tags = $this->questions->query('tags.tag, count( * ) AS Used')
			->from('questions2tags AS C2T')
			->join('tags AS tags', 'tags.id = C2T.tag')
			->groupBy('tags.tag')
			->orderBy('count( * ) DESC')
			->execute()))
		$tags = '<i>No tags</i>';
		
        $this->views->add('questions/tags', [
			'title'	=> null,
            'tags' => $tags,
        ]);
	}
	
	public function taggedAction($tag){
		
		$this->theme->setTitle("Tag ".$tag);
		
		$tagId = $this->questions->find($tag, 'tags', 'tag');
		if($tagId){
			$tagId = $tagId->getProperties()['id'];	
			
			if(empty($questions = $this->questions->query($this->questions->getPrefix().'questions.*, GROUP_CONCAT(T.tag) AS tags, LOWER( CONCAT( CONCAT( '.$this->questions->getPrefix().'questions.id, "-" ) , REPLACE( '.$this->questions->getPrefix().'questions.title, " ", "-" ) ) ) AS url, (SELECT acronym FROM '.$this->questions->getPrefix().'user WHERE '.$this->questions->getPrefix().'user.id = '.$this->questions->getPrefix().'questions.member_id ) AS member_name')
				->leftJoin('questions2tags AS C2T', $this->questions->getPrefix().'questions.id = C2T.comment')
				->join('tags AS T', 'T.id = C2T.tag')
				->where('T.id = ?')
				->groupBy($this->questions->getPrefix().'questions.id')
				->orderBy('timestamp DESC')
				->execute([$tagId])))
				$questions = '<i>No questions found with the tag <span class="label label-info">'.$tag.'</span></i>';
			
			$this->views->add('questions/questions', [
				'questions' => $questions,
			]);
		}
		else{
			$this->views->add('default/page', [
				'title'	  => 'Wooops!',
				'content' => '<h3>The tag <span class="label label-info">'.$tag.'</span> does not exist!</h3>',
			]);
		}
		
	}
	
	public function countAnswerAction($id){
		
		$countAnswers = $this->questions->query('count( id ) AS answers')
			->from('answers')
			->where('question_id = ?')
			->execute([$id]);
		
		return $answer = $countAnswers[0]->getProperties()['answers'];
	}
	
	/**
     * Add a comment.
     *
	 * @param int $id 
	 *
     * @return void
     */
    public function createAction()
    {	
	
		if($this->users->isAuthenticated()){
			$questionId = null;
			$form = $this->form->create([], [
				
				'title' => [
					'type'        => 'text',
					'required'    => true,
					'validation'  => ['not_empty'],
					'label'		  => 'Title',
					'placeholder' => 'What is your question? Be specific.',
					'class'		  => 'form-control',
				], 
				
				'question' => [
					'type'        => 'textarea',
					'label'       => 'Question',
					'required'    => true,
					'validation'  => ['not_empty'],
					'description' => '<small>Markdown Syntax is accepted</small>',
					'class'		  => 'form-control',
				],
				
				'tags' => [
					'type'        => 'text',
					'required'    => true,
					'validation'  => ['not_empty'],
					'label'		  => 'Tags',
					'placeholder' => 'Stats, SimCraft',
					'description' => '<small>At least 1 tag such as <span class="label label-info">Stats</span>, <span class="label label-info">SimCraft</span> separate with comma. Max 5 tags.</small>',
					'class'		  => 'form-control',
				], 

				'submit' => [
					'type'      => 'submit',
					'value'		=> 'Post your question',
					'class'		=> 'btn btn-info',
					'callback'  => function ($form) {
						
						$this->questions->create([
							'title'		=> strip_tags($form->value('title')),
							'content'   => $form->value('question'),
							'member_id' => $this->session->get('member_id'),
							'timestamp' => time(),
							'edited'	=> null,
						]);
						
						$this->questionId = $this->db->lastInsertId();
						
						$tags = explode(',', preg_replace('/\s+/','',$form->value('tags')));
						foreach($tags as $key => $val){
							if($key == 5) break;
							//Insert possibly new tags
							if(!($existingTag = $this->questions->find($val, 'tags', 'tag'))){
								$this->questions->setSource('tags');
								$this->questions->create([
									'tag' => $val,
								]);
								$tagId = $this->db->lastInsertId();
							}
							else{
								$tagId = $existingTag->getProperties()['id'];
							}
							
							$this->questions->setSource('questions2tags');
							$this->questions->create([
								'comment' => $this->questionId,
								'tag'	  => $tagId,
							]);
						}
						
						return true;
					}
				],
			]);

			@$status = $form->Check();

			if($status){
				$this->response->redirect($this->url->create('question/id/'.$this->questionId));
			}
			
			$this->theme->setTitle("Ask Question");
			$this->views->add('default/page', [
				'title' => 'Ask A Question',
				'content' => $form->getHTML(['use_fieldset' => false])
			]);
			
		}
		else{
			$this->theme->setTitle("Please login or sign up");
			$this->views->add('default/page', [
				'title' => '<h3>You must be logged in to ask a question</h3>',
				'content' => '<p><a href="'.$this->url->create('users/login').'">Login</a> or <a href="'.$this->url->create('users/register').'">Sign Up</a></p>'
			]);
		}

    }
	
    /**
     * Delete a comment
     * 
     * @param int $id 
     * 
     * @return redirects the user to the previous location
     */
	public function deleteAction($id){
		
		$this->questions->delete($id);
        $this->response->redirect($this->request->getPost('redirect'));
	}
	
	/**
     * Setup controller for the USERS database
     * 
     * 
     * @return void
     */
	public function setupAction(){
		
		$this->theme->setTitle("Setup database");
		
		$this->questions->setup();
	}
}