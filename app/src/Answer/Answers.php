<?php

namespace Phpmvc\Answer;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class Answers extends \Anax\MVC\CDatabaseModel
{
	/**
     * Creates a clean comment table
     * 
     * 
     * @return void
     */
	public function setup(){
		
		$this->db->dropTableIfExists('answers')->execute();
	 
		$this->db->createTable(
			'answers',
			[
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'question_id' => ['int(255)'],
				'content' => ['text'],
				'member_id' => ['varchar(80)'],
				'timestamp' => ['int(15)'],
				'edited' => ['int(15)'],
			]
		)->execute();
	}
}