<?php

	require_once '../config/config.php';

	if (key_exists('sweepyScannerMethod',$_COOKIE))
	{
	    $sweepyScannerMethod = $_COOKIE['sweepyScannerMethod'];
	} else {
		$sweepyScannerMethod = 'inventory';
	}
	
	if (key_exists('addContainerAssetRelation',$_COOKIE))
	{
	    $addContainerAssetRelation = $_COOKIE['addContainerAssetRelation'];
	} else {
		$addContainerAssetRelation = false;
	}

	if (key_exists('inventoryRoom',$_COOKIE))
	{
	    $inventoryRoom = $_COOKIE['inventoryRoom'];
	} else {
		$inventoryRoom = false;
	}

	if (key_exists('inventoryLocation',$_COOKIE))
	{
	    $inventoryLocation = $_COOKIE['inventoryLocation'];
	} else {
		$inventoryLocation = false;
	}

	if (key_exists('inventoryBuilding',$_COOKIE))
	{
	    $inventoryBuilding = $_COOKIE['inventoryBuilding'];
	} else {
		$inventoryBuilding = false;
	}

	if (key_exists('inventoryDepartment',$_COOKIE))
	{
	    $inventoryDepartment = $_COOKIE['inventoryDepartment'];
	} else {
		$inventoryDepartment = false;
	}

	if (key_exists('inventoryBranchoffice',$_COOKIE))
	{
	    $inventoryBranchoffice = $_COOKIE['inventoryBranchoffice'];
	} else {
		$inventoryBranchoffice = false;
	}

	if (key_exists('assetRelationParentID',$_COOKIE))
	{
	    $assetRelationParentID = $_COOKIE['assetRelationParentID'];
	} else {
		$assetRelationParentID = '';
	}

?>	
	<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Sweepy Barcode Scanner Companion</title>
    <script type="text/javascript">
      window.onload = function() {
        document.getElementById("barcodeBox").focus();
      }
      
      function stBrowserDidScanBarcode(type, data, stid) {
        document.getElementById("barcodeBox").value=data;
        document.forms[0].submit();
	  }
    </script>
  </head>
  <body>
<?php

    $barcodeValue = (isset($_REQUEST["barcodeValue"])?$_REQUEST["barcodeValue"]:"");

