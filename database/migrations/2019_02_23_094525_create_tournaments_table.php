<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Tournament\Tournament;

class CreateTournamentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(Tournament::TABLE, function (Blueprint $table) {
			$table->increments('id');
			$table->string(Tournament::Name, 100);
			$table->tinyInteger(Tournament::NoOfTeams);

			$table->date(Tournament::StartDate)
				  ->nullable();
			$table->date(Tournament::EndDate)
				  ->nullable();
			$table->tinyInteger(Tournament::NoOfWeeks)
				  ->nullable();
			$table->string(Tournament::Status, 50);
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
		Schema::dropIfExists('tournaments');
	}
}
