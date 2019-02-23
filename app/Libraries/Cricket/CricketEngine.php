<?php

namespace App\Libraries\Cricket;


use App\Models\Master\Team;
use App\Models\Tournament\TournamentMatches;
use App\Models\Master\TeamPlayer;
use App\Models\Tournament\TournamentMatchTracking;
use Illuminate\Support\Arr;

/**
 * Class CricketEngine
 *
 * @package App\Libraries\Cricket
 */
class CricketEngine
{
	/* @var TournamentMatches */
	private $Match;

	/* @var integer */
	private $currentScoreRuns;

	/* @var integer */
	private $currentScoreWickets;

	/* @var TeamPlayer */
	private $RunningBatsManOnStrike;

	/* @var TeamPlayer */
	private $RunningBatsManOnOtherSide;

	/* @var TeamPlayer */
	private $LastBatsMan;

	/* @var TeamPlayer */
	private $RunningBowler;

	/* @var integer */
	private $firstBattingTeamFinalScoreRuns;

	/* @var \Illuminate\Console\Command */
	private $Console;

	/**
	 * CricketEngine constructor.
	 *
	 * @param \App\Models\Tournament\TournamentMatches $TournamentMatch
	 */
	public function __construct(TournamentMatches $TournamentMatch, $Console)
	{
		$this->Match   = $TournamentMatch;
		$this->Console = $Console;
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function run()
	{
		$battingTeamID = $this->Match->{TournamentMatches::HeadTeamID};
		$bowlingTeamID = $this->Match->{TournamentMatches::TailTeamID};

		// Play first inning..
		$this->playInning($battingTeamID, $bowlingTeamID);

		// Let's store the final score..
		$this->firstBattingTeamFinalScoreRuns = $this->currentScoreRuns;
		$this->Match->update([TournamentMatches::HeadTeamScoreRuns    => $this->currentScoreRuns,
							  TournamentMatches::HeadTeamScoreWickets => $this->currentScoreWickets]);

		$BattingTeam = Team::find($battingTeamID);
		$BowlingTeam = Team::find($bowlingTeamID);

		// Console logs..
		$this->Console->error("==========================INNING COMPLETED=======================");
		$this->Console->info($BattingTeam->name . ' scored ' . $this->currentScoreRuns . '/' . $this->currentScoreWickets);
		$this->Console->error("==========================Second Inning Started=======================");

		// Play second inning..
		$this->playInning($bowlingTeamID, $battingTeamID, true);

		$this->Match->update([TournamentMatches::TailTeamScoreRuns    => $this->currentScoreRuns,
							  TournamentMatches::TailTeamScoreWickets => $this->currentScoreWickets]);

		$this->Console->error("==========================SECOND INNING COMPLETED=======================");
		$this->Console->info($BowlingTeam->name . ' scored ' . $this->currentScoreRuns . '/' . $this->currentScoreWickets);
		$this->Console->error("==========================Decision in progress =======================");

		return $this->decision();
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	private function decision()
	{
		// Decision..
		// If Head Team Scored more...winner is head team..looser is tail team..
		if ($this->Match->{TournamentMatches::HeadTeamScoreRuns} > $this->Match->{TournamentMatches::TailTeamScoreRuns}) {
			$winnerTeamID = $this->Match->{TournamentMatches::HeadTeamID};
			$looserTeamID = $this->Match->{TournamentMatches::TailTeamID};
		} // Otherwise..vice versa..
		elseif ($this->Match->{TournamentMatches::TailTeamScoreRuns} > $this->Match->{TournamentMatches::HeadTeamScoreRuns}) {
			$winnerTeamID = $this->Match->{TournamentMatches::TailTeamID};
			$looserTeamID = $this->Match->{TournamentMatches::HeadTeamID};
		} else {
			throw new \Exception('Match draw');
		}

		$this->Match->update([TournamentMatches::Status        => TournamentMatches::STATUS_COMPLETED,
							  TournamentMatches::WinningTeamID => $winnerTeamID,
							  TournamentMatches::LoosingTeamID => $looserTeamID]);

		$WinningTeam = Team::find($winnerTeamID);
		$LoosingTeam = Team::find($looserTeamID);

		$this->Console->error("=========================='.$WinningTeam->name.' won over '.$LoosingTeam->name.'=======================");

		return ['winning_team' => $WinningTeam,
				'loosing_team' => $LoosingTeam];
	}

	private function playInning($battingTeamID, $bowlingTeamID, $isSecondInning = false)
	{
		$totalOvers     = config('cap.max_overs');
		$ballsInOneOver = config('cap.balls_in_one_over');

		// Set current score 0-0
		$this->currentScoreRuns    = 0;
		$this->currentScoreWickets = 0;

		// Get First and Second Batsmen for batting..
		$this->RunningBatsManOnStrike = TeamPlayer::getOnSequence($battingTeamID, 1);
		$this->LastBatsMan            = $this->RunningBatsManOnOtherSide = TeamPlayer::getOnSequence($battingTeamID, 2);

		$Bowlers = TeamPlayer::where(TeamPlayer::TeamID, $bowlingTeamID)
							 ->where(TeamPlayer::PlayerType, TeamPlayer::PLAYER_TYPE_BOWLER)
							 ->get()
							 ->toArray();

		for($over = 1; $over <= $totalOvers; $over++) {
			// Let the system decide the bowler..
			$this->RunningBowler = Arr::random($Bowlers);

			// Looping for 6 balls for an over..
			for($index = 1; $index <= $ballsInOneOver; $index++) {
				// Let's hit the iterated index ball..and wait for some random result..
				$result = (new Bowling($over, $index, $this->currentScoreRuns, $this->currentScoreWickets))->hit();

				list($runs, $strikeChange) = Bowling::getRunsAndStrikeChange($result);

				// It could be a WICKET or some runs..
				$wicket = false;
				if ($result == 'WICKET') {
					++$this->currentScoreWickets;
					$runs         = 0;
					$strikeChange = false;
					$wicket       = true;
				}

				// Increase the score by runs..
				$this->currentScoreRuns += $runs;

				// Add the tracking record to data store and update running positions..
				$this->track($over, $index, $result, $runs, $strikeChange, $wicket);

				$this->Console->info('Running for ' . $over . ' OVER ' . $index . 'Ball [Result: ' . $result . '] [Runs: ' . $runs . '], [Score:' . $this->currentScoreRuns . ']');

				// If all wickets down...then stop the match..
				if ($this->currentScoreWickets >= 10
					|| ($isSecondInning && $this->currentScoreRuns > $this->firstBattingTeamFinalScoreRuns)) {
					break 2;
				}
			}
		}
	}

	/**
	 * @author Pankaj Garg <garg.pankaj15@gmail.com>
	 *
	 * @param integer $over
	 * @param integer $ball
	 * @param string  $result
	 * @param integer $runs
	 * @param boolean $isStrikeChange
	 * @param boolean $isWicket
	 */
	private function track($over, $ball, $result, $runs, $isStrikeChange, $isWicket)
	{
		TournamentMatchTracking::create([TournamentMatchTracking::TournamentID             => $this->Match->{TournamentMatches::TournamentID},
										 TournamentMatchTracking::TournamentMatchID        => $this->Match->{TournamentMatches::ID},
										 TournamentMatchTracking::OverNumber               => $over,
										 TournamentMatchTracking::BallNumber               => $ball,
										 TournamentMatchTracking::BatsmanPlayerID          => $this->RunningBatsManOnStrike->id,
										 TournamentMatchTracking::BatsmanOtherSidePlayerID => $this->RunningBatsManOnOtherSide->id,
										 TournamentMatchTracking::BowlingPlayerID          => $this->RunningBowler['id'],
										 TournamentMatchTracking::IsBoundary               => in_array($result, ['FOUR', 'SIX']),
										 TournamentMatchTracking::IsFour                   => $result == 'FOUR',
										 TournamentMatchTracking::IsSix                    => $result == 'SIX',
										 TournamentMatchTracking::IsWicket                 => $result == 'WICKET',
										 TournamentMatchTracking::TotalRunsOnThisBall      => $runs,
										 TournamentMatchTracking::PresentScoreRuns         => $this->currentScoreRuns,
										 TournamentMatchTracking::PresentScoreWickets      => $this->currentScoreWickets,
										]);

		// Get new batsmen for wicket..
		if ($isWicket) {
			$this->RunningBatsManOnStrike = TeamPlayer::getNextBatsmen($this->RunningBatsManOnStrike);
		}// Change the batsmen..
		elseif ($isStrikeChange) {
			$temp                            = $this->RunningBatsManOnStrike;
			$this->RunningBatsManOnStrike    = $this->RunningBatsManOnOtherSide;
			$this->RunningBatsManOnOtherSide = $temp;
		}
	}
}