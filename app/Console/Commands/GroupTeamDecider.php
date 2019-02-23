<?php

namespace App\Console\Commands;


use App\Libraries\Exceptions\InvalidInputException;
use App\Libraries\TeamFixer;
use App\Models\Master\Group;
use App\Models\Master\Team;
use App\Models\Tournament\TournamentTeamGroup;
use Illuminate\Console\Command;

class GroupTeamDecider extends Command
{
	const COMMAND = 'group_team_decider';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = self::COMMAND;

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Group Team Decider';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @throws \App\Libraries\Exceptions\InvalidInputException
	 */
	public function handle()
	{
		$name = $this->ask('Please enter tournament name:');
		if (empty($name)) {
			throw new InvalidInputException('Tournament name is required');
		}

		$groupOptions = '';
		foreach(Group::all() as $group) {
			$groupOptions .= $group->{Group::Name} . ':' . $group->{Group::ID} . ' | ';
		}

		$groupOptions = substr($groupOptions, 0, -1);

		$this->info($groupOptions);

		// Ask for GroupIDs:
		$groupIDs = $this->ask('Please enter comma separated group ids (Two group ids are required)');
		if (empty($groupIDs)) {
			throw new InvalidInputException('Group IDs are required');
		}

		// Ask for number of teams in a group..
		$noOfTeamInAGroup = $this->ask('How many teams are there in each group?');

		$teamOptions = '';
		foreach(Team::all() as $team) {
			$teamOptions .= $team->{Team::Name} . ':' . $team->{Team::ID} . ' | ';
		}

		$teamOptions = substr($teamOptions, 0, -1);
		$this->info($teamOptions);

		// Ask for TeamIDs:
		$teamIDs = $this->ask('Please enter comma separated team ids');
		if (empty($groupIDs)) {
			throw new InvalidInputException('Group IDs are required');
		}

		$Tournament = (new TeamFixer($name, explode(',', $groupIDs), $noOfTeamInAGroup, explode(',', $teamIDs)))->run();

		$tournamentGroups = TournamentTeamGroup::with('group', 'team', 'tournament')
											   ->where(TournamentTeamGroup::TournamentID, $Tournament->id)
											   ->get()
											   ->toArray();

		$header = ['Tournament', 'Group', 'Team'];

		$records = [];
		foreach($tournamentGroups as $tournamentGroup) {
			$records[] = [
				'Tournament' => $tournamentGroup['tournament']['name'],
				'Group'      => $tournamentGroup['group']['name'],
				'Team'       => $tournamentGroup['team']['name'],
			];
		}

		$this->table($header, $records);

		$this->info('Teams divided in groups successfully through draw process');
	}
}
