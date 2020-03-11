<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Sweepy Barcode Scanner Companion - Blacklist MAC Address</title>
    <script type="text/javascript">
      window.onload = function() {
        document.getElementById("macBox").focus();
      }
      
      function stBrowserDidScanBarcode(type, data, stid) {
        document.getElementById("macBox").value=data;
        document.forms[0].submit();
	  }
    </script>
  </head>
  <body>
<?php

    $macValue = (isset($_REQUEST["macValue"])?$_REQUEST["macValue"]:"");
    $commentValue = (isset($_REQUEST["commentValue"])?$_REQUEST["commentValue"]:"");

echo("
<h3>Sweepy MAC Blacklist</h3>
<p>Enter a MAC address in 01:02:03:04:05:06 format to blacklist in the Lansweeper database to prevent errant merges.</p>
<form action=\"#\" method=\"post\" name=\"macForm\">
<p><label>MAC Address: </label><input type=\"text\" id=\"macBox\" name=\"macValue\" value=\"\"/ autofocus=\"autofocus\" onblur=\"setTimeout(function(){this.focus()}, 10);\" /></p>
<p><label>Description: </label><input type=\"text\" id=\"commentBox\" name=\"commentValue\" value=\"\"/ /></p>
<p><button type=\"submit\">Submit</button></p>
</form>
");
?>
<?php

define("debug",false);

$baseDomain = "lansweeper.local";
$baseLSURL = "https://" . $baseDomain . "/asset.aspx?AssetID=";
$containerAssetRelationshipType = "5";

Alert($macValue . ":" . $commentValue);

if($macValue)
	{
		BlacklistMACAddress($macValue,$commentValue);
	}

function BlacklistMACAddress($macAddress,$macComment)
	{
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "INSERT INTO tsysMacBlacklist (Mac, Comment) VALUES ('" . $macAddress . "', '" . $macComment . "')";
            $updateMACAddress = sqlsrv_query($conn, $tsql);
            if ($updateMACAddress == FALSE)
                die(sqlsrv_errors());
            sqlsrv_free_stmt($updateMACAddress);
            sqlsrv_close($conn);
            UpdateBlackList();
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
	}

function UpdateBlackList()
	{
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "UPDATE tsysupdate SET MacBlacklist = GETDATE()";
            $MacBlacklist = sqlsrv_query($conn, $tsql);
            if ($MacBlacklist == FALSE)
                die(sqlsrv_errors());
            sqlsrv_free_stmt($MacBlacklist);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            echo("Error!");
        }
	}

function OpenConnection($baseDomain)
    {
        try
        {
//            $serverName = "tcp:" . $baseDomain . ",1433";
			$serverName = "lansweeper.local";
            $connectionOptions = array("Database"=>"lansweeperdb",
                "Uid"=>"sqluser", "PWD"=>"sqlpasswd");
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

?>
  </body>
</html>
