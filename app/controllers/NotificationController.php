<?php

class NotificationController
{
	private $model;
	private $allowedCodes = [
		"newFollowAsk"   => 1,
		"followAccepted" => 2,
		"newFollower"    => 3,
		"newLike"        => 4,
		"newComment"     => 5,
		"newCommentLike" => 6,
		"admin"          => 7,
		"moderator"      => 8,
		"user"           => 9
	];

	public function __construct()
	{
		$this->model = new NotificationModel();
	}

	public function create($code, $profileTarget, $target)
	{
		$resp = new Response();

		if(!array_key_exists($code, $this->allowedCodes)){
			$resp->setFailure(400, "wrong code")
			     ->send();
			return;
		}

		$notif = $this->allowedCodes[$code];
		$this->model->add($notif, $profileTarget, $target);

		$resp->setSuccess(200, "notif returned")
			 ->bindValue("type", $code)
			 ->bindValue("code", $notif)
			 ->bindValue("profileTargetID", $profileTarget)
			 ->bindValue("targetID", $target)
		     ->send();
	}

	public function delete()
	{

	}

	public function read()
	{

	}

}