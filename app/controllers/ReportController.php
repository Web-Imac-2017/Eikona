<?php

class ReportController
{

	private $model;
	private $postController;

	public function __construct()
	{
		$this->model     = new ReportModel();
		$this->postController = new PostController();
	}

	/*
	 * Add a report for the given postID with the report Comment
	 * Get the userID of the session
	 * Verify if the given post exist
	 * No handler yet, status is 0
	 * @param $postID of the post concern
	 * @param $reportComment Comment of the user who reported
	 */
	public function add($postID)
	{
		if(!$this->postController->setPost($postID))
			return;

		$reportComment = Sanitize::string($_POST['report_comment']);
		$userID = Session::read("userID");
		$reportStatus = 0;

		$reportID = $this->model->add($userID, $postID, $reportComment, $reportStatus);

		$rsp = new Response();

		if($reportID > 0)
		{
			$rsp->setSuccess(200, "Report added")
				->bindValue("ReportID", $reportID);
		} else {
			$rsp->setFailure(400, "Report not added");
		}

		$rsp->send();
	}

	/* A moderator take the report,
	 * Get the userID of the moderator
	 * Status change
	 */
	public function handle($reportID)
	{
		$rsp = new Response();

		//Does the post exist ?
		if($this->model->exist($reportID))
		{
			$reportHandler = Session::read("userID");

			//Is the user a moderator or an admin ?
			if(isAuthorized::isModerator() || isAuthorized::isAdmin())
			{
				if($this->model->status($reportID) != 1)
				{
					$reportStatus = 1;

					$this->model->addModerator($reportID, $reportHandler, $reportStatus);

					$rsp = new Response();
					$rsp->setSuccess(200, "Report taken")
						->bindValue("ReportHandlerID", $reportHandler)
						->bindValue("Status", $reportStatus)
						->bindValue("ReportID", $reportID)
						->send();
				} else {
					$rsp->setFailure(200, "Report already handle.")
						->send();
				}
			} else {
				$rsp->setFailure(401,"You are not a moderator or an admin.")
					->send();
			}
		} else {
			$rsp->setFailure(404, "Report does not exist.")
			    ->send();
		}
	}

	 /*
	 * ReportStatus: 3, nothing to be done.
	 * ReportResult is send to the user_id who reported
	 */
	public function cancel($reportID)
	{
		$rsp = new Response();

		//Does the post exist ?
		if($this->model->exist($reportID))
		{
			$reportHandler = Session::read("userID");

			//Is the user a moderator or an admin ?
			if(isAuthorized::isModerator() || isAuthorized::isAdmin())
			{
				//Is the status of the report already cancelled
				if($this->model->status($reportID) == 3)
				{
					$rsp->setFailure(304 ,"Report already cancelled.");
				} else {
					$reportStatus = 3;

					$reportResult = Sanitize::string($_POST['report_result']);

					$this->model->moderate($reportID, $reportStatus, $reportResult);

					$rsp->setSuccess(200, "Report is now finish.");
				}
			} else {
				$rsp->setFailure(401, "You are not a moderator or an admin.");
			}
		} else {
			$rsp->setFailure("404", "Report doesn't exist");
		}
		$rsp->send();
	}


	/*
	 * ReportStatus: 2, a moderator has done is job,
	 * Post_state : 2 is now. User has to change something.
	 * ReportResult send to the user
	 */
	public function reported($reportID)
	{
		$rsp = new Response();

		if($this->model->exist($reportID) == 0)
		{
			$rsp->setFailure(404, "Report doesn't exist");
		}

		if(isAuthorized::isModerator() || isAuthorized::isAdmin())
		{
			$reportResult = $_POST['report_result'];
			$reportResult = Sanitize::string($reportResult);
			$reportStatus = 2;


			//Changer le state du post qui est report maintenant, ça marche pas for now
			$postID = $this->model->postID($reportID);

			if(!$this->postModel->setPost($postID))
				return;

			$this->postModel->updateState(2);

			/* Envoyer une notif ici avec le reportResult de la part de la session de l'userID */

			$this->model->moderate($reportID, $reportStatus, $reportResult);

			$rsp->setSuccess(200, "Post is now hidden.");
		} else {
			$rsp->setFailure(404, "The report has not worked.");
		}

		$rsp->send();

	}

	/*
	 * Modifier la fonction qui suit pour qu'elle récupère tous les reports:
	 * Soit ils ont le statut 0, par du tout modérer
	 * Soit ils ont le statut 2, ils ont été modérés mais pas modifié encore
	 * Prend en param le statut que l'on souhaite (post déjà modéré ou pas encore)
	 */

	/*
	 * Get all the reports which are not taken
	 * reportStatus = 0
	 */
	public function reports()
	{
		$reports = $this->model->getReports();

		$rsp = new Response();

		if($reports == false) {
			$rsp->setFailure(404);
		} else if($reports == 0) {
			$rsp->setSucess(204, "There is no reports.");
		} else {
			$rsp->setSuccess(200, "Reports returned")
				->bindValue("nbOfReports", count($reports))
				->bindValue("Reports", $reports);
		}

		$rsp->send();
	}

}
