<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Master\Team;

class CreateTeamsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(Team::TABLE, function (Blueprint $table) {
			$table->increments('id');
			$table->string(Team::Name, 100);
			$table->string(Team::FlagIcon, 50)
				  ->nullable();
			$table->string(Team::Status, 50);
			$table->timestamps();

			$table->unique(Team::Name);
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
