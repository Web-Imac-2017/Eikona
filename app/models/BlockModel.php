<?php

class BlockModel extends DBInterface{

	public function __construct()
	{
		parent::__construct();
	}

	/***********************/
	/***** INSCRIPTION *****/
	/***********************/

	/**
	 * Vérifie si l'utilisateur est unique
	 * @param  int $blocker_id blocker_id
	 * @param  int $blocked_id blocked_id
	 * @param  time $time time()
	 * @return boolean	       true / false
	 */
	public function blockUser($blocker_id, $blocked_id, $time)
	{
		$stmt = $this->cnx->prepare("INSERT INTO blocked (blocked_id, blocker_id, block_time) VALUES (:blocker_id, :blocked_id, :time)");
		$stmt->execute([":blocker" => $blocker_id,
                        ":blocked" => $blocked_id,
                        ":time" => $time]);

		return ($stmt->fetchColumn() == 0) ? true : false;
	}
}
