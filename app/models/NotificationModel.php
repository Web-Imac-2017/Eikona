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

	public function add($code, $profileTarget)
	{
		$stmt = $this->cnx->prepare("
			INSERT INTO notifications (profile_id, notif_type, notif_time, notif_seen)
			VALUES (:target, :code, :time, :seen)");
		$stmt->execute([":target" => $profileTarget,
			            ":code" => $code,
			            ":time" => time(),
			            ":seen" => 0]);
	}

	public function delete($id)
	{
		$stmt = $this->cnx->prepare("
			DELETE FROM notifications
			WHERE notif_id = :id");
		$stmt->execute([":id" => $id]);
	}

}