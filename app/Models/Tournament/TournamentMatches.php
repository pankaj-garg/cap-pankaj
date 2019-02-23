<?php

namespace App\Models\Tournament;


use App\Models\Master\Group;
use App\Models\Master\Team;
use Illuminate\Database\Eloquent\Model;

class TournamentMatches extends Model
{
	const TABLE = 'tournament_matches';

	const ID           = 'id';
	const TournamentID = 'tournament_id';
	const GroupID      = 'group_id';
	const HeadTeamID   = 'head_team_id';
	const TailTeamID   = 'tail_team_id';
	const Week         = 'week';

	const HeadTeamScoreRuns    = 'head_team_score_runs';
	const HeadTeamScoreWickets = 'head_team_score_wickets';
	const TailTeamScoreRuns    = 'tail_team_score_runs';
	const TailTeamScoreWickets = 'tail_team_score_wickets';

	const WinningTeamID = 'winning_team_id';
	const LoosingTeamID = 'loosing_team_id';

	const Status    = 'status'; // PENDING | RUNNING | COMPLETED
	const CreatedAt = 'created_at';
	const UpdatedAt = 'updated_at';

	/** @var string */
	protected $table = self::TABLE;

	/** @var bool */
	public $timestamps = true;

	/** @var array */
	protected $guarded = [];

	const STATUS_PENDING   = 'PENDING';
	const STATUS_RUNNING   = 'RUNNING';
	const STATUS_COMPLETED = 'COMPLETED';

	public function head_team()
	{
		return $this->hasOne(Team::class, Team::ID, self::HeadTeamID);
	}

	public function tail_team()
	{
		return $this->hasOne(Team::class, Team::ID, self::TailTeamID);
	}

	public function group()
	{
		return $this->hasOne(Group::class, Group::ID, self::GroupID);
	}
}