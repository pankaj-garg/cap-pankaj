<?php

namespace App\Models\Tournament;


use App\Models\Master\Group;
use App\Models\Master\Team;
use Illuminate\Database\Eloquent\Model;

class TournamentTeamGroup extends Model
{
	const TABLE = 'tournament_team_groups';

	const ID           = 'id';
	const TournamentID = 'tournament_id';
	const GroupID      = 'group_id';
	const TeamID       = 'team_id';
	const CreatedAt    = 'created_at';
	const UpdatedAt    = 'updated_at';

	/** @var string */
	protected $table = self::TABLE;

	/** @var bool */
	public $timestamps = true;

	/** @var array */
	protected $guarded = [];

	const STATUS_ACTIVE   = 'ACTIVE';
	const STATUS_INACTIVE = 'INACTIVE';

	public function group()
	{
		return $this->hasOne(Group::class, Group::ID, self::GroupID);
	}

	public function team()
	{
		return $this->hasOne(Team::class, Team::ID, self::TeamID);
	}

	public function tournament()
	{
		return $this->hasOne(Tournament::class, Tournament::ID, self::TournamentID);
	}
}