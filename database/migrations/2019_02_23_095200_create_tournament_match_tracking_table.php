<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Tournament\TournamentMatchTracking;

class CreateTournamentMatchTrackingTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(TournamentMatchTracking::TABLE, function (Blueprint $table) {
			$table->increments('id');
			$table->integer(TournamentMatchTracking::TournamentID);
			$table->integer(TournamentMatchTracking::TournamentMatchID);
			$table->integer(TournamentMatchTracking::OverNumber);
			$table->integer(TournamentMatchTracking::BallNumber);
			$table->integer(TournamentMatchTracking::BatsmanPlayerID);
			$table->integer(TournamentMatchTracking::BatsmanOtherSidePlayerID);
			$table->integer(TournamentMatchTracking::BowlingPlayerID);
			$table->boolean(TournamentMatchTracking::IsBoundary);
			$table->boolean(TournamentMatchTracking::IsFour);
			$table->boolean(TournamentMatchTracking::IsSix);
			$table->boolean(TournamentMatchTracking::IsWicket);
			$table->integer(TournamentMatchTracking::TotalRunsOnThisBall);
			$table->integer(TournamentMatchTracking::PresentScoreRuns);
			$table->integer(TournamentMatchTracking::PresentScoreWickets);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('tournament_match_tracking');
	}
}
