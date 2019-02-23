<?php

use Illuminate\Database\Seeder;

use App\Models\Master\Group;

class GroupSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$noOfGroups = 2;

		for($index = 0; $index < $noOfGroups; $index++) {
			Group::create([
							  Group::Name   => 'Group-' . chr(65 + $index),
							  Group::Status => Group::STATUS_ACTIVE,
						  ]);
		}
	}
}