echo("
<h3>Sweepy Barcode Scanner Companion</h3>
<p></p>
<form action=\"#\" method=\"post\" name=\"barcodeForm\">
<p><label>Barcode: </label><input type=\"text\" id=\"barcodeBox\" name=\"barcodeValue\" value=\"\"/ autofocus=\"autofocus\" onblur=\"setTimeout(function(){this.focus()}, 10);\" /></p>
<p><button type=\"submit\">Submit</button></p>
</form>
<h4>Note: This will add the Last Physical Inventory time and a comment to that effect to any asset with a valid barcode scanned.</h4>
");
?>
<?php

$baseLSURL = "https://" . $cfg['baseDomain'] . "/asset.aspx?AssetID=";
$containerAssetRelationshipType = "5";

		    $explodedBarcode = explode("_",$barcodeValue);
//			PrintAssetExplosion($explodedBarcode);

if ($inventoryRoom)
	{
	    echo("<h3>Inventory Mode</h3>");
	}

if ($explodedBarcode[0] == $cfg['roomPrefix'] && $inventoryRoom == false)
	{
		$inventoryRoom = true;
		echo("<h1>Inventory Set</h1>");
		$inventoryLocation = $explodedBarcode[1];
		$inventoryBuilding = $explodedBarcode[2];
		$inventoryDepartment = $explodedBarcode[3];
		$inventoryBranchoffice = $explodedBarcode[4];
	}
elseif ($explodedBarcode[0] == $cfg['roomPrefix'] && $inventoryRoom == true)
	{
		$inventoryRoom = false;
		echo("<h1>Inventory Unset</h1>");
		$inventoryLocation = false;
		$inventoryBuilding = false;
		$inventoryDepartment = false;
		$inventoryBranchoffice = false;
	}
elseif ($barcodeValue) {
	$assetID = GetAssetID($barcodeValue,$cfg['baseDomain']);
	if ($assetID) {
		$assetType = GetAssetType($assetID,$cfg['baseDomain']);
		if ($assetType == 901 || $assetType == 908)
		{
			if ($assetID == $assetRelationParentID)
			{
				echo("<h1>Container Unset</h1>");
				EchoAssetLinks($baseLSURL,$assetID);
				$addContainerAssetRelation = false;
				$assetRelationParentID = '';

			} else {
				echo("<h1>Container Set</h1>");
				SetAssetCustomBarcodeScanTime($assetID,$cfg['baseDomain']);
				InsertAssetCommentBarcodeScanTime($assetID,$cfg['baseDomain']);
				EchoAssetLinks($baseLSURL,$assetID);
				$addContainerAssetRelation = true;
				$assetRelationParentID = $assetID;
			}
		} else {
			if ($addContainerAssetRelation)
			{
				echo("<h1>Adding Asset Relationship</h1>");
				SetAssetCustomBarcodeScanTime($assetID,$cfg['baseDomain']);
				InsertAssetCommentBarcodeScanTime($assetID,$cfg['baseDomain']);
				EchoAssetLinks($baseLSURL,$assetID);
				AddAssetRelationToParent($assetID,$assetRelationParentID,$cfg['baseDomain'],$containerAssetRelationshipType);
			} else {
				if ($inventoryRoom)
					{
						$oldInventoryInfo = GetAssetInventoryInfo($assetID,$cfg['baseDomain']);
				        PrintAssetInventoryInfo($oldInventoryInfo);
                        InsertAssetCommentAuditTrail($assetID,$cfg['baseDomain'],$oldInventoryInfo,false);
						UpdateAssetInventory($assetID,$inventoryLocation,$inventoryBuilding,$inventoryDepartment,$inventoryBranchoffice,$cfg['baseDomain']);
					}
				SetAssetCustomBarcodeScanTime($assetID,$cfg['baseDomain']);
				InsertAssetCommentBarcodeScanTime($assetID,$cfg['baseDomain']);
				EchoAssetLinks($baseLSURL,$assetID);
				PrintAssetInventoryInfo(GetAssetInventoryInfo($assetID,$cfg['baseDomain']));
			}
		}
	} else { echo("<p>Barcode Invalid!</p>"); }
} else { echo("<h2>Please enter/scan a valid Asset Tag Barcode!</h2>"); }

function PrintAssetExplosion($explodedBarcode)
	{
		    echo("Start Explosion\n");
		    echo($barcodeValue);
		    echo("\n");
		    echo($explodedBarcode . "\n");
		    echo("\nEnd Explosion\n");
		    
		    echo("<p>Location: " . $explodedBarcode[1] . "\n</p>");
		    echo("<p>Building: " . $explodedBarcode[2] . "\n</p>");
		    echo("<p>Department: " . $explodedBarcode[3] . "\n</p>");
		    echo("<p>Branchoffice: " . $explodedBarcode[4] . "\n</p>");
	}

function AddAssetRelationToParent($assetID,$assetRelationParentID,$baseDomain,$containerAssetRelationshipType)
	{
		if (!CheckAssetRelationToParent($assetID,$assetRelationParentID,$baseDomain,$containerAssetRelationshipType))
		{
		echo("<h1>" . CheckAssetRelationToParent($assetID,$assetRelationParentID,$baseDomain,$containerAssetRelationshipType) . "</h1>");
		echo("<!-- Relation Does Not Exist -->");
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "INSERT INTO dbo.tblAssetRelations (ParentAssetID,ChildAssetID,Type) values (" . $assetRelationParentID . "," . $assetID . "," . $containerAssetRelationshipType . ")";
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
            sqlsrv_free_stmt($getAsset);
            sqlsrv_close($conn);
			InsertAssetCommentBarcodeScanTime($assetID,$baseDomain,("Inside Asset " . $assetRelationParentID));
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
        } else { echo("<!-- Relation Exists -->"); }
	}

function CheckAssetRelationToParent($assetID,$assetRelationParentID,$baseDomain,$containerAssetRelationshipType)
	{
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "SELECT LastChanged from dbo.tblAssetRelations WHERE ParentAssetID = " . $assetRelationParentID . " AND ChildAssetID = " . $assetID . " AND Type = " . $containerAssetRelationshipType;
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


function EchoAssetLinks($baseLSURL,$assetID)
	{
		echo("<p><a href=\"" . $baseLSURL . $assetID . "\" target=\"_blank\">Open in new page</a></p>\n");
//		echo("<div height=\"100%\"><iframe src=\"" . $baseLSURL . $assetID . "\" width=\"100%\" height=\"410px\" /></div>\n");
	}

function PrintAssetInfo($assetName)
	{
		if(debug){echo("<h3>Asset Name:</h3>\n");}
		echo("<h2>" . $assetName . "</h2>\n");
	}

function PrintAssetInventoryInfo($assetInfoArray)
	{
		if(debug){print_r($assetInfoArray);}
		echo("<h3>Asset: " . $assetInfoArray[0] . "</h3>\n");
		echo("<p>Location: " . $assetInfoArray[1] . "</p>\n");
		echo("<p>Building: " . $assetInfoArray[2] . "</p>\n");
		echo("<p>Department: " . $assetInfoArray[3] . "</p>\n");
		echo("<p>Branch Office:" . $assetInfoArray[4] . "</p>\n");
	}


function OpenConnection($baseDomain)
    {
        global $cfg;
        try
        {
//            $serverName = "tcp:" . $baseDomain . ",1433";
			$serverName = $baseDomain;
            $connectionOptions = array("Database"=>"lansweeperdb",
                "Uid"=>$cfg['sqluser'], "PWD"=>$cfg['sqlpass']);
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
		echo("<script type=\"text/javascript\">alert(\"" . $message . "\");</script>");
	}
}


function GetAssetID($barcodeValue,$baseDomain)
    {
        try
        {
            $conn = OpenConnection($baseDomain);
            $barcodeString = "'%" . ($str = ltrim($barcodeValue, '0')) . "%'";
            $tsql = "SELECT AssetID FROM dbo.tblAssetCustom where BarCode LIKE " . $barcodeString;
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
            Alert($getAsset);
			while ($row = sqlsrv_fetch_array($getAsset)) {
				foreach($row as $field) {
			        Alert($field);
					return(htmlspecialchars($field));
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

function GetAssetInfo($assetId,$baseDomain)
    {
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "SELECT AssetName FROM dbo.tblAssets where AssetID=" . $assetId;
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
            Alert($getAsset);
			while ($row = sqlsrv_fetch_array($getAsset)) {
				foreach($row as $field) {
			        Alert($field);
					return(htmlspecialchars($field));
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

function GetAssetInventoryInfo($assetId,$baseDomain)
    {
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "SELECT dbo.tblAssets.AssetName,dbo.tblAssetCustom.Location,dbo.tblAssetCustom.Building,dbo.tblAssetCustom.Department,dbo.tblAssetCustom.Branchoffice FROM dbo.tblAssets inner join dbo.tblAssetCustom on dbo.tblAssets.AssetID=dbo.tblAssetCustom.AssetID where dbo.tblAssets.AssetID=" . $assetId;
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
//            Alert($getAsset);
			while ($row = sqlsrv_fetch_array($getAsset))
				{
//			        Alert($row);
					return array(htmlspecialchars($row['AssetName']),htmlspecialchars($row['Location']),htmlspecialchars($row['Building']),htmlspecialchars($row['Department']),htmlspecialchars($row['Branchoffice']));
//					$assetName,$assetLocation,$assetBuilding,$assetDepartment,$assetBranchOffice
				}
            sqlsrv_free_stmt($getAsset);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
    }

function UpdateAssetInventory($assetID,$assetLocation,$assetBuilding,$assetDepartment,$assetBranchoffice,$baseDomain)
    {
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "UPDATE dbo.tblAssetCustom SET dbo.tblAssetCustom.Location='" . $assetLocation . "',dbo.tblAssetCustom.Building='" . $assetBuilding . "',dbo.tblAssetCustom.Department='" . $assetDepartment . "',dbo.tblAssetCustom.Branchoffice='" . $assetBranchoffice . "' where AssetID=" . $assetID;
            if(debug){echo("SQL: " . $tsql);}
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
            sqlsrv_free_stmt($getAsset);
            sqlsrv_close($conn);
            $inventoryInfoArray = array(0=>$assetID,1=>$assetLocation,2=>$assetBuilding,3=>$assetDepartment,4=>$assetBranchoffice);
            InsertAssetCommentAuditTrail($assetID,$baseDomain,$inventoryInfoArray,true);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
    }

function GetAssetType($assetID,$baseDomain)
    {
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "SELECT Assettype FROM dbo.tblAssets where AssetID=" . $assetID;
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
			while ($row = sqlsrv_fetch_array($getAsset)) {
				foreach($row as $field) {
					return(htmlspecialchars($field));
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

function SetAssetCustomBarcodeScanTime($assetID,$baseDomain)
    {
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "UPDATE dbo.tblAssetCustom SET Custom15 = CURRENT_TIMESTAMP where AssetID=" . $assetID;
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
            sqlsrv_free_stmt($getAsset);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
    }

function InsertAssetCommentBarcodeScanTime($assetID,$baseDomain,$comment="Barcode Scanned")
    {
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "INSERT INTO dbo.tblAssetComments (AssetID,Comment,AddedBy) values (" . $assetID . ",'" . $comment . "','SBSC')";
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
            sqlsrv_free_stmt($getAsset);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
    }

/*function PrintAssetInventoryInfo($assetInfoArray)
	{
		if(debug){print_r($assetInfoArray);}
		echo("<h3>Asset: " . $assetInfoArray[0] . "</h3>\n");
		echo("<p>Location: " . $assetInfoArray[1] . "</p>\n");
		echo("<p>Building: " . $assetInfoArray[2] . "</p>\n");
		echo("<p>Department: " . $assetInfoArray[3] . "</p>\n");
		echo("<p>Branch Office:" . $assetInfoArray[4] . "</p>\n");
	}*/


function InsertAssetCommentAuditTrail($assetID,$baseDomain,$inventoryInfoArray,$isNew)
    {
    	if ($isNew)
    		{
		        $comment = "Audit: New L: " . $inventoryInfoArray[1] . " B: " . $inventoryInfoArray[2] . " D: " . $inventoryInfoArray[3] . " bO: " . $inventoryInfoArray[4];
        	}
    	else
    		{
		        $comment = "Audit: Was L: " . $inventoryInfoArray[1] . " B: " . $inventoryInfoArray[2] . " D: " . $inventoryInfoArray[3] . " bO: " . $inventoryInfoArray[4];
        	}
        $sqlComment = htmlspecialchars($comment,ENT_Quotes);
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "INSERT INTO dbo.tblAssetComments (AssetID,Comment,AddedBy) values (" . $assetID . ",'" . $sqlComment . "','SBSC')";
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
            sqlsrv_free_stmt($getAsset);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
    }

setcookie("sweepyScannerMethod",$sweepyScannerMethod);
setcookie("addContainerAssetRelation",$addContainerAssetRelation);
setcookie("inventoryRoom",$inventoryRoom);
setcookie("inventoryLocation",$inventoryLocation);
setcookie("inventoryBuilding",$inventoryBuilding);
setcookie("inventoryDepartment",$inventoryDepartment);
setcookie("inventoryBranchoffice",$inventoryBranchoffice);
setcookie("assetRelationParentID",$assetRelationParentID);

?>
<a href="stBrowser://startBarcodeScanner">Start Scanner</a>
  </body>
</html>
