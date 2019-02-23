<?php

namespace App\Models\Master;


use Illuminate\Database\Eloquent\Model;

/**
 * @author  Pankaj Garg <garg.pankaj15@gmail.com>
 *
 * @package App\Models
 */
class TeamPlayer extends Model
{
	const TABLE = 'team_players';

	const ID          = 'id';
	const TeamID      = 'team_id';
	const PlayerName  = 'player_name';
	const PlayerType  = 'player_type'; // BOWLER | BATSMEN
	const Sequence    = 'sequence';
	const Age         = 'age';
	const Description = 'description';
	const Status      = 'status';
	const CreatedAt   = 'created_at';
	const UpdatedAt   = 'updated_at';

	/** @var string */
	protected $table = self::TABLE;

	/** @var bool */
	public $timestamps = true;

	/** @var array */
	protected $guarded = [];

	const STATUS_ACTIVE   = 'ACTIVE';
	const STATUS_INACTIVE = 'INACTIVE';

	const PLAYER_TYPE_BOWLER  = 'BOWLER';
	const PLAYER_TYPE_BATSMAN = 'BATSMAN';

	/**
	 * @param TeamPlayer $LastBatsmen
	 *
	 * @return self
	 */
	static public function getNextBatsmen(TeamPlayer $LastBatsmen)
	{
		return self::where(self::TeamID, $LastBatsmen->{self::TeamID})
				   ->where(self::Sequence, '>', $LastBatsmen->{self::Sequence})
				   ->orderBy(self::Sequence)
				   ->first();
	}

	/**
	 * @param $teamID
	 * @param $sequence
	 *
	 * @return self
	 */
	static public function getOnSequence($teamID, $sequence)
	{
		return self::where(self::TeamID, $teamID)
				   ->where(self::Sequence, $sequence)
				   ->first();
	}
}