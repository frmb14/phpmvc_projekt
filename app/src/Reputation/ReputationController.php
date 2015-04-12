<?php

namespace Anax\Reputation;

class ReputationController implements \Anax\DI\IInjectionAware 
{
    use \Anax\DI\TInjectable;
	

	public function initialize()
	{
		$this->rep = new \Anax\Reputation\Reputation();
		$this->rep->setDI($this->di);
		
	}
	
	public function repAction($encoded){
		
		$decoded = base64_decode($encoded);
		$array = json_decode($decoded, TRUE);
		if($this->users->isAuthenticated()){
			
			$given = $this->rep->repGiven($array['question_id'], $array['answer_id']);
			if(is_null($given)){
				$this->rep->create([
					'question_id' 	=> $array['question_id'],
					'answer_id' 	=> $array['answer_id'],
					'member_id'		=> $array['member_id'],
					'type'			=> $array['type'],
					'rep_giver'		=> $this->session->get('member_id')
				]);
			}
			else{
				if($array['type'] != $given['type']){
					$this->rep->delete($given['id']);
					$this->rep->create([
						'question_id' 	=> $array['question_id'],
						'answer_id' 	=> $array['answer_id'],
						'member_id'		=> $array['member_id'],
						'type'			=> $array['type'],
						'rep_giver'		=> $this->session->get('member_id')
					]);
				}
			}
			
		}
		
		$this->response->redirect($this->url->create('question/id/'.$array['question_id']));
	}

	public function setupAction(){
		$this->rep->setup();
	}
}