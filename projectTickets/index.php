<?php
define("debug",true);
define("html",true);

if (html)
	{
		echo("<!DOCTYPE html>");
		echo("<html>");
		echo("  <head>");
		echo("    <meta charset=\"utf-8\" />");
		echo("    <title>IT Projects</title>");
		echo("  </head>");
		echo("  <body>");
		echo("<h3>IT Projects</h3>");
	}

$baseProtocol = "http://";
$baseDomain = "lansweeper.local";
$basePath = "/helpdesk/Ticket.aspx?tId=";
$baseLSURL = $baseProtocol . $baseDomain . $basePath;
$parentCustomField = 45;
$projectTicketType = 6;

if (false) //html
	{
		echo("<a href=\"" . $baseLSURL . GetProjectIdList($baseDomain,6) . "\">" . GetTicketSubject($baseDomain,GetProjectIdList($baseDomain,6)) . "</a> - ". GetTicketState($baseDomain,68) . "</p>");
		PrintSubTasks($baseDomain,$baseLSURL,68,$parentCustomField);
		UpdateNotesWithLinks($baseProtocol,$baseDomain,$basePath,69,68);
	}

if (true) //!html
	{
		$projects = GetProjectIdList($baseDomain,$projectTicketType);
		foreach($projects as $projectId)
			{
				$tasks = GetProjectSubTasks($baseDomain,$projectId,$parentCustomField);
				foreach($tasks as $taskId)
					{
						UpdateNotesWithLinks($baseProtocol,$baseDomain,$basePath,$taskId,$projectId);
					}
			}

	}


function PrintTicketNotes($baseDomain,$ticketId)
	{
		$notes = GetTicketNotes($baseDomain,$ticketId);
		foreach($notes as $note)
			{
				if (html)
					{
						echo("<p>" . $note . "</p>");
					}
			}
		
	}

function UpdateNotesWithLinks($baseProtocol,$baseDomain,$basePath,$childTicketId,$parentTicketId)
	{
		$parentNotes = GetTicketNotes($baseDomain,$parentTicketId);
		$parentLink = ($basePath . $parentTicketId);
		$parentLinkNeeded = true;
		$childNotes = GetTicketNotes($baseDomain,$childTicketId);
		$childLink = ($basePath . $childTicketId);
		$childLinkNeeded = true;

		foreach($parentNotes as $note)
			{
				if (stristr($note,$basePath . $childTicketId,1))
					{
						$parentLinkNeeded = false;
					}
				else
					{
					}
			}
		
		foreach($childNotes as $note)
			{
				if (stristr($note,$basePath . $parentTicketId,1))
					{
						$childLinkNeeded = false;
					}
				else
					{
					}
			}

		if ($parentLinkNeeded)
			{
				CreateLinkNote($baseProtocol,$baseDomain,$basePath,$childTicketId,$parentTicketId);
			}

		if ($childLinkNeeded)
			{
				CreateLinkNote($baseProtocol,$baseDomain,$basePath,$parentTicketId,$childTicketId);
			}

	}

function CreateLinkNote($baseProtocol,$baseDomain,$basePath,$linkTicketId,$targetTicketId)
	{
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "INSERT INTO dbo.htblnotes (ticketid,userid,note,notetype) values (" . $targetTicketId . ",-1,'Related Ticket: <a href=\"" . $baseProtocol . $baseDomain . $basePath . $linkTicketId . "\">" . GetTicketSubject($baseDomain,$linkTicketId) . "</a>',2)";
            $createLinkNotes = sqlsrv_query($conn, $tsql);
            if ($createLinkNotes == FALSE)
                die(sqlsrv_errors());
            sqlsrv_free_stmt($createLinkNotes);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
	}

function GetTicketNotes($baseDomain,$ticketId)
	{
    	$out = array();
	    try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "select note from dbo.htblnotes where ticketId=" . $ticketId . ";";
            $getTicketNotes = sqlsrv_query($conn, $tsql);
            if ($getTicketNotes == FALSE)
                die(sqlsrv_errors());
			while ($row = sqlsrv_fetch_array($getTicketNotes)) {
				foreach($row as $field) {
					if(end($out) != $field)
						{
							$out[] = $field;
						}
				}
			}
			return $out;
            sqlsrv_free_stmt($getTicketNotes);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
	}

function PrintSubTasks($baseDomain,$baseLSURL,$parentProjectId,$parentCustomField)
	{
		$tickets = GetProjectSubTasks($baseDomain,$parentCustomField,$parentCustomField);
		foreach($tickets as $ticketId)
			{
				if (html)
					{
						echo("<p>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"" . $baseLSURL . $ticketId . "\">" . GetTicketSubject($baseDomain,$ticketId) . "</a> - ". GetTicketState($baseDomain,$ticketId) . "</p>");
						PrintTicketNotes($baseDomain,$ticketId);
					}
				else
					{
						echo(GetTicketSubject($baseDomain,$ticketId) . " - ". GetTicketState($baseDomain,$ticketId));
					}
			}
		
	}

function GetProjectIDList($baseDomain,$ticketTypeId)
	{
    	$out = array();
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "SELECT ticketId FROM dbo.htblticket where tickettypeId in (" . $ticketTypeId . ");";
            $getProjectList = sqlsrv_query($conn, $tsql);
            if ($getProjectList == FALSE)
                die(sqlsrv_errors());
			while ($row = sqlsrv_fetch_array($getProjectList)) {
				foreach($row as $field) {
//			        echo($field);
//					return(htmlspecialchars($field));
					if(end($out) != $field)
						{
							$out[] = $field;
						}
				}
			}
			return $out;
            sqlsrv_free_stmt($getProjectList);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
	}

