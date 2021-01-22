<?php
	require_once $_SERVER["DOCUMENT_ROOT"] . '/../classes/sweepy.php';

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
	

$baseLSURL = "https://" . $cfg['baseDomain'] . "/asset.aspx?AssetID=";
$containerAssetRelationshipType = "5";

if ($serialValue) {
	$assetID = GetAssetIDBySerial($serialValue,$cfg['baseDomain']);
	if ($assetID) {
		PrintAssetInventoryInfo($cfg['baseDomain'],GetAssetInventoryInfo($assetID,$cfg['baseDomain']),$assetID);
        $html = $html . "<h2>User Relationships</h2>\n<hr />";
        PrintAssetUserRelations(GetAssetUserRelations($assetID,$cfg['baseDomain']));
        $html = $html . "<h2>Open Tickets</h2>\n<hr />";
        $tickets = array();
        $tickets = array_unique(GetAssetTickets($assetID,$cfg['baseDomain']));
        PrintUserTickets($tickets);
        if (count($tickets) == 0)
            {
                $html = $html . "<script type=\"text/javascript\">startFadeDec(50, 255, 50, 255, 255, 255, 20);</script>\n";
            }
            else
            {
                $html = $html . "<script type=\"text/javascript\">startFadeDec(255, 255, 50, 255, 255, 255, 20);</script>\n";
            }
	}
	else
		{
			$html = $html . "<p>Serial Invalid!</p>\n";
			$html = $html . "<script type=\"text/javascript\">startFadeDec(255, 50, 50, 255, 255, 255, 20);</script>\n";

		}
} else { $html = $html . "<h2>Invalid Entry</h2>\n"; }


echo($html);

?>
