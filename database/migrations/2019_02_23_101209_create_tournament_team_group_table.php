<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Tournament\TournamentTeamGroup;

class CreateTournamentTeamGroupTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(TournamentTeamGroup::TABLE, function (Blueprint $table) {
			$table->increments('id');
			$table->integer(TournamentTeamGroup::TournamentID);
			$table->integer(TournamentTeamGroup::GroupID);
			$table->integer(TournamentTeamGroup::TeamID);
			$table->timestamps();

			$table->unique([TournamentTeamGroup::TournamentID, TournamentTeamGroup::GroupID, TournamentTeamGroup::TeamID]);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('tournament_team_group');
	}
}
