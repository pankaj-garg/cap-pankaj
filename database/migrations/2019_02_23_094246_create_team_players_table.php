<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Master\TeamPlayer;

class CreateTeamPlayersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(TeamPlayer::TABLE, function (Blueprint $table) {
			$table->increments('id');
			$table->integer(TeamPlayer::TeamID);
			$table->string(TeamPlayer::PlayerName, 100);
			$table->string(TeamPlayer::PlayerType, 50);
			$table->tinyInteger(TeamPlayer::Age)
				  ->nullable();
			$table->tinyInteger(TeamPlayer::Description)
				  ->nullable();
			$table->tinyInteger(TeamPlayer::Sequence);
			$table->string(TeamPlayer::Status, 50);
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
	}
}
