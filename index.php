<?php

	// NOTE: annotations required
	class Stream extends ArrayObject
	{
	    private $data;

		// NOTE: annotations required
		public function __construct($data = array())
		{
			$this -> data = $data;
		}

		// NOTE: annotations required
		public function filter($logic)
		{
			// Define Result
			$result = [];

			// Iterate Pairs
			foreach($this -> data as $k => $v)
			{
				// Invoke Predicate
				$pairInclude = $logic($k, $v);

				// Invalid Return
				if(!is_bool($pairInclude)) throw new \Exception('Logic must return boolean.');

				// Include Pair
				if($pairInclude) $result[$k] = $v;
			}

			// Update Data
			$this -> data = $result;

			// Return Stream
			return $this;
		}

		// NOTE: annotations required
		public function map($logic)
		{
			// Iterate Pairs
			foreach($this -> data as $k => $v) $this -> data[$k] = $logic($k, $v);

			// Return Stream
			return $this;
		}

		// NOTE: annotations required
		public function reduce($logic)
		{
			// NOTE: write this one (and the alternative where initial value is supplied)
		}

		// NOTE: annotations required
		public function reject($logic)
		{
			// Define Result
			$result = [];

			// Iterate Pairs
			foreach($this -> data as $k => $v)
			{
				// Invoke Predicate
				$pairInclude = $logic($k, $v);

				// Invalid Return
				if(!is_bool($pairInclude)) throw new \Exception('Logic must return boolean.');

				// Include Pair
				if(!$pairInclude) $result[$k] = $v;
			}

			// Update Data
			$this -> data = $result;

			// Return Stream
			return $this;
		}

	}

	// Execute Test
	$s = new Stream([
		'name' => 'Jamie',
		'age'  => 29,
		'lang' => ['PHP', 'Kotlin', 'Coldfusion']
	]);
	print_r($s -> reject(function($k, $v)
	{
		return $k == 'age';
	}) -> filter(function($k, $v)
	{
		return $v == 'Jamie';
	}) -> map(function($k, $v)
	{
		return 'k = ' . $k . ', v = ' . $v;
	}));

?>