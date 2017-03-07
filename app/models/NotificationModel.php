<?php

class NotificationModel extends DBInterface
{

	/*
	
	Nouvel abonnement : 1. L'utilisateur est notifié que quelqu'un veut s'abonner à son compte. SEULEMENT POUR LES PROFILS PRIVES.
	Abonnement accepté : 2. L'utilisateur a accepté votre demande d'abonnement.
	Nouvel abonné : 3. L'utilisateur a un nouvel abonné.
	Nouveau like : 4. Tel post a reçu un like.
	Nouveau commentaire : 5. Tel post à un nouveau commentaire
	Nouveau like sur un commentaire : 6. Tel commentaire sur tel post à un nouveau like
	Vous êtes admin : 7.
	Vous êtes modérateur : 8.
	Vous avez perdu vos droits : 9.

	 */	

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

}