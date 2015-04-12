<?php

namespace Anax\MVC;
 
/**
 * Model for Users.
 *
 */
class CDatabaseModel implements \Anax\DI\IInjectionAware
{
	use \Anax\DI\TInjectable;
	
	private $source;
	
	/**
	 * Get the table name.
	 *
	 * @return string with the table name.
	 */
	public function getSource()
	{	
		if(is_null($this->source))
			return strtolower(implode('', array_slice(explode('\\', get_class($this)), -1)));
		else{
			$source = $this->source;
			$this->source = null;
			return $source;
		}
	}
	
	public function setSource($source){
		$this->source = strtolower($source);
	}
	
	/**
	 * Create new row.
	 *
	 * @param array $values key/values to save.
	 *
	 * @return boolean true or false if saving went okey.
	 */
	public function create($values)
	{
		$keys   = array_keys($values);
		$values = array_values($values);
	 
		$this->db->insert(
			$this->getSource(),
			$keys
		);
	 
		$res = $this->db->execute($values);
	 
		$this->id = $this->db->lastInsertId();
	 
		return $res;
	}
	
	/**
	 * Update row.
	 *
	 * @param array $values key/values to save.
	 *
	 * @return boolean true or false if saving went okey.
	 */
	public function update($values)
	{
		$keys   = array_keys($values);
		$values = array_values($values);
		
		// Its update, remove id and use as where-clause
		unset($keys['id']);
		$values[] = $this->id;
	 
		$this->db->update(
			$this->getSource(),
			$keys,
			"id = ?"
		);
	 
		return $this->db->execute($values);
	}
	
	/**
	 * Delete row.
	 *
	 * @param integer $id to delete.
	 *
	 * @return boolean true or false if deleting went okey.
	 */
	public function delete($id)
	{
		$this->db->delete(
			$this->getSource(),
			'id = ?'
		);
	 
		return $this->db->execute([$id]);
	}
	
	/**
	 * Find and return all.
	 *
	 * @return array
	 */
	public function findAll()
	{
		$this->db->select()
				 ->from($this->getSource());
	 
		$this->db->execute();
		$this->db->setFetchModeClass(__CLASS__);
		return $this->db->fetchAll();
	}
	
	/**
	 * Find and return specific.
	 *
	 * @return this
	 */
	public function find($id, $from=null, $where='id')
	{
		$this->db->select()
				 ->from(is_null($from) ? $this->getSource() : $from)
				 ->where($where." = ?");
	 
		$this->db->execute([$id]);
		return $this->db->fetchInto($this);
	}
	
	/**
	 * Get object properties.
	 *
	 * @return array with object properties.
	 */
	public function getProperties()
	{
		$properties = get_object_vars($this);
		unset($properties['di']);
		unset($properties['db']);
		unset($properties['source']);
	 
		return $properties;
	}
	
	/**
	 * Set object properties.
	 *
	 * @param array $properties with properties to set.
	 *
	 * @return void
	 */
	public function setProperties($properties)
	{
		// Update object with incoming values, if any
		if (!empty($properties)) {
			foreach ($properties as $key => $val) {
				$this->$key = $val;
			}
		}
	}
	
	/**
	 * Save current object/row.
	 *
	 * @param array $values key/values to save or empty to use object properties.
	 *
	 * @return boolean true or false if saving went okey.
	 */
	public function save($values = [])
	{
		$this->setProperties($values);
		$values = $this->getProperties();
		if (isset($values['id'])) {
			return $this->update($values);
		} else {
			return $this->create($values);
		}
	}
	
    /**
     * Get the table prefix set in config
     * 
     * @return string
     */
	public function getPrefix(){
		$reflection = new \ReflectionClass(get_class($this->db));
		$property = $reflection->getProperty('prefix');
		$property->setAccessible(true);
		return $property->getValue($this->db);
	}
	
	/**
     * Build a insert-query.
     *
     * @param string $table   the table name.
     * @param array  $columns to insert och key=>value with columns and values.
     * @param array  $values  to insert or empty if $columns has both columns and values.
     *
     * @return void
     */
	public function insert($table, $columns, $values = null)
	{
		$this->db->insert($table, $columns, $values);
		
		return $this;
	}
	
	/**
     * Build the from part.
     *
     * @param string $table name of table.
     *
     * @return $this
     */
    public function from($table)
    {
        $this->db->from($table);

        return $this;
    }
	
	/**
     * Build the inner join part.
     *
     * @param string $table     name of table.
     * @param string $condition to join.
     *
     * @return $this
     */
    public function join($table, $condition)
    {
       $this->db->join($table, $condition);
	   
	   return $this;
    }

    /**
     * Build the right join part.
     *
     * @param string $table     name of table.
     * @param string $condition to join.
     *
     * @return $this
     */
    public function rightJoin($table, $condition)
    {
       $this->db->rightJoin($table, $condition);
	   
	   return $this;
    }

    /**
     * Build the left join part.
     *
     * @param string $table     name of table.
     * @param string $condition to join.
     *
     * @return $this
     */
    public function leftJoin($table, $condition)
    {
        $this->db->leftJoin($table, $condition);
		
		return $this;
    }
	
	/**
	 * Build a select-query.
	 *
	 * @param string $columns which columns to select.
	 *
	 * @return $this
	 */
	public function query($columns = '*')
	{
		$this->db->select($columns)
				 ->from($this->getSource());
	 
		return $this;
	}
	
	/**
	 * Build the where part.
	 *
	 * @param string $condition for building the where part of the query.
	 *
	 * @return $this
	 */
	public function where($condition)
	{
		$this->db->where($condition);
	 
		return $this;
	}
	
	/**
	 * Build the where part.
	 *
	 * @param string $condition for building the where part of the query.
	 *
	 * @return $this
	 */
	public function andWhere($condition)
	{
		$this->db->andWhere($condition);
	 
		return $this;
	}
	
	/**
     * Build the order by part.
     *
     * @param string $condition for building the where part of the query.
     *
     * @return $this
     */
    public function orderBy($condition)
    {
		$this->db->orderBy($condition);

		return $this;
    }
	
	/**
    * Build the group by part.
    *
    * @param string $condition for building the group by part of the query.
    *
    * @return $this
    */
    public function groupBy($condition)
    {
      $this->db->groupBy($condition);

      return $this;
    }
	
	/**
     * Build the LIMIT by part.
     *
     * @param string $condition for building the LIMIT part of the query.
     *
     * @return $this
     */
    public function limit($condition)
    {
        $this->db->limit($condition);

        return $this;
    }
	
	/**
	 * Execute the query built.
	 *
	 * @param string $query custom query.
	 *
	 * @return $this
	 */
	public function execute($params = [])
	{
		$this->db->execute($this->db->getSQL(), $params);
		$this->db->setFetchModeClass(__CLASS__);
	 
		return $this->db->fetchAll();
	} 
}