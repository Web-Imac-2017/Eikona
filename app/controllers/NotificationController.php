<?php

interface NotificationControllerInterface
{
    public function create($code, $profileID, $profileTarget, $target);
}

class NotificationController implements NotificationControllerInterface
{
	private $model;
	private $allowedCodes = [
		"newFollowAsk"      => 1,
		"followAccepted"    => 2,
		"newFollowing"      => 3,
		"newLike"           => 4,
		"newComment"        => 5,
		"newCommentLike"    => 6,
		"changeReportState" => 7
	];

	public function __construct()
	{
		$this->model = new NotificationModel();
	}

	/**
	 * Creata new notification with the given arguments
	 * @param string  $code          TYpe of notification to add
	 * @param integer $profileID     Profile sending the nitification
	 * @param integer $profileTarget Profile receiving the notification
	 * @param integer $target        Id of the item the notification links to
	 */
	public function create($code, $profileID, $profileTarget, $target)
	{
		$rsp = new Response();

		if(!array_key_exists($code, $this->allowedCodes)){
			$rsp->setFailure(400, "wrong code")
			     ->send();
			return;
		}

		$notif = $this->allowedCodes[$code];
		$this->model->add($notif, $profileID, $profileTarget, $target);

		$rsp->setSuccess(200, "notif returned")
            ->bindValue("type", $code)
            ->bindValue("code", $notif)
			->bindValue("profileID", $profileID)
			->bindValue("profileTargetID", $profileTarget)
			->bindValue("targetID", $target)
		    ->send();
	}
}
