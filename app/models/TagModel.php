<?php

class TagModel extends DBInterface
{

	public function __construct()
	{
		parent::__construct();
	}

	private function existTag($postID, $tagName)
	{
		// Verify if the tagName exist
		$stmtVerify = $this->cnx->prepare("SELECT tag_name FROM tags WHERE tag_name = :tagName AND post_id = :postID");
		$stmtVerify->execute([":postID"  => $postID,
							  ":tagName" => $tagName
		]);

		//The tag doesn't exist
		if($stmtVerify->fetch() == 0)
		{
			return false;
		}

		return true;
	}

	/*
	 * Add a comment
	 * @param $postID Id of the post where to add the tagName
	 * @param $tagName Name of the tag to add
	 */
	public function addTag($postID, $tagName)
	{
		if($this->existTag($postID, $tagName))
		{
			return "existAlready";
		}

		$stmt = $this->cnx->prepare("INSERT INTO tags(tag_name, post_id, use_time) VALUES (:tagName, :postID, :useTime)");
		$stmt->execute([":postID"  => $postID,
						":tagName" => $tagName,
						":useTime" => time()
 		]);

		return "success";
	}

	/*
	 * Delete a tag
	 * @param $postID Id of the post where there's the tagName
	 * @param $tagName Name of the tag to remove
	 */
	public function deleteTag($postID, $tagName)
	{
		if(!$this->existTag($postID, $tagName))
		{
			return "notFound";
		}

		//The tag exist, we delete it
		$stmt = $this->cnx->prepare("DELETE FROM tags WHERE post_id = :postID AND tag_name = :tagName");
		$stmt->execute([":postID"  => $postID,
					    ":tagName" => $tagName
		]);

		return "success";
	}

	
	/*
	 * Get all the post with the tagName given
	 * Do we need a limit ?
	 */
	public function tag($tagName)
	{
		$stmt = $this->cnx->prepare("
            SELECT post_id FROM tags
            WHERE tag_name = :tagName 
            ORDER BY use_time DESC");
        $stmt->execute([":tagName" => $tagName]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
