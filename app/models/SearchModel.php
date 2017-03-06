<?php

class SearchModel extends DBInterface
{
	public function __construct()
	{
		parent::__construct();
	}


	public function searchProfile($query)
	{
		$stmt = $this->cnx->prepare("
			SELECT profile_id, profile_name, profile_desc, profile_create_time, profile_views, profile_private, profile_picture
			FROM profiles
			WHERE profile_name LIKE :q
			ORDER BY profile_views");
		$stmt->execute([":q" => '%'.$query.'%']);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function searchDescription($query)
	{
		$stmt = $this->cnx->prepare("
			SELECT post_id, posts.profile_id, profiles.profile_name, post_type, post_extension, post_description, post_publish_time, post_edit_time, post_state, post_geo_lat, post_geo_lng, post_geo_name, post_allow_comments, post_approved
			FROM posts
			JOIN profiles ON posts.profile_id = profiles.profile_id
			WHERE post_description LIKE :q
			ORDER BY post_publish_time DESC");
		$stmt->execute([":q" => '%'.$query.'%']);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function searchComment($query)
	{
		$stmt = $this->cnx->prepare("
			SELECT comment_id, comment_text, comment_time, comments.post_id, posts.profile_id, profiles.profile_name, post_type, post_extension, post_description, post_publish_time, post_edit_time, post_state, post_geo_lat, post_geo_lng, post_geo_name, post_allow_comments, post_approved
			FROM comments
			JOIN posts ON posts.post_id = comments.post_id
			JOIN profiles ON comments.profile_id = profiles.profile_id
			WHERE comment_text LIKE :q
			ORDER BY comment_time DESC");
		$stmt->execute([":q" => '%'.$query.'%']);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function searchTag($query)
	{
		$stmt = $this->cnx->prepare("");
		$stmt->execute([":q" => '%'.$query.'%']);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function searchAll($query)
	{
		$stmt = $this->cnx->prepare();
	}

}