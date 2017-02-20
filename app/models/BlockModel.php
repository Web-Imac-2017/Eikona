<?php

class BlockModel extends DBInterface{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Indique dans la BDD que le premier utilisateur fourni bloque le second.
	 * @param  int $blocker_id blocker_id
	 * @param  int $blocked_id blocked_id
	 * @param  time $time time()
	 * @return boolean	       true / false
	 */
	public function blockUser($blocker_id, $blocked_id, $time)
	{
		$stmt = $this->cnx->prepare("INSERT INTO blocked (blocked_id, blocker_id, block_time) VALUES (:blocker_id, :blocked_id, :time)");
		$stmt->execute([":blocker_id" => $blocker_id,
                        ":blocked_id" => $blocked_id,
                        ":time" => $time]);

		return ($stmt->fetchColumn() == 0) ? true : false;
	}

	/**
	 * Indique dans la BDD que le premier utilisateur fourni NE bloque PLUS le second.
	 * @param  int $blocker_id blocker_id
	 * @param  int $blocked_id blocked_id
	 * @return boolean	       true / false
	 */
	public function unblockUser($blocker_id, $blocked_id)
	{
		$stmt = $this->cnx->prepare("DELETE FROM blocked WHERE blocker_id = :blocker_id AND blocker_id = :blocked_id");
		$stmt->execute([":blocker_id" => $blocker_id,
                        ":blocked_id" => $blocked_id]);

		return ($stmt->fetchColumn() == 0) ? true : false;
	}
}
