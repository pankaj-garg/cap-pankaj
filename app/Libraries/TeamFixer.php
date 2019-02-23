<?php

namespace App\Libraries;


use App\Libraries\Exceptions\InvalidInputException;
use App\Models\Master\Group;
use App\Models\Master\Team;
use App\Models\Tournament\Tournament;
use App\Models\Tournament\TournamentTeamGroup;
use Illuminate\Support\Arr;

/**
 * @author  Pankaj Garg <garg.pankaj15@gmail.com>
 *
 * @package App\Libraries
 */
class TeamFixer
{
	/* @var array */
	private $tournamentName;

	/* @var array */
	private $groupIDList;

	/* @var array */
	private $teamIDList;

	/* @var int */
	private $noOfTeamsInGroup;

	/**
	 * TeamAllocator constructor.
	 *
	 * @param string $tournamentName
	 * @param array  $groupIDList
	 * @param int    $noOfTeamsInGroup
	 * @param array  $teamIDList
	 *
	 * @throws \App\Libraries\Exceptions\InvalidInputException
	 */
	public function __construct($tournamentName, array $groupIDList, $noOfTeamsInGroup, array $teamIDList)
	{
		$tournamentName = trim($tournamentName);
		if (empty($tournamentName)) {
			throw new InvalidInputException('Tournament name cannot be empty');
		}
		$this->tournamentName = $tournamentName;

		// Validate group count and assign..
		if (count($groupIDList) < 2) {
			throw new InvalidInputException('Minimum two groups are required');
		}

		$this->groupIDList = $groupIDList;

		$groupCount    = count($groupIDList);
		$requiredTeams = $groupCount * $noOfTeamsInGroup;

		// Validate team count and assign..
		if (count($teamIDList) < $requiredTeams) {
			throw new InvalidInputException('Minimum ' . $requiredTeams . ' teams are required');
		}

		$this->teamIDList = $teamIDList;

		// Finally validate noOfTeamsInGroup..
		if ($noOfTeamsInGroup < 2 || $noOfTeamsInGroup % 2 != 0) {
			throw new InvalidInputException('Invalid no of teams in a group specified');
		}

		$this->noOfTeamsInGroup = $noOfTeamsInGroup;
	}

	public function run()
	{
		\DB::beginTransaction();

		$Tournament = $this->createTournament();

		$this->decideTeamsForGroups($Tournament);

		\DB::commit();

		return $Tournament;
	}

	/**
	 * @return Tournament
	 */
	private function createTournament()
	{
		return Tournament::create([Tournament::Name      => $this->tournamentName,
								   Tournament::Status    => Tournament::STATUS_ACTIVE,
								   Tournament::NoOfTeams => count($this->teamIDList)]);
	}

	/**
	 * @param \App\Models\Tournament\Tournament $Tournament
	 */
	private function decideTeamsForGroups(Tournament $Tournament)
	{
		$inputTeamIDList = $this->teamIDList;

		$Groups = Group::whereIn(Group::ID, $this->groupIDList)
					   ->get();

		$teams = Team::whereIn(Team::ID, $inputTeamIDList)
					 ->get()
					 ->keyBy(Team::ID)
					 ->toArray();

		$teamIndexes = array_keys($inputTeamIDList);

		foreach($Groups as $Group) {
			$selectedTeamIndexes = Arr::random($teamIndexes, $this->noOfTeamsInGroup);


			foreach($selectedTeamIndexes as $selectedTeamIndex) {
				$teamID = $inputTeamIDList[$selectedTeamIndex];

				TournamentTeamGroup::create([
												TournamentTeamGroup::TournamentID => $Tournament->id,
												TournamentTeamGroup::GroupID      => $Group->id,
												TournamentTeamGroup::TeamID       => $teams[$teamID][Team::ID],
											]);

				unset($teamIndexes[$selectedTeamIndex]);
			}
		}
	}
}