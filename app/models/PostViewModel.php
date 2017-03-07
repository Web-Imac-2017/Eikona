<?php

class PostViewModel extends DBInterface
{
	private $postViewID = 0;

	public function __construct($postViewID = 0)
	{
		parent::__construct();
	}

	public function view($profileID, $postID)
	{
		$prfl = Sanitize::int($profileID);
		$pst = Sanitize::int($postID);

		if($prfl < 1 || $pst < 1)
            return false;

        $stmt = $this->cnx->prepare("INSERT INTO post_views(profile_id, post_id, view_time) VALUES (:profile, :post, :time)");
        $stmt->execute([
        	":profile" => $prfl,
        	":post" => $pst,
        	":time" => time()
        ]);

        return true;
	}

	public function nbPostViews($postID)
	{
		$pst = Sanitize::int($postID);
		
		if($pst < 1)
            return false;

		$stmt = $this->cnx->prepare("SELECT COUNT(*) FROM post_views WHERE post_id = :postID");
		$stmt->execute([":postID" => $postID]);

		return $stmt->fetchColumn();
	}

	public function mostViewedPosts($nbPostsToReturn)
	{

		$tabCountPost = array();

		$stmtCount = $this->cnx->prepare("SELECT COUNT(*) FROM post_views");
		$stmtCount->execute();
		$nbLines = $stmtCount->fetchColumn();

		$stmtPost = $this->cnx->prepare("SELECT post_id FROM post_views");
		$stmtPost->execute();

		for($i = 0 ; $i < $nbLines ; $i++)
		{
			$id = intval($stmtPost->fetchColumn());
			$found = false;
			//$tabCountPost[$tmp]++;

			for($j = 0 ; $j < sizeof($tabCountPost) ; $j++)
			{
				if(isset($tabCountPost[$j][0]) && $tabCountPost[$j][0] == $id)
				{
					$tabCountPost[$j][1]++;
					$found = true;
				}
			}
			if(!$found)
			{
				array_push($tabCountPost, [$id, 1]);		
			}

		}

		$tabMostViewedPosts = $this->tri($tabCountPost);

		if(count($tabMostViewedPosts) < $nbPostsToReturn)
		{
			return $tabMostViewedPosts;
		}
		else
		{
			return array_slice($tabMostViewedPosts, 0, $nbPostsToReturn);
		}

		
	}

	public function tri($t)
	{
		$echange = true;
		while($echange)
		{
			for($i = 0 ; $i < count($t) - 1 ; $i++)
			{
				$echange = false;
				if($t[$i][1] < $t[$i + 1][1])
				{
					$tmp = $t[$i];
					$t[$i] = $t[$i + 1];
					$t[$i + 1] = $tmp;
					$echange = true;
				}
			}
		}
		
	    return $t;
	}
}
