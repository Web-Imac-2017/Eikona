<?php

class ReportModel extends DBInterface
{
	private $reportID = 0;

	public function __construct(){
		parent::__construct();
	}

	/*
	 * Does the report with this reportID exist ?
	 * @param $reportID
	 * return 1 if yes, 0 if no
	 */
	public function exist($reportID)
	{
		$stmt = $this->cnx->prepare("SELECT * FROM reports WHERE report_id = :reportID");
		$stmt->execute([ ":reportID" => $reportID]);

		if($stmt->fetchColumn() == false)
		{
			return 0;
		}

		return 1;
	}

	/*
	 * Add a report
	 * @param $userID User who reports
	 * @param $postID ID which is reported
	 * @param $reportComment Comment for the report
	 * @param $reportStatus Status for the report
	 */
	public function add($userID, $postID, $reportComment, $reportStatus)
	{
		$stmt = $this->cnx->prepare("INSERT INTO reports(user_id, post_id, report_comment, report_status) VALUES (:userID, :postID, :reportComment, :reportStatus)");
		$stmt->execute([ ":userID"        => $userID,
						 ":postID"        => $postID,
						 ":reportComment" => $reportComment,
						 ":reportStatus"  => $reportStatus
					   ]);

		$this->reportID = $this->cnx->lastInsertId();

		return $this->reportID;
	}

	public function getFullReport($reportID)
	{
		$stmt = $this->cnx->prepare("SELECT * FROM reports WHERE report_id = :reportID");
		$stmt->execute([ ":reportID" => $reportID]);

		return $stmt->fetch();
	}

	/*
	 * A moderator is now handling the report
	 * @param $reportID
	 * @param $reportHandler ID of the moderator
	 * @param $reportStatus Status is now 1, taken
	 */
	public function addModerator($reportID, $reportHandler, $reportStatus)
	{
		$stmt = $this->cnx->prepare("UPDATE reports SET report_handler = :reportHandler, report_status = :reportStatus WHERE report_id = :reportID");
		$stmt->execute([ ":reportHandler" => $reportHandler,
						 ":reportStatus"  => $reportStatus,
						 ":reportID"      => $reportID
		]);
	}

	/*
	 * The reports is legitimate. Changes the status of the reports and time change
	 * @param $reportID
	 * @param $reportStatus
	 * @param $reportResult Optionnal
	 */
	public function moderate($reportID, $reportStatus, $reportResult = "")
	{
		$stmt = $this->cnx->prepare("UPDATE reports SET report_status = :reportStatus, report_result = :reportResult, time_state_change = :timeStateChange WHERE report_id = :reportID");
		$stmt->execute([ ":reportStatus"    => $reportStatus,
						 ":reportResult"    => $reportResult,
						 ":timeStateChange" => time(),
						 ":reportID"        => $reportID
		]);
	}

	/*
	 * Get all my reports OR get all reports not taken yet
	 * @param $userID optionnal if I want my reports
	 * return all reports ID
	 */
	public function getReports($userID = 0)
	{
		if($userID != 0)
		{
			$stmt = $this->cnx->prepare("SELECT report_id FROM reports WHERE report_handler = :userID AND (report_status = 1 OR report_status = 2)");
			$stmt->execute([ ":userID" => $userID]);
		} else {
			$stmt = $this->cnx->prepare("SELECT report_id FROM reports WHERE report_status = 0");
			$stmt->execute();
		}

		return $stmt->fetchAll(PDO::FETCH_COLUMN, "report_id");
	}
	/*
	 * Get all reports reported by the user
	 * @param $reporterID
	 * return all reports ID
	 */
	public function getReportsFromReporter($reporterID)
	{
		$stmt = $this->cnx->prepare("SELECT report_id FROM reports WHERE user_id = :reporterID");
		$stmt->execute([ ":reporterID" => $reporterID]);

		return $stmt->fetchAll(PDO::FETCH_COLUMN, "report_id");
	}

	/*
	 * Set Reporter to 0
	 * @param $reportID
	 */
	public function removeReporter($reportID)
	{
		$stmt = $this->cnx->prepare("UPDATE reports SET user_id = 0 WHERE report_id = :reportID");
		$stmt->execute([ ":reportID" => $reportID]);
	}

	/*
	 * Get all my reportsID and postID
	 *
	 */
	public function getPostModified($userID)
	{
		$stmt = $this->cnx->prepare("SELECT report_id FROM reports JOIN posts ON reports.post_id = posts.post_id WHERE report_status = 2 AND report_handler = :userID AND posts.post_edit_time > reports.time_state_change");
		$stmt->execute([":userID" => $userID]);

		return $stmt->fetchAll();
	}

	/*
	 * Get the postID from the reportID given
	 * @param $reportID
	 * return postID
	 */
	public function postID($reportID)
	{
		$stmt = $this->cnx->prepare("SELECT post_id FROM reports WHERE report_id = :reportID");
		$stmt->execute([":reportID" => $reportID]);

		return $stmt->fetchColumn();
	}

	/*
	 * Get the status from the reportID given
	 * @param $reportID
	 * return status : 0, 1, 2, 3
	 */
	public function status($reportID)
	{
		$stmt = $this->cnx->prepare("SELECT report_status FROM reports WHERE report_id = :reportID");
		$stmt->execute([":reportID" => $reportID]);

		return $stmt->fetchColumn();
	}

	/*
	 * Get the handler ID of the report ID
	 * @param $reportID
	 * return reportHandlerID
	 */
	public function handlerID($reportID)
	{
		$stmt = $this->cnx->prepare("SELECT report_handler FROM reports WHERE report_id = :reportID");
		$stmt->execute([":reportID" => $reportID]);

		return $stmt->fetchColumn();
	}
}
