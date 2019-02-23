<?php

namespace App\Models\Tournament;


use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
	const TABLE = 'tournaments';

	const ID            = 'id';
	const Name          = 'name';
	const NoOfTeams     = 'no_of_teams';
	const StartDate     = 'start_date';
	const EndDate       = 'end_date';
	const NoOfWeeks     = 'no_of_weeks';
	const Status        = 'status';
	const CreatedAt     = 'created_at';
	const UpdatedAt     = 'updated_at';

	/** @var string */
	protected $table = self::TABLE;

	/** @var bool */
	public $timestamps = true;

	/** @var array */
	protected $guarded = [];

	const STATUS_ACTIVE   = 'ACTIVE';
	const STATUS_INACTIVE = 'INACTIVE';
}