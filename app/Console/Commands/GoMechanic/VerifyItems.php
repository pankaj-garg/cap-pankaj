<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;

class VerifyItems extends Command
{
	const COMMAND = 'verify_items';

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
	protected $description = 'Verify items';

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
	 * @throws \Exception
	 */
	public function handle()
	{
		$origItems  = ["rice", "sugar", "wheat", "cheese"];
		$origPrices = [16.89, 56.92, 20.89, 345.99];
		$items      = ["rice", "cheese"];
		$prices     = [16.89, 400.89];

		$count = $this->verifyItems($origItems, $origPrices, $items, $prices);

		$this->info('Mismatch count: ' . $count);
	}

	/**
	 * @param array $origItems
	 * @param array $origPrices
	 * @param array $items
	 * @param array $prices
	 *
	 * @return int|void
	 * @throws \Exception
	 */
	public function verifyItems(array $origItems, array $origPrices, array $items, array $prices)
	{
		$n  = count($origItems);
		$n1 = count($origPrices);
		$m  = count($items);
		$m1 = count($prices);

		$validation = $this->validateCount($n, $n1, $m, $m1);
		if (!empty($validation['error'])) {
			$this->error($validation['message'] ?? 'Invalid arguments');

			return;
		}

		$originalPriceMap = array_flip($origItems);

		$mismatchCount = 0;

		foreach($items as $index => $itemName) {
			if (!isset($originalPriceMap[$itemName]) || !isset($origPrices[$originalPriceMap[$itemName]])) {
				throw new \Exception('Price not defined for item ' . $itemName);
			}

			$originalPrice = $origPrices[$originalPriceMap[$itemName]];

			if (abs($originalPrice - $prices[$index]) > 0) {
				++$mismatchCount;
			}
		}

		return $mismatchCount;
	}

	/**
	 * @param int $n  Count of origItems
	 * @param int $n1 Count of origPrices
	 * @param int $m  Count of items
	 * @param int $m1 Count of prices
	 *
	 * @return array
	 */
	private function validateCount($n, $n1, $m, $m1)
	{
		// Validate $n
		if ($n < 1 || $n > 1000000) {
			return ['error' => true, 'message' => 'Invalid count of origItems'];
		}

		if ($n != $n1) {
			return ['error' => true, 'message' => 'Count of origItems and origPrices should be same'];
		}

		// Validate $m
		if ($m < 1 || $m > $n) {
			return ['error' => true, 'message' => 'Invalid count of items, should be greater than 0 and less than count of origItems'];
		}

		if ($m != $m1) {
			return ['error' => true, 'message' => 'Count of items and prices should be same'];
		}

		return ['error' => false, 'message' => ''];
	}
}
