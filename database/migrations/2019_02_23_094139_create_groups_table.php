<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Master\Group;

class CreateGroupsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(Group::TABLE, function (Blueprint $table) {
			$table->increments('id');
			$table->string(Group::Name, 100);
			$table->string(Group::Status, 50);
			$table->timestamps();

			$table->unique(Group::Name);
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
