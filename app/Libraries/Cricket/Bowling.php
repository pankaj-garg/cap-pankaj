<?php

namespace App\Libraries\Cricket;


use Illuminate\Support\Arr;

/**
 * @author  Pankaj Garg <garg.pankaj15@gmail.com>
 *
 * @package App\Libraries\Cricket
 */
class Bowling
{
	/* @var int */
	private $over;

	/* @var int */
	private $ball;

	/* @var int */
	private $currentScoreRun;

	/* @var int */
	private $currentScoreWicket;

	/**
	 * Bowling constructor.
	 *
	 * @param $over
	 * @param $ball
	 * @param $currentScoreRun
	 * @param $currentScoreWicket
	 */
	public function __construct($over, $ball, $currentScoreRun, $currentScoreWicket)
	{
		$this->over               = $over;
		$this->ball               = $ball;
		$this->currentScoreRun    = $currentScoreRun;
		$this->currentScoreWicket = $currentScoreWicket;
	}

	/**
	 * @author Pankaj Garg <garg.pankaj15@gmail.com>
	 *
	 * @return string
	 */
	public function hit()
	{
		$boundaries = ['FOUR', 'SIX'];
		$wicket     = ['WICKET'];
		$singles    = ['SINGLE', 'TWO', 'THREE', 'ZERO'];

		if ($this->over % 3 == 0 && $this->ball % 3 == 0) {
			return Arr::random(array_merge($singles, $wicket, $boundaries));
		} elseif ($this->over % 4 == 0 && $this->ball % 2 == 0) {
			return Arr::random(array_merge($singles, $boundaries));
		} elseif (($this->over % 5 == 0 && $this->ball % 3 == 0) && $this->currentScoreRun > 100 && $this->currentScoreWicket < 3) {
			return Arr::random(array_merge($singles, $wicket));
		} else {
			return Arr::random($singles);
		}
	}

	/**
	 * @param $result
	 *
	 * @return array
	 */
	static public function getRunsAndStrikeChange($result)
	{
		switch ($result) {
			case 'ZERO':
				return [0, false];

			case 'SINGLE':
				return [1, true];

			case 'TWO':
				return [2, false];

			case 'THREE':
				return [3, true];

			case 'FOUR':
				return [4, false];

			case 'SIX':
				return [6, false];

			case 'WICKET':
				return [0, false];

			default:
				throw new \Exception('Unhandled cricket output:' . $result);
		}
	}
}