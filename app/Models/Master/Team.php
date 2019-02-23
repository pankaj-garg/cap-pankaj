<?php

namespace App\Models\Master;


use Illuminate\Database\Eloquent\Model;

/**
 * @author  Pankaj Garg <garg.pankaj15@gmail.com>
 *
 * @package App\Models
 */
class Team extends Model
{
	const TABLE = 'teams';

	const ID        = 'id';
	const Name      = 'name';
	const FlagIcon  = 'flag_icon';
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