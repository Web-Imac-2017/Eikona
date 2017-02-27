<?php

class PostModel extends DBInterface
{
	private $commentData = null;

	public function __construct()
	{
		parent::__construct();
	}

	public function addTag($postID, $tagName)
	{
		$tagName = Sanitize::string($tagName);

		$stmt = $this->cnx->prepare("INSERT INTO comments(tag_name, post_id, use_time) VALUES(:tagName, :postID, :useTime) WHERE post_id = :postID");
		$stmt->execute([":tagName" => $tagName,
						":postID"  => $postID,
						":useTime" => time()
 		]);
	}

	public function deleteTag()
	{
		$stmt = $this->cnx->prepare("DELETE FROM tags WHERE post_id = :postID AND tag_name = :tagName");
		$stmt->execute([":postID"  => $this->postID,
					    ":tagName" => $this->tagName
		]);

		return true;
	}

	//countTag($tagName);
}
