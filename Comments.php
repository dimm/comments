<?php
namespace dimm\comments;

use dimm\comments\Interfaces\IRepository;
use dimm\comments\Interfaces\IAccessControl;
use dimm\comments\Interfaces\IEntity;
use dimm\comments\Interfaces\ITree;
use dimm\comments\Interfaces\IFilter;
use dimm\comments\Interfaces\IValidator;
/**
 * @author dimm <dimm.mds@gmail.com>
 */
class Comments
{

	/**
	 * Validation, save etc. errrors
	 * @var array
	 */
	protected $errors;
	
	/**
	 * The handler that provides access control
	 * @var class inplements IAccessControl
	 */
	protected $accessControl;

	/**
	 * The repository handler that provides CRUD operations
	 * @var class implements IAccessControl
	 */
	protected $repository;

	/**
	 * The handler that provides validation of comment data
	 * @var class implements IValidator
	 */
	protected $validator;
	/**
	 * Comments filters (censor, links etc.)
	 * @var array
	 */
	protected $filters = [];

	protected $tree = 'dimm\comments\Base\BTree';
	protected $entity = 'dimm\comments\Base\BEntity';


	/**
	 * Comment entities tree
	 * @var ITree
	 */
	protected $_tree;

	public function __construct(IRepository $repository){
		$this->repository = $repository;
	}
	/**
	 * Add optional AccessControl component
	 * @param IAccessControl $accessControl
	 */
	public function setHandlerAceessControl(IAccessControl $accessControl){
		$this->accessControl = $accessControl;
	}

	/**
	 * Set custom Tree handler
	 * @param ITree $tree
	 */
	public function setHandlerTree(ITree $tree){
		$this->tree = $tree;
	}

	/**
	 * Set custom Entity (DTO object)
	 * @param IEntity $entity
	 */
	public function setHandlerEntity(IEntity $entity){
		$this->entity = $entity;
	}

	/**
	 * Adds data filter components
	 * @param IFilter $filter
	 */
	public function setHandlerFilter(IFilter $filter){
		$this->filters[] = $filter;
	}

	/**
	 * Set data validator components
	 * @param IValidator $validator [description]
	 */
	public function setHandlerValidator(IValidator $validator){
		$this->validator = $validator;
	}


	/**
	 * Returns a tree 
	 * @param  string $key chain key
	 * @return IBranch
	 */
	public function getTree($key)
	{
		if (!$this->_tree){
			$raw = $this->repository->getAll(['key'=>$key]);
			$tree = [];
			foreach ($raw as $item){
				$tree[] = $this->toEnity($item);
			}
			$this->_tree = $this->toTree($tree)->get();
		}
		return $this->_tree;
	}

	/**
	 * Check access control for action
	 * @param  const  $action Constant of action
	 * @param  IEntity $entity 
	 * @return bool
	 */
	public function can($action, IEntity $entity)
	{
		if ($this->accessControl)
			return $this->accessControl->can($action, $entity);
		throw new \Exception('AccessControl not configured');
	}

	/**
	 * Add comment to repository
	 * @param array $data 
	 * @return bool
	 */
	public function add(array $data)
	{
		$to_store = $this->getPreparedToStore($data);
		if ($to_store === false)
			return false;

		return $this->repository->create($to_store);
	}

	/**
	 * Update existing comment in repository
	 * @param  array  $data 
	 * @return bool
	 */
	public function update(array $data){
		$to_store = $this->getPreparedToStore($data);
		if ($to_store === false)
			return false;
		return $this->repository->update($to_store );
	}

	/**
	 * Prepare raw data for repository before add or update. Executes filters, validators
	 * @param  array $data 
	 * @return array | false
	 */
	protected function getPreparedToStore($data){
		if ($this->filters){
			foreach($this->filters as $filter){
				$filter->getFiltred($data);
			}
		}
		if ($this->validator){
			$this->validator->validate($data);
			if ($this->validator->hasErrors()){
				$this->errors['validator'] = $this->validator->getErrors();
				return false;
			}
		}
		return $data;
	}

	/**
	 * Return existing errors
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors();
	}

	/**
	 * Converts entity data to array
	 * @param  array
	 * @return IEntry
	 */
	protected function toArray(IEntity $entity, $set_null = false)
	{
		$array = [];
		foreach ($entity->getAttributes() as $attr){
			if ($entity->$attr !== null || $set_null)
				$array[$attr] = $entity->$attr;
		}
		return $array;
	}

	/**
	 * Converts raw data from array to entity
	 * @param  array
	 * @return IEntry
	 */
	protected function toEnity($raw)
	{
		$entity = new $this->entity;
		foreach ($entity->getAttributes() as $attr){
			if (isset($raw[$attr]))
				$entity->$attr = $raw[$attr];
		}
		return $entity;
	}
	/**
	 * Converts array of raw chain entities to tree
	 * @param  array
	 * @return ITree
	 */
	protected function toTree(array $entities)
	{
		return new $this->tree($entities);
	}
}