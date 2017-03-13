<?php

class ReportController
{

	private $model;
	private $postModel;
	private $postController;

	public function __construct()
	{
		$this->model          = new ReportModel();
		$this->postModel      = new PostModel();
		$this->postController = new PostController();
	}

	/*
	 * Add a report for the given postID with the report comment
	 * @param $postID of the post concern
	 * @param $_POST[$reportComment] Comment of the user who reported
	 */
	public function add($postID)
	{
		if(!$this->postController->setPost($postID))
			return;

		$reportComment = !empty($_POST['report_comment']) ? Sanitize::string($_POST['report_comment']) : "";
		$userID = Session::read("userID");
		$reportStatus = 0;

		$reportID = $this->model->add($userID, $postID, $reportComment, $reportStatus);

		$rsp = new Response();

		if($reportID > 0)
		{
			$rsp->setSuccess(200, "Report added")
				->bindValue("ReportID", $reportID)
				->send();
		} else {
			$rsp->setFailure(400, "Report not added")
				->send();
		}
	}

	/*
	 * Display all informations of a reports
	 * @param $reportID
	 */
	public function display($reportID)
	{
		$rsp = new Response();

		if(!$this->model->exist($reportID))
		{
			$rsp->setFailure(404, "Report does not exist.")
				->bindValue("ReportID", $reportID)
			    ->send();
			return;
		}

		$data = $this->model->getFullReport($reportID);

		$rsp->setSuccess(200, "Get all report informations")
			->bindValue("reportID", $reportID)
			->bindValue("userID", $data["user_id"])
			->bindValue("postID", $data["post_id"])
			->bindValue("reportComment", $data["report_comment"])
			->bindValue("reportStatus", $data["report_status"])
			->bindValue("reportHandler", $data["report_handler"])
			->bindValue("reportResult", $data["report_result"])
			->bindValue("timeStateChange", $data["time_state_change"])
			->send();
	}

	/*
	 * A moderator take the report
	 * @param $reportID
	 */
	public function handle($reportID)
	{
		$rsp = new Response();

		if(!$this->model->exist($reportID))
		{
			$rsp->setFailure(404, "Report does not exist.")
				->bindValue("ReportID", $reportID)
			    ->send();
			return;
		}

		$reportHandler = Session::read("userID");

		if(isAuthorized::isModerator() || isAuthorized::isAdmin())
		{
			switch($this->model->status($reportID)){
					case "1":
						$rsp->setFailure(200, "Report has already an handler.")
							->send();
						break;
					case "2":
						$rsp->setFailure(200 ,"Report already reported.")
							->send();
						break;
					case "3":
						$rsp->setFailure(200 ,"Report already finished.")
							->send();
						break;
					case "0":
						$reportStatus = 1;

						$this->model->addModerator($reportID, $reportHandler, $reportStatus);

						$rsp = new Response();
						$rsp->setSuccess(200, "Report taken")
							->bindValue("ReportHandlerID", $reportHandler)
							->bindValue("Status", $reportStatus)
							->bindValue("ReportID", $reportID)
							->send();
					break;
				}
		} else {
			$rsp->setFailure(401,"You are not a moderator or an admin.")
				->send();
			return;
		}
	}

	 /*
	 * Cancel the report
	 * ReportResult is send to the user_id who reported
	 * @param $reportID
	 */
	public function cancel($reportID)
	{
		$rsp = new Response();

		//Does the post exist ?
		if(!$this->model->exist($reportID))
		{
			$rsp->setFailure("404", "Report doesn't exist")
				->bindValue("ReportID", $reportID)
				->send();
			return;
		}

		//Is the user connected the one who is in charge ?
		$userID = Session::read("userID");
		$handlerID = $this->model->handlerID($reportID);

		if($userID != $handlerID)
		{
			$rsp->setFailure(200, "You are not in charge of this report.")
				->send();
			return;
		}

		//Is the user a moderator or an admin ?
		if(isAuthorized::isModerator() || isAuthorized::isAdmin())
		{
			switch($this->model->status($reportID)){
				//The post is handle or has been reported. It can be cancelled
				case "1":
				case "2":
					$reportStatus = 3;

					$postID = $this->model->postID($reportID);

					if(!$this->postController->setPost($postID))
						return;

					$result = $this->postModel->setPost($postID);

					//Update state of the post, send reportResult
					if($result == "success")
					{
						$state = $this->postModel->updateState(1);

						$reportResult = !empty($_POST['report_result']) ? Sanitize::string($_POST['report_result']) : "";

						$this->model->moderate($reportID, $reportStatus, $reportResult);

						$rsp->setSuccess(200, "Report is now finish.")
							->bindValue("ReportID", $reportID)
							->bindValue("PostState", $state)
							->send();
					}
					break;
				case "3":
					$rsp->setFailure(200 ,"Report already finished.")
						->send();
					break;
				case "0":
					$rsp->setFailure(200, "Report has not an handler yet.")
						->send();
				break;
			}
		} else {
			$rsp->setFailure(401, "You are not a moderator or an admin.")
				->send();
		}
	}


