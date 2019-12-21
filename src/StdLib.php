<?php
	namespace StdLib;

	/**
	 * Determines if a string is valid JSON
	 *
	 * @param String $value the string to check
	 * @return Boolean
	 */
	function isJSON($value) {
		json_decode($value);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	/**
	 * Pair
	 *
	 * @author craicoverflow89
	 */
	final class Pair {

		/** @var any $first */
		public $first;

		/** @var any $second */
		public $second;

		/**
		 * Constructs a Pair
		 *
		 * @param Any $first value
		 * @param Any $second value
		 * @return Pair
		 */
		public function __construct($first, $second) {
			$this -> first = $first;
			$this -> second = $second;
		}

	}

	/**
	 * Creates a Pair
	 *
	 * @param Any $first value
	 * @param Any $second value
	 * @return Pair
	 */
	function Pair($first, $second) : Pair {
		return new Pair($first, $second);
	}

	/**
	 * Pair
	 *
	 * @author craicoverflow89
	 */
	class Stream extends \ArrayObject {

		/** @var array $data */
		private $data;

		/**
		 * Constructs a Stream
		 *
		 * @param Array $data pairs
		 * @throws Exception if $data is not an associative array of key/value pairs
		 * @return Stream
		 */
		public function __construct(Array $data = array()) {

			// Invalid Array
			if(array_keys($data) === range(0, count($data) - 1)) throw new \Exception('Data must contains key/value pairs.');

			// Create Stream
			$this -> data = $data;
		}

		/**
		 * Adds a pair to the stream
		 *
		 * @param String $key the key
		 * @param Any $value the value
		 * @return Stream
		 */
		public function add(string $key, $value) : Stream {

			// Append Pair
			$this -> data[$key] = $value;

			// Return Stream
			return $this;
		}

		/**
		 * Determines if all pairs match a predicate
		 *
		 * @param Callable $logic ($k: String, $v: Any) -> Boolean
		 * @return Boolean
		 * @throws Exception if $logic does not return boolean
		 */
		public function all(Callable $logic) : bool {

			// Iterate Pairs
			foreach($this -> data as $k => $v) {

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
		 * @param Callable $logic ($k: String, $v: Any) -> Boolean | null
		 * @return Boolean
		 * @throws Exception if $logic does not return boolean
		 */
		public function any(Callable $logic = null) : bool {

			// No Logic
			if($logic == null) return !!count($this -> data);

			// Iterate Pairs
			foreach($this -> data as $k => $v) {

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
		 * Creates a generator for the stream
		 *
		 * @return Array [isDone: Boolean, next: Any]
		 */
		public function asIterable() : Array {

			// Instantiate Position
			$pos = 0;

			// Return Proxy
			return [
				'isDone' => function() use ($pos) {

					// Return Completion
					return $pos > count($this -> data) - 1;
				},
				'next' => function() use ($pos) {

					// Invalid Position
					if($pos > count($this -> data)) throw new \Exception('Generator has reached end of stream.');

					// Increment Position
					$pos ++;

					// Return Data
					return $this -> data[$pos];
				}
			];
		}

		/**
		 * Creates an array of streams of max size
		 *
		 * @param Int $size maximum Stream size
		 * @return Array<Stream>
		 * @throws Exception if $size is fewer than one
		 */
		public function chunked(Int $size) : Array {

			// Validate Size
			if($size < 1) throw new \Exception('Size must be at least one.');

			// Define Result
			$result = [[]];

			// Iterate Pairs
			$pos = 0;
			foreach($this -> data as $k => $v) {

				// Next Chunk
				if(count($result[$pos]) == $size) {
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
		public function filter(Callable $logic) : Stream {

			// Define Result
			$result = [];

			// Iterate Pairs
			foreach($this -> data as $k => $v) {

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
		public function fold(Any $initial, Callable $logic) : Any {

			// Define Result
			$result = $initial;

			// Iterate Pairs
			foreach($this -> data as $k => $v) $result = $logic($result, $k, $v);

			// Return Result
			return $result;
		}

		/**
		 * Performs logic against pairs
		 *
		 * @param Callable $logic ($k: String, $v: Any)
		 */
		public function forEach(Callable $logic) {

			// Iterate Pairs
			foreach($this -> data as $k => $v) $logic($k, $v);
		}

		/**
		 * Maps pairs on logic
		 *
		 * @param Callable $logic ($k: String, $v: Any) -> Any
		 * @return Stream
		 */
		public function map(Callable $logic) : Stream {

			// Iterate Pairs
			foreach($this -> data as $k => $v) $this -> data[$k] = $logic($k, $v);

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
		public function none(Callable $logic) : bool {

			// Iterate Pairs
			foreach($this -> data as $k => $v) {

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
		 * Overrides method for array get syntactic sugar
		 *
		 * @param Any $index the key (string)
		 * @return Any
		 * @throws Exception if $index does not exist as a key in the stream
		 */
		public function offsetGet($index) {

			// Invalid Key
			if(!array_key_exists($index, $this -> data)) throw new \Exception('Key does not exist in the stream.');

			// Return Value
			return $this -> data[$index];
		}

		/**
		 * Overrides method for array add syntactic sugar
		 *
		 * @param Any $index the key (string)
		 * @param Any $newval the value
		 * @return Stream
		 */
		public function offsetSet($index, $newval) : Stream {

			// Invoke Add
			return $this -> add($index, $newval);
		}

		/**
		 * Performs logic against pairs and returns stream
		 *
		 * @param Callable $logic ($k: String, $v: Any)
		 * @return Stream
		 */
		public function onEach(Callable $logic) {

			// Iterate Pairs
			foreach($this -> data as $k => $v) $logic($k, $v);

			// Return Stream
			return $this;
		}

		public function partition(Callable $logic) : Pair {

			// Define Results
			$result = [
				"first" => [],
				"second" => []
			];

			// Iterate Pairs
			foreach($this -> data as $k => $v) {

				// Invoke Predicate
				$pairPartition = $logic($k, $v);

				// Invalid Return
				if(!is_bool($pairPartition)) throw new \Exception('Logic must return boolean.');

				// Include Pair
				$result[$pairPartition === true ? "first" : "second"][$k] = $v;
			}

			// Return Result
			return Pair($result["first"], $result["second"]);
		}

		/**
		 * Reduces the stream into a single value
		 *
		 * @param Callable $logic ($result: Any, $k: String, $v: Any) -> Any
		 * @return Any
		 */
		public function reduce(Callable $logic) : Any {

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
		public function reject(Callable $logic) : Stream {

			// Define Result
			$result = [];

			// Iterate Pairs
			foreach($this -> data as $k => $v) {

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
		 * Converts the stream to an array
		 *
		 * @return Array
		 */
		public function toArray() : Array {
			return $this -> data;
		}

		/**
		 * Converts the stream to JSON
		 *
		 * @return JSON
		 */
		public function toJSON() : String {
			return json_encode($this -> data);
		}

		/**
		 * Converts the stream to a map
		 *
		 * @return Array
		 */
		public function toMap() : Array {
			return $this -> data;
		}

	}

	/**
	 * Creates a stream
	 *
	 * @param Any $data associative array of key/value pairs or JSON string
	 * @throws Exception if $data is not an associative array of key/value pairs or valid JSON
	 * @return Stream
	 */
	function Stream($data = array()) : Stream {

		// Decode JSON
		if(isJSON($data)) $data = json_decode($data, true);

		// Return Stream
		return new Stream($data);
	}

	/**
	 * Pair
	 *
	 * @author craicoverflow89
	 */
	class Struct {

		/** @var array $dataMap */
		private $dataMap;

		/** @var array $typeData */
		private $typeData;

		/**
		 * Constructs a Struct
		 *
		 * @param Array $data pairs
		 * @return Struct
		 * @throws Exception if $dataMap contains no data
		 * @throws Exception if $dataMap contains invalid data
		 */
		public function __construct(array $dataMap) {

			// Type Logic
			$this -> typeData = (function() use ($dataMap) {

				// Validate Size
				if(!count($dataMap)) throw new \Exception('Missing struct data.');

				// Type Logic
				$typeMap = Stream([
					'boolean' => "is_bool",
					'integer' => "is_int",
					'number' => "is_numeric",
					'string' => "is_string"
				]) -> map(function($type, $method) {

					// Validation Logic
					return function($it) use ($method) {

						// Validate Type
						return $method($it);
					};
				}) -> toArray();

				// Return Proxy
				return [
					'exists' => function($type) use ($typeMap) {
						return array_key_exists($type, $typeMap);
					},
					'validate' => function($type, $value) use ($typeMap) {
						return $typeMap[$type]($value);
					}
				];
			})();
			// Do not want to perform this operation each time we construct (static?)

			// Validate Data
			$this -> dataMap = Stream($dataMap) -> onEach(function($name, $type) {

				// Invalid Type
				if(!$this -> typeData['exists']($type)) throw new \Exception('Invalid struct data.');
			}) -> toArray();
		}

		/**
		 * Validates a map
		 *
		 * @param Array $value pairs
		 * @return Boolean
		 */
		public function validate(array $value) : bool {

			// Validate Data
			return Stream($this -> dataMap) -> all(function($name, $type) use ($value, $typeData) {

				// Data Missing
				if(!array_key_exists($name, $value)) return false;

				// Validate Type
				return $this -> typeData['validate']($type, $name);
			});
		}

	}

	/**
	 * Creates a struct
	 *
	 * @param Array $dataMap pairs
	 * @return Struct
	 * @throws Exception if $dataMap contains no data
	 * @throws Exception if $dataMap contains invalid data
	 */
	function Struct(array $dataMap) : Struct {
		return new Struct($dataMap);
	}

?>