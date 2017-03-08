<?php

class NotificationModel extends DBInterface
{

	public function __construct()
	{
		parent::__construct();
	}

	public function add($code, $profileID, $profileTargetID, $targetID)
	{
		$stmt = $this->cnx->prepare("
			INSERT INTO notifications (profile_id, profile_target_id, notif_type, notif_target, notif_time, notif_seen)
			VALUES (:profile, :target, :code, :targetID, :time, :seen)");
		$stmt->execute([":profile"  => $profileID,
						":target"   => $profileTargetID,
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
			SELECT notif_id, profile_id, profile_target_id, notif_type, notif_target, notif_time, notif_seen
			FROM notifications
			WHERE profile_target_id = :id
			AND notif_seen = 0
			ORDER BY notif_time");
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
			ORDER BY profile_id, notif_time DESC");
		$stmt->execute([":id" => $userID]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function setNotificationSeen($notifID)
	{
		$stmt = $this->cnx->prepare("
			UPDATE notifications SET notif_seen = 0
			WHERE notif_id = :id");
		$stmt->execute([":id" => $notifID]);
	}
}