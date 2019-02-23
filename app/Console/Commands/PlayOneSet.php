<?php

namespace App\Console\Commands;


use App\Libraries\Cricket\CricketEngine;
use App\Libraries\Exceptions\InvalidInputException;
use App\Models\Tournament\Tournament;
use App\Models\Tournament\TournamentMatches;
use Illuminate\Console\Command;

class PlayOneSet extends Command
{
	const COMMAND = 'play_one_set';

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
	protected $description = 'Generate group draw';

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
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$name = $this->ask('Please enter tournament name');
		if (empty($name)) {
			throw new InvalidInputException('Tournament name is required');
		}

		$Tournament = Tournament::where(Tournament::Name, $name)
								->first();

		if (empty($Tournament)) {
			$this->error('No tournament found');

			return;
		}

		// Print Matches Table..
		$matches = TournamentMatches::with('head_team', 'tail_team', 'group')
									->where(TournamentMatches::TournamentID, $Tournament->id)
									->orderBy(TournamentMatches::GroupID)
									->get()
									->toArray();

		$header = ['ID', 'Week', 'Group', 'Head Team', 'Tail Team', 'Status'];
		$data   = [];

		foreach($matches as $record) {
			$data[] = [
				'ID'       => $record[TournamentMatches::ID],
				'Week'     => $record[TournamentMatches::Week],
				'Group'    => $record['group']['name'],
				'HeadTeam' => $record['head_team']['name'],
				'TailTeam' => $record['tail_team']['name'],
				'Status'   => $record['status'],
			];
		}

		$this->table($header, $data);

		// Ask for Match ID..
		$matchID = $this->ask('Please enter tournament match ID to play:');

		$Match = TournamentMatches::find($matchID);

		$output = (new CricketEngine($Match, $this))->run();
	}
}
