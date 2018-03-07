<?php
class SafeMySQL
{
	private $conn;
	private $emode;
	private $exname;
	private $defaults = array(
		'host'      => 'localhost',
		'user'      => '',
		'pass'      => '',
		'db'        => '',
		'port'      => NULL,
		'socket'    => NULL,
		'pconnect'  => FALSE,
		'charset'   => 'utf8',
		'errmode'   => 'exception', //or 'error'
		'exception' => 'Exception', //Exception class name
	);
	const RESULT_ASSOC = MYSQLI_ASSOC;
	const RESULT_NUM   = MYSQLI_NUM;
	function __construct($opt = array())
	{
		$opt = array_merge($this->defaults,$opt);
		$this->emode  = $opt['errmode'];
		$this->exname = $opt['exception'];
		if (isset($opt['mysqli']))
		{
			if ($opt['mysqli'] instanceof mysqli)
			{
				$this->conn = $opt['mysqli'];
				return;
			} else {
				$this->error("mysqli option must be valid instance of mysqli class");
			}
		}
		if ($opt['pconnect'])
		{
			$opt['host'] = "p:".$opt['host'];
		}
		@$this->conn = mysqli_connect($opt['host'], $opt['user'], $opt['pass'], $opt['db'], $opt['port'], $opt['socket']);
		if ( !$this->conn )
		{
			$this->error(mysqli_connect_errno()." ".mysqli_connect_error());
		}
		mysqli_set_charset($this->conn, $opt['charset']) or $this->error(mysqli_error($this->conn));
		unset($opt); // I am paranoid
	}

	public function query()
	{	
		return $this->rawQuery($this->prepareQuery(func_get_args()));
	}

	public function free($result)
	{
		mysqli_free_result($result);
	}

	private function rawQuery($query)
	{
		$res   = mysqli_query($this->conn, $query);
		return $res;
	}
	private function prepareQuery($args)
	{
		$query = '';
		$raw   = array_shift($args);
		$array = preg_split('~(\?[nsiuap])~u',$raw,null,PREG_SPLIT_DELIM_CAPTURE);
		
		foreach ($array as $i => $part)
		{
			if ( ($i % 2) == 0 )
			{
				$query .= $part;
				continue;
			}
			$value = array_shift($args);
			switch ($part)
			{
				case '?i':
					$part = $this->escapeInt($value);
					break;
				case '?u':
					$part = $this->createSET($value);
					break;
				case '?p':
					$part = $value;
					break;
			}
			
			$query .= $part;
		}
		return $query;
	}

	private function escapeInt($value)
	{
		if ($value === NULL)
		{
			return 'NULL';
		}
		if(!is_numeric($value))
		{
			$this->error("Integer (?i) placeholder expects numeric value, ".gettype($value)." given");
			return FALSE;
		}
		if (is_float($value))
		{
			$value = number_format($value, 0, '.', ''); 
		} 
		return $value;
	}

	private function escapeString($value)
	{
		if ($value === NULL)
		{
			return 'NULL';
		}
		return	"'".mysqli_real_escape_string($this->conn,$value)."'";
	}

	private function escapeIdent($value)
	{
		if ($value)
		{
			return "`".str_replace("`","``",$value)."`";
		} else {
			$this->error("Empty value for identifier (?n) placeholder");
		}
	}


	private function createSET($data)
	{
		if (!is_array($data))
		{
			$this->error("SET (?u) placeholder expects array, ".gettype($data)." given");
			return;
		}
		if (!$data)
		{
			$this->error("Empty array for SET (?u) placeholder");
			return;
		}
		$query = $comma = '';
		foreach ($data as $key => $value)
		{
			$query .= $comma.$this->escapeIdent($key).'='.$this->escapeString($value);
			$comma  = ",";
		}
		return $query;
	}


	private function error($err)
	{
		$err  = __CLASS__.": ".$err;
		if ( $this->emode == 'error' )
		{
			$err .= ". Error initiated in ".$this->caller().", thrown";
			trigger_error($err,E_USER_ERROR);
		} else {
			throw new $this->exname($err);
		}
	}
	private function caller()
	{
		$trace  = debug_backtrace();
		$caller = '';
		foreach ($trace as $t)
		{
			if ( isset($t['class']) && $t['class'] == __CLASS__ )
			{
				$caller = $t['file']." on line ".$t['line'];
			} else {
				break;
			}
		}
		return $caller;
	}


}
