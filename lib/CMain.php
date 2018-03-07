<?
class CMain
{
	private $db;
	
	function __construct($host, $user, $db, $pass = "")
	{
		if($host && $user && $db) {
			try {
				$settings = array(
					'host' => $host,
					'user' => $user,
					'pass' => $pass,
					'db' => $db,
					'charset' => 'utf8',
				);
				$this->db = new SafeMySQL($settings);
			} catch (Exception $e) {

				echo "Error:";
				echo $e->getMessage();
				die();
			}
		}
		else {
			echo "Error params";
			die();
		}
	}
	
	function Query($query = "")
	{
		if($query) {
			$queryRes = $this->db->query($query);
			while ($row = $queryRes->fetch_row()) {
				$result[] = $row;
			}
		}
		return ($result);
	}
	
	/** Update row in DB */
	function UpdateRecord($table, $arParams, $id)
	{
		$result = "";
		$sql = "UPDATE {$table} SET ?u WHERE ID=?i";
		$result = $this->db->query($sql, $arParams, $id);
		return ($result);
	}

	/** Delete rows from DB */
	function DeleteRecord($table, $arIDS)
	{
		$result = "";
		$ids = implode(",", $arIDS);
		$sql = "DELETE FROM {$table} WHERE ID IN({$ids})";
		$result = $this->db->query($sql);
		return ($result);
	}

		/** Add a new row to DB */
	function AddNewRecord($table, $arParams)
	{
		$result = "";
		$sql = "INSERT INTO {$table} SET ?u";
		$result = $this->db->query($sql, $arParams);
		return ($result);
	}
	
}

?>