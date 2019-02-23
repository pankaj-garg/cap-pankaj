<?php

namespace App\Models\Tournament;


use Illuminate\Database\Eloquent\Model;

class TournamentMatchTracking extends Model
{
	const TABLE = 'tournament_match_tracking';

	const ID                       = 'id';
	const TournamentID             = 'tournament_id';
	const TournamentMatchID        = 'tournament_match_id';
	const OverNumber               = 'over_number';
	const BallNumber               = 'ball_number';
	const BatsmanPlayerID          = 'batsman_player_id';
	const BatsmanOtherSidePlayerID = 'batsman_other_side_player_id';
	const BowlingPlayerID          = 'bowling_player_id';
	const IsBoundary               = 'is_boundary';
	const IsFour                   = 'is_four';
	const IsSix                    = 'is_six';
	const IsWicket                 = 'is_wicket';
	const TotalRunsOnThisBall      = 'total_runs_on_this_ball';
	const PresentScoreRuns         = 'present_score_runs';
	const PresentScoreWickets      = 'present_score_wickets';
	const CreatedAt                = 'created_at';
	const UpdatedAt                = 'updated_at';

	/** @var string */
	protected $table = self::TABLE;

	/** @var bool */
	public $timestamps = true;

	/** @var array */
	protected $guarded = [];
}