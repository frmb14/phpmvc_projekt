<?php

namespace Phpmvc\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class Comments extends \Anax\MVC\CDatabaseModel
{
	/**
     * Creates a clean comment table
     * 
     * 
     * @return void
     */
	public function setup(){
		
		$this->db->dropTableIfExists('comments')->execute();
	 
		$this->db->createTable(
			'comments',
			[
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'question_id' => ['varchar(255)'],
				'answer_id' => ['varchar(255)'],
				'content' => ['text'],
				'member_id' => ['int(80)'],
				'timestamp' => ['int(15)'],
				'edited' => ['int(15)'],
			]
		)->execute();
	}
}