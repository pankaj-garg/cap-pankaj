<?php

namespace App\Libraries;


use App\Models\Tournament\Tournament;
use App\Models\Tournament\TournamentMatches;
use App\Models\Tournament\TournamentTeamGroup;

class MatchEngine
{
	private $tournament;

	private $takenTeams = [];

	/**
	 * MatchScheduler constructor.
	 *
	 * @param \App\Models\Tournament\Tournament $tournament
	 */
	public function __construct(Tournament $tournament)
	{
		$this->tournament = $tournament;
	}

	public function run()
	{
		$teams = TournamentTeamGroup::where(TournamentTeamGroup::TournamentID, $this->tournament->id)
									->get()
									->toArray();

		$groupWiseTeams = [];
		foreach($teams as $team) {
			$groupWiseTeams[$team[TournamentTeamGroup::GroupID]][] = $team[TournamentTeamGroup::TeamID];
		}

		foreach($groupWiseTeams as $groupID => $groupTeamIDList) {
			$this->takenTeams[$groupID] = [];
			$this->setCombinations($groupID, $groupTeamIDList);
		}

		// Finally...create matches..
		foreach($this->takenTeams as $groupID => $matchedTeams) {
			$week = 0;
			foreach($matchedTeams as $matchedTeam) {
				++$week;
				list($teamAId, $teamBId) = explode('-', $matchedTeam);

				TournamentMatches::create([TournamentMatches::TournamentID => $this->tournament->id,
										   TournamentMatches::GroupID      => $groupID,
										   TournamentMatches::HeadTeamID   => $teamAId,
										   TournamentMatches::TailTeamID   => $teamBId,
										   TournamentMatches::Week         => $week,
										   TournamentMatches::Status       => TournamentMatches::STATUS_PENDING]);
			}
		}
	}

	private function setCombinations($groupID, array $groupTeamIDList)
	{
		$count = count($groupTeamIDList);

		for($index = 0; $index < $count; $index++) {
			for($innerIndex = $index + 1; $innerIndex < $count; $innerIndex++) {
				$teamA = $groupTeamIDList[$index];
				$teamB = $groupTeamIDList[$innerIndex];

				if ($this->isExistingTeam($groupID, $teamA, $teamB)) {
					continue;
				}

				$this->takenTeams[$groupID][] = $teamA . '-' . $teamB;
			}
		}
	}

	/**
	 * @param int $groupID
	 * @param int $teamA
	 * @param int $teamB
	 *
	 * @return bool
	 */
	private function isExistingTeam($groupID, $teamA, $teamB)
	{
		$slug1 = $teamA . '-' . $teamB;
		$slug2 = $teamB . '-' . $teamA;

		return in_array($slug1, $this->takenTeams[$groupID]) || in_array($slug2, $this->takenTeams[$groupID]);
	}
}