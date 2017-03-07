<?php

class NotificationModel extends DBInterface
{

	public function __construct()
	{
		parent::__construct();
	}

	public function add($code, $profileTarget, $targetID)
	{
		$stmt = $this->cnx->prepare("
			INSERT INTO notifications (profile_id, notif_type, notif_target, notif_time, notif_seen)
			VALUES (:target, :code, :targetID, :time, :seen)");
		$stmt->execute([":target"   => $profileTarget,
						":code"     => $code,
						":targetID" => $targetID,
						":time"     => time(),
						":seen"     => 0]);
	}

	public function delete($id)
	{
		$stmt = $this->cnx->prepare("
			DELETE FROM notifications
			WHERE notif_id = :id");
		$stmt->execute([":id" => $id]);
	}

	public function getProfileNotifications($profileID)
	{
		$stmt = $this->cnx->prepare("
			SELECT notif_id, notif_type, notif_target, notif_time, notif_seen
			FROM notifications
			WHERE profile_id = :id");
		$stmt->execute([":id" => $profileID]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getUserNotifications($userID)
	{
		$stmt = $this->cnx->prepare("
			SELECT notif_id, profile_id, notif_type, notif_target, notif_time, notif_seen
			FROM notifications
			WHERE profile_id IN
			(
				SELECT profile_id FROM profiles
				WHERE user_id = :id
			)
			AND notif_seen = 0
			ORDER BY profile_id");
		$stmt->execute([":id" => $userID]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}