	/*
	 * Report is legitimate. Post is now hidden
	 * ReportResult send to the user
	 * @param $reportID
	 */
	public function reported($reportID)
	{
		$rsp = new Response();

		if($this->model->exist($reportID) == 0)
		{
			$rsp->setFailure(404, "Report doesn't exist")
				->send();
			return;
		}

		//Is the user connected the one who is in charge ?
		$userID = Session::read("userID");
		$handlerID = $this->model->handlerID($reportID);

		if($userID != $handlerID)
		{
			$rsp->setFailure(200, "You are not in charge of this report.")
				->send();
			return;
		}

		if(isAuthorized::isModerator() || isAuthorized::isAdmin())
		{
			switch($this->model->status($reportID)){
				case "0":
					$rsp->setFailure(200, "Report has not an handler yet.")
						->send();
					break;
				case "2":
					$rsp->setFailure(200 ,"Report already reported.")
						->send();
					break;
				case "3":
					$rsp->setFailure(200 ,"Report already finished.")
						->send();
					break;
				case "1":
					$reportResult = !empty($_POST['report_result']) ? Sanitize::string($_POST['report_result']) : "";
					$reportResult = Sanitize::string($reportResult);
					$reportStatus = 2;

					$postID = $this->model->postID($reportID);

					if(!$this->postController->setPost($postID))
						return;

					$result = $this->postModel->setPost($postID);

					if($result == "success")
					{
						$state = $this->postModel->updateState(2);


						$this->model->moderate($reportID, $reportStatus, $reportResult);

						$notif = Response::read("notification", "create", "changeReportState", 0, $this->postModel->getProfileID(), $postID);

						if($notif['code'] == 200){
							$rsp->setSuccess(200, "Post is now hidden. Notif send.")
								->bindValue("ReportID", $reportID)
								->bindValue("PostID", $postID)
								->bindValue("PostState", $state)
								->bindValue("notif", $notif['data'])
								->send();
							return;
						}else{
							$rsp->setFailure(409, "notif error")
							     ->send();
							return;
						}
						
					} else {
						$rsp->setFailure(400, "There is a problem with the postID.")
							->send();
					}
					break;
			}
		} else {
			$rsp->setFailure(404, "The report has not worked.")
				->send();
			return;
		}
	}

	/*
	 * Get all the post from the moderator connected, which are hidden and have been modify since
     */
	public function waiting()
	{
		$userID = Session::read("userID");
		$reportsID = $this->model->getPostModified($userID);

		$rsp = new Response();

		if(!$reportsID)
		{
			$rsp->setFailure(400, "There is no reports which has been changed since last time.")
				->send();
			return;
		}

		$rsp->setSuccess(200, "Here is the reports which changed since their status changed.");

		$reports = array();

		foreach($reportsID as $row => $report){
			$reportID = $report["report_id"];
			$report = Response::read("Report", "display", $reportID)["data"];
			array_push($reports, $report);
		}

		$rsp->bindValue("reports", $reports)
			->send();
	}

	/*
	 * Get all the reports which are not taken or get all reports from a moderator
	 * $_POST['my_reports'] = if you want your reports to moderate
	 * Else get all reports not handle yet
	 */
	public function reports()
	{
		if(isset($_POST['my_reports']))
		{
			$userID = Session::read("userID");
			$reports = $this->model->getReports($userID);
		} else {
			$reports = $this->model->getReports();
		}

		$rsp = new Response();

		if($reports == false) {
			$rsp->setSuccess(200, "You have no reports.");
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
