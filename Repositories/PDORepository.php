<?php
namespace dimm\comments\Repositories;

use dimm\comments\Interfaces\IRepository;
/**
 * @author dimm.mds@gmail.com 
 */
class PDORepository implements IRepository
{
	/**
	 * Connection
	 * @var Connection string or PDO connection
	 */
	public $connection;

	public $tableName = 'comments';

	protected $_connection;

	protected function getConnection(){
		if (!$this->_connection){
			if (!$this->connection)
				throw new \Exception('PDO connection must be established');
			
			if (is_array($this->connection)){
				if (!isset($this->connection['dsn']) || !isset($this->connection['username']) || !isset($this->connection['password']))
					throw new \Exception ('Some PDO connection params (dsn, username, password) is not established');
				$this->_connection = new \PDO($this->connection['dsn'], $this->connection['username'], $this->connection['password']);
				$this->_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
			}
			else 
			{
				if (!($this->connection instanceof \PDO))
					throw new \Exception('Connection must be instanceof PDO');
				$this->_connection = $this->connection;
			}
		}

		return $this->_connection;
	}

	/**
	 * Returns data of comment
	 * @param int $id The comment primary key
	 * @return array
	 */
	public function get($id){
		$query = $this->getConnection()->prepare("SELECT * FROM {$this->tableName} WHERE id = :id LIMIT 1");
		$query->execute([':id' => $id]);
		return $query->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * Returns comments data
	 * @param  mixed $search search query. As usualy its array pair ['key'=>'some_key'] that gets comments chain
	 * @return [type]         [description]
	 */
	public function getAll(array $search){
		$query_string = "SELECT * FROM {$this->tableName} ";
		$where = '';
		if ($search){
			$where = [];
			foreach($search as $param => $val){
				$where[] = "`{$param}`= :{$param}";
			}
			$query_string .= ' WHERE '.implode(' AND ', $where);
		}
		$query = $this->getConnection()->prepare($query_string);
		$query->execute($search);
		return $query->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * Deletes the comment
	 * @param  int $id The comment primary key
	 * @return null
	 */
	public function delete($id){
		$where = ['id' => $id];
		$this->getConnection()->prepare("DELETE FROM {$this->tableName} WHERE id=:id")->execute($where);
	}

	/**
	 * Saves new comment data
	 * @param  array  $data
	 * @return bool 
	 */
	public function create(array $data){
		$set = [];
		$where = '';
		foreach ($data as $attr => $val){
			$set[] = "`{$attr}`=:$attr";
		}
		
		$query = $this->getConnection()->prepare("INSERT INTO {$this->tableName} SET ".implode(',',$set));
		$query->execute($data);
		return true;
	}

	/**
	 * Updates existing comment data
	 * @param  array  $data
	 * @return bool 
	 */
	public function update(array $data){
		unset($to_store['user_id']);
		unset($to_store['key']);
		unset($to_store['created_at']);

		$set = [];
		$where = '';
		foreach ($data as $attr => $val){
			$set[] = "`{$attr}`=:$attr";
		}
		$exists = $this->get($data['id']);
		if(!$exists)
			return false;
		
		$query = $this->getConnection()->prepare("UPDATE {$this->tableName} SET ".implode(',',$set)." WHERE id=:id");
		$query->execute($data);
		return true;

	}

}