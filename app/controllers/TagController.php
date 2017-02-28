<?php

class TagController
{
	private $model;

	public function __construct()
	{
		$this->model = new TagModel();
	}

	/*
	 * Add a comment
	 * Verify if there is a profile and if it's autorized
	 * Verify if the post exist
	 * @param $postID Id of the post where to add the tagName
	 * @param $tagName Name of the tag to add
	 */
	public function add($postID, $tagName)
	{
		//Manque le Sanitize des mots bannis
		//$tagName = Sanitize::string($tagName);
		$rsp = new Response();

		$profileID = Session::read("profileID");

		if(!$profileID){
			$rsp->setFailure(401, "You do not have current profile selected")
			    ->send();
			return;
		}

		if(!isAuthorized::editPost($postID)){
			$rsp->setFailure(401, "You are not authorized to do this action.")
			    ->send();
			return;
		}

		//Verify if the tagName for this post already exist
		$result = $this->model->addTag($postID, $tagName);

		if($result == "existAlready")
		{
			$rsp->setFailure(401, "This tag already exist for this post.")
			    ->send();
			return;
		}

		$rsp->setSuccess(200, "tag added")
			->send();
	}

	/*
	 * Delete a tag
	 * Verify if there is a profile and if it's autorized
	 * Verify if the post exist
	 * @param $postID Id of the post where there's the tagName
	 * @param $tagName Name of the tag to remove
	 */
	public function delete($postID, $tagName)
	{
		$rsp = new Response();

		$profileID = Session::read("profileID");

		if(!$profileID){
			$rsp->setFailure(401, "You do not have current profile selected")
			    ->send();
			return;
		}

		if(!isAuthorized::editPost($postID)){
			$rsp->setFailure(401, "You are not authorized to do this action.")
			    ->send();
			return;
		}

		//Vérifier si l'id du post existe
		$result = $this->model->deleteTag($postID, $tagName);

		if($result == "notFound")
		{
			$rsp->setFailure(400, "not found");
		} else {
			$rsp->setSuccess(200, "tag deleted")
			->bindValue("result", $result);
		}
			$rsp->send();
	}

	/*
	 * Number of a tag with this tagName
	 * @param $tagName name of the tag to find
	 */
	public function count($tagName)
	{
		$nbTag = $this->model->countTag($tagName);

		$rsp = new Response;
		$rsp->setSuccess(200)
			->bindValue("nbTag", $nbTag)
			->send();
	}
}
