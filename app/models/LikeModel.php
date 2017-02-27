<?php

class LikeModel extends DBInterface{

	public function __construct(){

		parent::__construct();

	}

	public function like($postID, $profileID)
	{
		if($postID == 0 || $profileID == 0) return false;

		$stmt = $this->cnx->prepare("
			INSERT INTO post_likes (profile_id, post_id, like_time)
			VALUES (:profileID, :postID, :likeTime)");
		$stmt->execute([":profileID" => $profileID,
			            ":postID"    => $postID,
			            ":likeTime"  => time()]);

		return true;
	}


	public function unlike($postID, $profileID)
	{
		if($postID == 0 || $profileID == 0) return false;

		$stmt = $this->cnx->prepare("
			DELETE FROM post_likes
			WHERE :postID = post_id
			AND :profileID = profile_id");
		$stmt->execute([":postID"    => $postID,
			            ":profileID" => $profileID]);

		return true;
	}

}