<?php

class ReportModel extends DBInterface
{
	private $reportID = 0;

	public function __construct(){
		parent::__construct();
	}

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

	public function addModerator($reportID, $reportHandler, $reportStatus)
	{
		$stmt = $this->cnx->prepare("UPDATE reports SET report_handler = :reportHandler, report_status = :reportStatus WHERE report_id = :reportID");
		$stmt->execute([ ":reportHandler" => $reportHandler,
						 ":reportStatus"  => $reportStatus,
						 ":reportID"      => $reportID
		]);
	}

	public function moderate($reportID, $reportStatus, $reportResult = "")
	{
		$stmt = $this->cnx->prepare("UPDATE reports SET report_status = :reportStatus, report_result = :reportResult WHERE report_id = :reportID");
		$stmt->execute([ ":reportStatus" => $reportStatus,
						 ":reportID"     => $reportID,
						 ":reportResult" => $reportResult
		]);
	}

	public function getReports()
	{
		$stmt = $this->cnx->prepare("SELECT report_id FROM reports WHERE report_status = 0");
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_COLUMN, "report_id");
	}

	public function postID($reportID)
	{
		$stmt = $this->cnx->prepare("SELECT post_id FROM reports WHERE report_id = :reportID");
		$stmt->execute([":reportID" => $reportID]);

		return $stmt->fetchColumn();
	}

	public function status($reportID)
	{
		$stmt = $this->cnx->prepare("SELECT report_status FROM reports WHERE report_id = :reportID");
		$stmt->execute([":reportID" => $reportID]);

		return $stmt->fetchColumn();
	}
}
