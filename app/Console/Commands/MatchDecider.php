<?php

namespace App\Console\Commands;


use App\Libraries\Exceptions\InvalidInputException;
use App\Libraries\MatchEngine;
use App\Models\Tournament\TournamentMatches;
use Illuminate\Console\Command;
use App\Models\Tournament\Tournament;

class MatchDecider extends Command
{
	const COMMAND = 'match_decider';

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


		(new MatchEngine($Tournament))->run();

		$matches = TournamentMatches::with('head_team', 'tail_team', 'group')
									->where(TournamentMatches::TournamentID, $Tournament->id)
									->orderBy(TournamentMatches::GroupID)
									->get()
									->toArray();

		$header = ['Week', 'Group', 'Head Team', 'Tail Team'];
		$data   = [];

		foreach($matches as $record) {
			$data[] = [
				'Week'     => $record[TournamentMatches::Week],
				'Group'    => $record['group']['name'],
				'HeadTeam' => $record['head_team']['name'],
				'TailTeam' => $record['tail_team']['name'],
			];
		}

		$this->table($header, $data);
		$this->info('Matches has been decided automatically by the draw system!');
	}
}
