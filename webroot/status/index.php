<?php
	if (isset($_POST["serialNumber"]))
	{
	    $serialValue = $_POST["serialNumber"];
	    Alert($serialValue . "\r\n");
	}
	elseif (isset($_GET['serial']))
	{
		$serialValue = filter_input(INPUT_GET, 'serial', FILTER_SANITIZE_URL);
		Alert($serialValue . "\r\n");
	} else {
		$serialValue = "";
		echo("No value set!\r\n");
	}
	

function Alert($message)
{
	if (false) //True for debug, false for production.
	{
		echo($message . "\r\n");
	}
}

$baseDomain = "lansweeper.local";
$baseLSURL = "https://" . $baseDomain . "/asset.aspx?AssetID=";
$containerAssetRelationshipType = "5";

if ($serialValue) {
	Alert($serialValue);
	$assetID = GetAssetIDBySerial($serialValue,$baseDomain);
	Alert($assetID);
	echo GetName($assetID,$baseDomain) . "\r\n";
}

function OpenConnection($baseDomain)
    {
        try
        {
            $serverName = "tcp:" . $baseDomain . ",1433";
            $connectionOptions = array("Database"=>"lansweeperdb",
                "Uid"=>"sqluser", "PWD"=>"sqlpassword");
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

function GetAssetIDBySerial($serialValue,$baseDomain)
    {
        try
        {
            $conn = OpenConnection($baseDomain);
            $serialString = "'%" . ($str = ltrim($serialValue, ' ')) . "%'";
            $tsql = "SELECT AssetID FROM dbo.tblAssetCustom where SerialNumber LIKE " . $serialString;
            Alert($tsql);
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

function GetName($assetID,$baseDomain)
    {
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "SELECT AssetName FROM dbo.tblAssets where AssetID=" . $assetID;
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
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
?>
