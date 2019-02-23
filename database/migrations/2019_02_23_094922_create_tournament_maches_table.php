<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Tournament\TournamentMatches;

class CreateTournamentMachesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(TournamentMatches::TABLE, function (Blueprint $table) {
			$table->increments('id');
			$table->integer(TournamentMatches::TournamentID);
			$table->integer(TournamentMatches::GroupID);
			$table->integer(TournamentMatches::HeadTeamID);
			$table->integer(TournamentMatches::TailTeamID);
			$table->integer(TournamentMatches::Week);
			$table->integer(TournamentMatches::HeadTeamScoreRuns)
				  ->nullable();
			$table->integer(TournamentMatches::HeadTeamScoreWickets)
				  ->nullable();
			$table->integer(TournamentMatches::TailTeamScoreRuns)
				  ->nullable();
			$table->integer(TournamentMatches::TailTeamScoreWickets)
				  ->nullable();
			$table->integer(TournamentMatches::WinningTeamID)
				  ->nullable();
			$table->integer(TournamentMatches::LoosingTeamID)
				  ->nullable();
			$table->string(TournamentMatches::Status, 50);
			$table->timestamps();

			$table->unique([TournamentMatches::TournamentID, TournamentMatches::HeadTeamID, TournamentMatches::TailTeamID], 'idx_unq');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('tournament_maches');
	}
}
