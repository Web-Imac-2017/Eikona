<?php

class NotificationController
{
	private $model;
	private $allowedCodes = [
		"newFollowAsk"      => 1, //DONE
		"followAccepted"    => 2, //DONE
		"newFollowing"      => 3, //DONE
		"newLike"           => 4, //DONE
		"newComment"        => 5, //DONE
		"newCommentLike"    => 6, //DONE
		"changeReportState" => 7
	];

	public function __construct()
	{
		$this->model = new NotificationModel();
	}

	public function create($code, $profileID, $profileTarget, $target)
	{
		$resp = new Response();

		if(!array_key_exists($code, $this->allowedCodes)){
			$resp->setFailure(400, "wrong code")
			     ->send();
			return;
		}

		$notif = $this->allowedCodes[$code];
		$this->model->add($notif, $profileID, $profileTarget, $target);

		$resp->setSuccess(200, "notif returned")
			 ->bindValue("type", $code)
			 ->bindValue("code", $notif)
			 ->bindValue("profileID", $profileID)
			 ->bindValue("profileTargetID", $profileTarget)
			 ->bindValue("targetID", $target)
		     ->send();
	}

	public function delete()
	{

	}

}