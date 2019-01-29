<?php

	// NOTE: annotations required
	class Stream extends ArrayObject
	{
		private $data;

		// NOTE: annotations required
		public function __construct($data = array())
		{
			// NOTE: type checks (must be associative array)
			$this -> data = $data;
		}

		/**
		 * Filters pairs that match predicate
		 *
		 * @param Callable $logic ($k: String, $v: Any) -> Boolean
		 * @return Stream
		 * @throws Exception if $logic does not return boolean
		 */
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

		/**
		 * Maps pairs on logic
		 *
		 * @param Callable $logic ($k: String, $v: Any) -> Any
		 * @return Stream
		 */
		public function map($logic)
		{
			// Iterate Pairs
			foreach($this -> data as $k => $v) $this -> data[$k] = $logic($k, $v);

			// NOTE: should we check that $logic returns something or is null acceptable?

			// Return Stream
			return $this;
		}

		// NOTE: annotations required
		public function reduce($logic)
		{
			// NOTE: write this one (and the alternative where initial value is supplied)
		}

		/**
		 * Rejects pairs that do not match predicate
		 *
		 * @param Callable $logic ($k: String, $v: Any) -> Boolean
		 * @return Stream
		 * @throws Exception if $logic does not return boolean
		 */
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

		/**
		 * Converts the stream to JSON
		 *
		 * @return String of pairs as JSON
		 */
		public function toJSON()
		{
			return json_encode($this -> data);
		}

		/**
		 * Converts the stream to a map
		 *
		 * @return Array of pairs
		 */
		public function toMap()
		{
			return $this -> data;
		}

	}

	/**
	 * Creates a stream
	 *
	 * @param Array $data pairs
	 */
	function Stream($data = array())
	{
		// NOTE: $data should be typed?
		return new Stream($data);
	}

	// Execute Test
	print_r(Stream([
		'name' => 'Jamie',
		'age'  => 29,
		'lang' => ['PHP', 'Kotlin', 'Coldfusion']
	]) -> reject(function($k, $v)
	{
		return $k == 'age';
	}) -> filter(function($k, $v)
	{
		return $v == 'Jamie';
	}) -> map(function($k, $v)
	{
		return 'k = ' . $k . ', v = ' . $v;
	}) -> toMap());

?>