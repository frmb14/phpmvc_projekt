<?php

namespace Anax\Reputation;
 
/**
 * Model for Users.
 *
 */
class Reputation extends \Anax\MVC\CDatabaseModel
{
	
	public function buildRepString($type, $member_id, $question_id, $answer_id = 0){
		
		$array = ['type'=> $type, 'member_id'=> $member_id, 'question_id' => $question_id, 'answer_id' => $answer_id];
		return base64_encode(json_encode($array));
		
	}
	
	public function getRep($question_id, $answer_id = 0){
		/*
			SELECT *,
			(SELECT count(*) FROM phpmvc_projekt_reputation WHERE type = '+' AND question_id = 35 AND answer_id = 12)  - (SELECT count(*) FROM phpmvc_projekt_reputation WHERE type = '-' AND question_id = 35 AND answer_id = 12) AS reputation
			FROM `phpmvc_projekt_reputation`
			GROUP BY reputation
		*/

		//Do we have any reputation?
		if(!empty($rep = $this->query("(SELECT count(*) FROM ".$this->getPrefix()."reputation WHERE type = '+' AND question_id = ? AND answer_id = ?)  - (SELECT count(*) FROM ".$this->getPrefix()."reputation WHERE type = '-' AND question_id = ? AND answer_id = ?) AS rep")
			->groupBy('rep')
			->execute([$question_id, $answer_id, $question_id, $answer_id]))
		){
			//Reputation found, return reputation value
			return $rep[0]->getProperties()['rep'];
		}
		else{
			//No reputation found, returning zero
			return 0;
		}
	}
	
	public function repGiven($question_id, $answer_id=0){
		
		if($this->users->isAuthenticated()){
			
			if(!empty($test = $this->query()
				->where('question_id = ?')
				->andWhere('answer_id = ?')
				->andWhere('rep_giver = ?')
				->execute([$question_id, $answer_id, $this->session->get('member_id')]))
			){
				return $test[0]->getProperties();
			}
			else{
				return null;
			}
		}
		else{
			return ['type' => 'disabled'];
		}
	}
	
    /**
     * Creates a clean user table
     * 
     * 
     * @return void
     */
	public function setup(){
	
		$this->db->dropTableIfExists('reputation')->execute();
	 
		$this->db->createTable(
			'reputation',
			[
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'question_id' 	=> ['int(80)'],
				'answer_id' 	=> ['int(80)'],
				'member_id'		=> ['int(80)'],
				'type'			=> ['varchar(10)'],
				'rep_giver' 	=> ['int(80)'] 
			]
		)->execute();
	}
}