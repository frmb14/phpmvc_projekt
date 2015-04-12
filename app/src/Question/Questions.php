<?php

namespace Phpmvc\Question;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class Questions extends \Anax\MVC\CDatabaseModel
{
	/**
     * Creates a clean comment table
     * 
     * 
     * @return void
     */
	public function setup(){
		
		$this->db->dropTableIfExists('questions')->execute();
	 
		$this->db->createTable(
			'questions',
			[
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'title' => ['varchar(255)'],
				'content' => ['text'],
				'email' => ['varchar(80)'],
				'member_id' => ['int(80)'],
				'timestamp' => ['int(15)'],
				'edited' => ['int(15)'],
			]
		)->execute();
		
		$this->db->dropTableIfExists('questions2tags')->execute();
	 
		$this->db->createTable(
			'questions2tags',
			[
				'comment' => ['int(255)'],
				'tag' => ['int(255)'],
			]
		)->execute();
		
		$this->db->dropTableIfExists('tags')->execute();
	 
		$this->db->createTable(
			'tags',
			[
				'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
				'tag' => ['varchar(255)', 'unique'],
			]
		)->execute();
	}

}