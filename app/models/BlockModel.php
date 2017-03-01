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
	 */
	public function blockUser($blocker_id, $blocked_id)
	{
			$stmt = $this->cnx->prepare("INSERT INTO blocked (blocker_id, blocked_id, block_time) VALUES (:blocker_id, :blocked_id, :time)");
		$stmt->execute([":blocker_id" => $blocker_id,
                        ":blocked_id" => $blocked_id,
                        ":time" => time()]);
	}

	/**
	 * Indique dans la BDD que le premier utilisateur fourni NE bloque PLUS le second.
	 * @param  int $blocker_id blocker_id
	 * @param  int $blocked_id blocked_id
	 */
	public function unblockUser($blocker_id, $blocked_id)
	{
		$stmt = $this->cnx->prepare("DELETE FROM blocked WHERE blocker_id = :blocker_id AND blocked_id = :blocked_id");
		$stmt->execute([":blocker_id" => $blocker_id,
                        ":blocked_id" => $blocked_id]);
	}

	/**
	 * Indique si le premier utilisateur fourni est en train de bloquer le second.
	 * @param  int $blocker_id blocker_id
	 * @param  int $blocked_id blocked_id
	 * @return boolean	       true / false
	 */
	public function isBlocking($blocker_id, $blocked_id)
	{
		$stmt = $this->cnx->prepare("SELECT COUNT(*) FROM blocked WHERE blocker_id = :blocker_id AND blocked_id = :blocked_id");
		$stmt->execute([":blocker_id" => $blocker_id,
                        ":blocked_id" => $blocked_id]);

		return ($stmt->fetchColumn() == 1) ? true : false;
	}
}
