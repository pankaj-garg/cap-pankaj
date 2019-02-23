<?php

use Illuminate\Database\Seeder;

use App\Models\Master\Team;
use App\Models\Master\TeamPlayer;

class TeamSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$noOfTotalTeams = 8;

		for($index = 0; $index < $noOfTotalTeams; $index++) {
			$Team = Team::create([
									 Team::Name   => 'Team-' . chr(65 + $index),
									 Team::Status => Team::STATUS_ACTIVE,
								 ]);

			$players = [];

			// Let's seed players as well..
			for($playerIndex = 1; $playerIndex <= 11; $playerIndex++) {

				$players[] = [TeamPlayer::TeamID     => $Team->{Team::ID},
							  TeamPlayer::PlayerName => $Team->{Team::Name} . '-Player-' . $playerIndex,
							  TeamPlayer::PlayerType => self::getPlayerType($playerIndex),
							  TeamPlayer::Sequence   => $playerIndex,
							  TeamPlayer::Age        => rand(20, 35),
							  TeamPlayer::Status     => TeamPlayer::STATUS_ACTIVE,
							  TeamPlayer::CreatedAt  => \Carbon\Carbon::now(),
							  TeamPlayer::UpdatedAt  => \Carbon\Carbon::now()];
			}

			TeamPlayer::insert($players);
		}
	}

	static private function getPlayerType($index)
	{
		if ($index < 6) {
			return 'BATSMEN';
		} else {
			return 'BOWLER';
		}
	}
}
