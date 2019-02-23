<?php

namespace App\Models\Master;


use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
	const TABLE = 'groups';

	const ID        = 'id';
	const Name      = 'name';
	const Status    = 'status';
	const CreatedAt = 'created_at';
	const UpdatedAt = 'updated_at';

	/** @var string */
	protected $table = self::TABLE;

	/** @var bool */
	public $timestamps = true;

	/** @var array */
	protected $guarded = [];

	const STATUS_ACTIVE   = 'ACTIVE';
	const STATUS_INACTIVE = 'INACTIVE';
}