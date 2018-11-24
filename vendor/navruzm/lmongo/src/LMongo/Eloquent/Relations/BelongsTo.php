<?php namespace LMongo\Eloquent\Relations;

use MongoID;
use LMongo\Eloquent\Model;
use LMongo\Eloquent\Builder;
use LMongo\Eloquent\Collection;

class BelongsTo extends Relation {

	/**
	 * The foreign key of the parent model.
	 *
	 * @var string
	 */
	protected $foreignKey;

	/**
	 * The name of the relationship.
	 *
	 * @var string
	 */
	protected $relation;

	/**
	 * Create a new has many relationship instance.
	 *
	 * @param  \LMongo\Eloquent\Builder  $query
	 * @param  \LMongo\Eloquent\Model  $parent
	 * @param  string  $foreignKey
	 * @param  string  $relation
	 * @return void
	 */
	public function __construct(Builder $query, Model $parent, $foreignKey, $relation)
	{
		$this->relation = $relation;
		$this->foreignKey = $foreignKey;

		parent::__construct($query, $parent);
	}

	/**
	 * Get the results of the relationship.
	 *
	 * @return mixed
	 */
	public function getResults()
	{
		return $this->query->first();
	}

	/**
	 * Set the base constraints on the relation query.
	 *
	 * @return void
	 */
	public function addConstraints()
	{
		// For belongs to relationships, which are essentially the inverse of has one
		// or has many relationships, we need to actually query on the primary key
		// of the related models matching on the foreign key that's on a parent.
		$key = $this->related->getKeyName();

		$this->query->where($key, new MongoID((string) $this->parent->{$this->foreignKey}));
	}

	/**
	 * Set the constraints for an eager load of the relation.
	 *
	 * @param  array  $models
	 * @return void
	 */
	public function addEagerConstraints(array $models)
	{
		// We'll grab the primary key name of the related models since it could be set to
		// a non-standard name and not "id". We will then construct the constraint for
		// our eagerly loading query so it returns the proper models from execution.
		$key = $this->related->getKeyName();

		$value = $this->getEagerModelKeys($models);

		$value = array_map(function($value)
		{
			return ($value instanceof MongoID) ? $value : new MongoID($value);
		}, $value);

		$this->query->whereIn($key, $value);
	}

	/**
	 * Gather the keys from an array of related models.
	 *
	 * @param  array  $models
	 * @return array
	 */
	protected function getEagerModelKeys(array $models)
	{
		$keys = array();

		// First we need to gather all of the keys from the parent models so we know what
		// to query for via the eager loading query. We will add them to an array then
		// execute a "where in" statement to gather up all of those related records.
		foreach ($models as $model)
		{
			if ( ! is_null($value = $model->{$this->foreignKey}))
			{
				$keys[] = $value;
			}
		}

		// If there are no keys that were not null we will just return an array with 0 in
		// it so the query doesn't fail, but will not return any results, which should
		// be what this developer is expecting in a case where this happens to them.
		if (count($keys) == 0)
		{
			return array(0);
		}

		return array_values(array_unique($keys));
	}

	/**
	 * Initialize the relation on a set of models.
	 *
	 * @param  array   $models
	 * @param  string  $relation
	 * @return void
	 */
	public function initRelation(array $models, $relation)
	{
		foreach ($models as $model)
		{
			$model->setRelation($relation, null);
		}

		return $models;
	}

	/**
	 * Match the eagerly loaded results to their parents.
	 *
	 * @param  array   $models
	 * @param  \LMongo\Eloquent\Collection  $results
	 * @param  string  $relation
	 * @return array
	 */
	public function match(array $models, Collection $results, $relation)
	{
		$foreign = $this->foreignKey;

		// First we will get to build a dictionary of the child models by their primary
		// key of the relationship, then we can easily match the children back onto
		// the parents using that dictionary and the primary key of the children.
		$dictionary = array();

		foreach ($results as $result)
		{
			$dictionary[$result->getKey()] = $result;
		}

		// Once we have the dictionary constructed, we can loop through all the parents
		// and match back onto their children using these keys of the dictionary and
		// the primary key of the children to map them onto the correct instances.
		foreach ($models as $model)
		{
			if (isset($dictionary[(string) $model->$foreign]))
			{
				$model->setRelation($relation, $dictionary[(string)$model->$foreign]);
			}
		}

		return $models;
	}

	/**
	 * Associate the model instance to the given parent.
	 *
	 * @param  \LMongo\Eloquent\Model  $model
	 * @return \LMongo\Eloquent\Model
	 */
	public function associate(Model $model)
	{
		$this->parent->setAttribute($this->foreignKey, $model->getKey());

		return $this->parent->setRelation($this->relation, $model);
	}

	/**
	 * Update the parent model on the relationship.
	 *
	 * @param  array  $attributes
	 * @return mixed
	 */
	public function update(array $attributes)
	{
		$instance = $this->getResults();

		return $instance->fill($attributes)->save();
	}

	/**
	 * Get the foreign key of the relationship.
	 *
	 * @return string
	 */
	public function getForeignKey()
	{
		return $this->foreignKey;
	}

}