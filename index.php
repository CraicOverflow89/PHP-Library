<?php

	/*
		write fold, reduce and window methods
		finish annotations
		type checks and exceptions
		return types for methods
		separate files into index, Stream and test
		should the Stream class work for both standard and associative arrays (method overloading to support both)?
	*/

	// NOTE: annotations required
	class Stream extends ArrayObject
	{
		// NOTE: annotations here?
		private $data;

		// NOTE: annotations required
		public function __construct(Array $data = array())
		{
			// NOTE: check for associative array?
			$this -> data = $data;
		}

		/**
		 * Filters pairs that match predicate
		 *
		 * @param Callable $logic ($k: String, $v: Any) -> Boolean
		 * @return Stream
		 * @throws Exception if $logic does not return boolean
		 */
		public function filter(Callable $logic) : Stream
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
		 * Folds the stream into a single value
		 *
		 * @param Any $initial value to start
		 * @param Callable $logic ($result: Any, $k: String, $v: Any) -> Any
		 * @return Any
		 */
		public function fold(Any $initial, Callable $logic) : Any
		{
			// Define Result
			$result = $initial;

			// Iterate Pairs
			foreach($this -> data as $k => $v) $result = $logic($result, $k, $v);

			// NOTE: should we type check the return against initial?
			//       we're not bothering with $result: Any<T> for now

			// Return Result
			return $result;
		}

		/**
		 * Maps pairs on logic
		 *
		 * @param Callable $logic ($k: String, $v: Any) -> Any
		 * @return Stream
		 */
		public function map(Callable $logic) : Stream
		{
			// Iterate Pairs
			foreach($this -> data as $k => $v) $this -> data[$k] = $logic($k, $v);

			// NOTE: should we check that $logic returns something or is null acceptable?

			// Return Stream
			return $this;
		}

		/**
		 * Reduces the stream into a single value
		 *
		 * @param Callable $logic ($result: Any, $k: String, $v: Any) -> Any
		 * @return Any
		 */
		public function reduce(Callable $logic) : Any
		{
			// Define Result
			$result = null;

			// Iterate Pairs
			foreach($this -> data as $k => $v) $result = $logic($result, $k, $v);

			// Return Result
			return $result;
		}

		/**
		 * Rejects pairs that do not match predicate
		 *
		 * @param Callable $logic ($k: String, $v: Any) -> Boolean
		 * @return Stream
		 * @throws Exception if $logic does not return boolean
		 */
		public function reject(Callable $logic) : Stream
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
		public function toJSON() : String
		{
			// NOTE: this annotation obviously only works for associate arrays
			return json_encode($this -> data);
		}

		/**
		 * Converts the stream to a map
		 *
		 * @return Array of pairs
		 */
		public function toMap() : Array
		{
			// NOTE: this annotation and method name obviously only work for associate arrays
			return $this -> data;
		}

	}

	/**
	 * Creates a stream
	 *
	 * @param Array $data pairs
	 * @return Stream
	 */
	function Stream(Array $data = array()) : Stream
	{
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