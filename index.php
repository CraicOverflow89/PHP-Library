<?php

	/*
		write asIterable and window methods
		finish annotations
		type checks and exceptions
		separate files into index, Stream and test
		should the Stream class work for both standard and associative arrays (method overloading to support both)?
		would sequence be a better name for this?
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
		 * Determines if all pairs match a predicate
		 *
		 * @param Callable $logic ($k: String, $v: Any) -> Boolean
		 * @return Boolean
		 * @throws Exception if $logic does not return boolean
		 */
		public function all(Callable $logic) : Boolean
		{
			// Iterate Pairs
			foreach($this -> data as $k => $v)
			{
				// Invoke Predicate
				$pairMatch = $logic($k, $v);

				// Invalid Return
				if(!is_bool($pairMatch)) throw new \Exception('Logic must return boolean.');

				// Match Failure
				if(!$pairMatch) return false;
			}

			// Match Success
			return true;
		}

		/**
		 * Determines if any pairs match a predicate
		 *
		 * @param Callable $logic ($k: String, $v: Any) -> Boolean
		 * @return Boolean
		 * @throws Exception if $logic does not return boolean
		 */
		public function any(Callable $logic) : Boolean
		{
			// NOTE: could make $logic optional (without simply returns size > 0)

			// Iterate Pairs
			foreach($this -> data as $k => $v)
			{
				// Invoke Predicate
				$pairMatch = $logic($k, $v);

				// Invalid Return
				if(!is_bool($pairMatch)) throw new \Exception('Logic must return boolean.');

				// Match Success
				if($pairMatch) return true;
			}

			// Match Failure
			return false;
		}

		/**
		 * Creates an array of streams of max size
		 *
		 * @param Int $size maximum Stream size
		 * @return Array<Stream>
		 * @throws Exception if $size is fewer than one
		 */
		public function chunked(Int $size) : Array
		{
			// Validate Size
			if($size < 1) throw new \Exception('Size must be at least one.');

			// Define Result
			$result = [[]];

			// Iterate Pairs
			$pos = 0;
			foreach($this -> data as $k => $v)
			{
				// Next Chunk
				if(count($result[$pos]) == $size)
				{
					$pos ++;
					$result[$pos] = [];
				}

				// Append Pair
				$result[$pos][$k] = $v;
			}

			// Return Result
			return $result;
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
		 * Performs logic against pairs
		 *
		 * @param Callable $logic ($k: String, $v: Any)
		 */
		public function forEach(Callable $logic)
		{
			// Iterate Pairs
			foreach($this -> data as $k => $v) $logic($k, $v);
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
		 * Determines if no pairs match a predicate
		 *
		 * @param Callable $logic ($k: String, $v: Any) -> Boolean
		 * @return Boolean
		 * @throws Exception if $logic does not return boolean
		 */
		public function none(Callable $logic) : Boolean
		{
			// Iterate Pairs
			foreach($this -> data as $k => $v)
			{
				// Invoke Predicate
				$pairMatch = $logic($k, $v);

				// Invalid Return
				if(!is_bool($pairMatch)) throw new \Exception('Logic must return boolean.');

				// Match Failure
				if($pairMatch) return false;
			}

			// Match Success
			return true;
		}

		/**
		 * Performs logic against pairs and returns stream
		 *
		 * @param Callable $logic ($k: String, $v: Any)
		 * @return Stream
		 */
		public function onEach(Callable $logic)
		{
			// Iterate Pairs
			foreach($this -> data as $k => $v) $logic($k, $v);

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

	// Test 1
	/*print_r(Stream([
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
	}) -> toMap());*/

	// Test 2
	print_r(Stream([
		'name' => 'Jamie',
		'age'  => 29,
		'lang' => ['PHP', 'Kotlin', 'Coldfusion']
	]) -> chunked(2));

?>