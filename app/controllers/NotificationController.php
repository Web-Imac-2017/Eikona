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

	public function create($code, $profileTarget)
	{
		$notif = $this->allowedCodes[$code];
		$this->model->add($notif, $profileTarget);
	}

	public function delete()
	{

	}

	public function read()
	{

	}

}