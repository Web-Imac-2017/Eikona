<?php

interface CommentControllerInterface
{
    public function create($postID);
    
	public function delete($commentID);
    
	public function like($commentID);
    
	public function unlike($commentID);

	public function likes($commentID);
}

class CommentController
{
	private $model;
	private $likeModel;
	private $postModel;

	public function __construct()
	{
		$this->model     = new CommentModel();
		$this->likeModel = new CommentLikeModel();
		$this->postModel = new PostModel();
	}

	/**
	 * Set current comment
	 * @param integer $postID Post to comment
	 */
	public function create($postID)
    {
		$rsp = new Response();

		//Is this postID valid
		if(!isAuthorized::isPost($postID))
        {
			$rsp->setFailure(404, "Given comment ID does not exist.")
                ->send();
            
            return;
        }
        
        $this->postModel->setPost($postID);

        //Are comment allowed on this post ?
        if(!$this->postModel->getAllowComments())
        {
            $rsp->setFailure(400, "Comments are disabled for this post")
                ->send();
            
            return;
        }
        
        //Do we have something insert ?
		if(empty($_POST['commentText']))
        {
            $rsp->setFailure(400, "Missing value. Edit aborted.")
                ->send();
            
            return;
        }
        
		$profileID = Session::read("profileID");
        
        //Can the user comment with this profile ?
        if(!isAuthorized::ownProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();
            
            return;
        }
    
        //Can the current profile comment on a post from this profile ?
        if(!isAuthorized::seeFullProfile($this->postModel->getProfileID()))
        {
            $rsp->setFailure(401, "You can not comment this post")
                ->send();
            
            return;
        }
        
        $this->model->create($profileID, $postID, $_POST['commentText']);
        $notif = Response::read("notification", "create", "newComment", $profileID, $this->postModel->getProfileID(), $postID);
        
        if($notif['code'] != 200)
        {
            $rsp->setFailure(409, "post not commented and notification not sent")
                ->send();
            
            return;
        }
        
        $rsp->setSuccess(200, "Comment posted and notification sent")
            ->bindValue("profileID", $profileID)
            ->bindValue("postID", $postID)
            ->bindValue("comment", $_POST['commentText'])
            ->bindValue("notif", $notif['data'])
            ->send();
	}

	/*
	 * Suppression d'un commentaire
	 * @param integer $commentID Comment to remove
	 */
	public function delete($commentID)
	{
		$profileID = Session::read("profileID");

		if(!$this->setComment($commentID))
			return;

		$rsp = new Response();
        
        //Can the user comment with this profile ?
        if(!isAuthorized::ownProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();
            
            return;
        }
        
        //Is this profile the comment publisher ?
        if($this->model->getProfileID() != $profileID)
        {
            $rsp->setFailure(401, "You can not delete this comment. Not yours.")
                ->send();
            
            return;
        }
        
        $this->model->delete($commentID);
            
        $rsp->setSuccess(200, "comment deleted")
            ->bindValue("commentID", $commentID)
            ->send();
	}

	/*
	 * Like a comment
	 * @param integer $commentID Comment to like
	 */
	public function like($commentID)
	{
		if(!$this->setComment($commentID))
			return;

		$rsp = new Response();
        
		$profileID = Session::read("profileID");
        
        //Can the user comment with this profile ?
        if(!isAuthorized::ownProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();
            
            return;
        }
		
        //Is this a comment of the current profile
        if($this->model->getProfileID() == $profileID)
        {
            $rsp->setFailure(400, "You can not like your own comment")
                ->send();
            
            return;
        }
    
        //Can the current profile comment on a post from this profile ?
        if(!isAuthorized::seeFullProfile($this->postModel->getProfileID()))
        {
            $rsp->setFailure(401, "You can not comment this post")
                ->send();
            
            return;
        }
        
        //Is the user already liking the comment ?
        if($this->likeModel->isLiked($profileID, $commentID))
        {
            $rsp->setFailure(400, "comment already liked")
                ->send();
            
            return;
        }
        
        $this->likeModel->like($profileID, $commentID);
        $notif = Response::read("notification", "create", "newCommentLike", $profileID, $this->model->getProfileID(), $commentID);

        if($notif['code'] != 200)
        {
            $rsp->setFailure(409, "comment not liked and notification not sent")
                ->send();
            
            return;
        }
        
        $rsp->setSuccess(201, "comment liked and notification sent")
            ->bindValue("commentID", $commentID)
            ->bindValue("profileID", $profileID)
            ->bindValue("notif", $notif['data'])
            ->send();
	}

	/*
	 * Unlike a comment
	 * @param integer $commentID comment to unlike
	 */
	public function unlike($commentID)
	{
		$profileID = Session::read("profileID");

		if(!$this->setComment($commentID))
			return;

		$rsp = new Response();
        
        //Can the current profile comment on a post from this profile ?
        if(!isAuthorized::seeFullProfile($this->postModel->getProfileID()))
        {
            $rsp->setFailure(401, "You can not comment this post")
                ->send();
            
            return;
        }
        
        //Is the profile liking this comment?
        if(!$this->likeModel->isLiked($profileID, $commentID))
        {
            $rsp->setFailure(400, "post not liked")
                ->send();
            
            return;
        }
        
		$this->likeModel->unlike($profileID, $commentID);
		
        $rsp->setSuccess(200, "comment unliked")
			->bindValue("commentID", $commentID)
			->bindValue("profileID", $profileID)
            ->send();
	}
    
	/**
	 * Return the likes of a comment
	 * @param integer $commentID Comment to retrieve likes from
	 */
	public function likes($commentID)
	{
		if(!$this->setComment($commentID))
			return;
			
		$rsp = new Response();

        //Can this user get the likes.?
		if(!isAuthorized::seeFullProfile($this->model->getProfileID()))
        {
			$rsp->setFailure(401, "You can not see likes on this comment")
			    ->send();
            
			return;
		}

		$likes = $this->likeModel->getLikes($commentID);

		$rsp->setSuccess(200, "likes comment returnded")
		     ->bindValue("commentID", $commentID)
		     ->bindValue("nbOfLikes", count($likes))
		     ->bindValue("likes", $likes)
		     ->send();	
	}
}