/*
function GetProjectIdList($baseDomain,$ticketTypeId)
    {
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "SELECT ticketId FROM dbo.htblticket where tickettypeId in (" . $ticketTypeId . ");";
            $getProjectList = sqlsrv_query($conn, $tsql);
            if ($getProjectList == FALSE)
                die(sqlsrv_errors());
			while ($row = sqlsrv_fetch_array($getProjectList)) {
				foreach($row as $field) {
//			        echo($field);
					return(htmlspecialchars($field));
				}
			}
            sqlsrv_free_stmt($getProjectList);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
    }
*/

function GetTicketSubject($baseDomain,$ticketId)
	{
	    try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "select subject from dbo.htblticket where ticketId=" . $ticketId . ";";
            $getTicketSubject = sqlsrv_query($conn, $tsql);
            if ($getTicketSubject == FALSE)
                die(sqlsrv_errors());
			while ($row = sqlsrv_fetch_array($getTicketSubject)) {
				foreach($row as $field) {
//			        echo($field);
					return(htmlspecialchars($field));
				}
			}
            sqlsrv_free_stmt($getTicketSubject);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
	}

function GetTicketState($baseDomain,$ticketId)
	{
	    try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "Select htblticketstates.statename From htblticket Inner Join htblticketstates On htblticketstates.ticketstateId = htblticket.ticketstateId where ticketId=" . $ticketId . ";";
            $getTicketState = sqlsrv_query($conn, $tsql);
            if ($getTicketState == FALSE)
                die(sqlsrv_errors());
			while ($row = sqlsrv_fetch_array($getTicketState)) {
				foreach($row as $field) {
//			        echo($field);
					return(htmlspecialchars($field));
				}
			}
            sqlsrv_free_stmt($getTicketState);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
	}

function GetProjectSubTasks($baseDomain,$parentProjectId,$parentCustomField)
    {
    	$out = array();
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "SELECT ticketId FROM dbo.htblticketcustomfield where tickettypefieldId in (" . $parentCustomField . ");";
            $getTicketList = sqlsrv_query($conn, $tsql);
            if ($getTicketList == FALSE)
                die(sqlsrv_errors());
			while ($row = sqlsrv_fetch_array($getTicketList)) {
				foreach($row as $field) {
//			        echo($field);
//					return(htmlspecialchars($field));
					if(end($out) != $field)
						{
							$out[] = $field;
						}
				}
			}
			return $out;
            sqlsrv_free_stmt($getTicketList);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
    }

/*
function AddAssetRelationToParent($assetId,$assetRelationParentId,$baseDomain,$containerAssetRelationshipType)
	{
		if (!CheckAssetRelationToParent($assetId,$assetRelationParentId,$baseDomain,$containerAssetRelationshipType))
		{
		echo("<h1>" . CheckAssetRelationToParent($assetId,$assetRelationParentId,$baseDomain,$containerAssetRelationshipType) . "</h1>");
		echo("<!-- Relation Does Not Exist -->");
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "INSERT INTO dbo.tblAssetRelations (ParentAssetId,ChildAssetId,Type) values (" . $assetRelationParentId . "," . $assetId . "," . $containerAssetRelationshipType . ")";
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
            sqlsrv_free_stmt($getAsset);
            sqlsrv_close($conn);
			InsertAssetCommentBarcodeScanTime($assetId,$baseDomain,("InsIde Asset " . $assetRelationParentId));
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
        } else { echo("<!-- Relation Exists -->"); }
	}

*/
/*

function CheckAssetRelationToParent($assetId,$assetRelationParentId,$baseDomain,$containerAssetRelationshipType)
	{
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "SELECT LastChanged from dbo.tblAssetRelations WHERE ParentAssetId = " . $assetRelationParentId . " AND ChildAssetId = " . $assetId . " AND Type = " . $containerAssetRelationshipType;
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
			while ($row = sqlsrv_fetch_array($getAsset)) {
  				foreach($row as $field) {
  					if ( $field != '')
  					{
    					return(true);
    				} else { return false; }
			    }
			}
            sqlsrv_free_stmt($getAsset);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
	}

*/
/*


function EchoAssetLinks($baseLSURL,$assetId)
	{
		echo("<p><a href=\"" . $baseLSURL . $assetId . "\" target=\"_blank\">Open in new page</a></p>");
		echo("<div height=\"100%\"><iframe src=\"" . $baseLSURL . $assetId . "\" wIdth=\"100%\" height=\"410px\" /></div>");
	}

*/


function OpenConnection($baseDomain)
    {
        try
        {
            $serverName = "tcp:" . $baseDomain . ",1433";
            $connectionOptions = array("Database"=>"lansweeperdb",
                "UId"=>"sqluser", "PWD"=>"sqlpassword");
            $conn = sqlsrv_connect($serverName, $connectionOptions);
            if($conn == false)
                die(sqlsrv_errors());
            else
            	return $conn;
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
    }

function Alert($message)
{
	if (debug)
	{
	    if (html)
	    {
			echo("<script type=\"text/javascript\">alert(\"" . $message . "\");</script>");
		} else {
			echo("Alert: " . $message);
		}
	}
}

if (html)
	{
		echo("  </body>");
		echo("</html>");
	}

?>
