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
        echo("<h2>User Relationships</h2>\n<hr />");
        PrintAssetUserRelations(GetAssetUserRelations($assetID,$cfg['baseDomain']));
        echo("<h2>Open Tickets</h2>\n<hr />");
        $tickets = array();
        $tickets = array_unique(GetAssetTickets($assetID,$cfg['baseDomain']))
        PrintUserTickets($tickets);
        if (count($tickets) == 0)
            {
                $html = $html . "<script type=\"text/javascript\">startFadeDec(50, 255, 50, 255, 255, 255, 20);</script>\n";
            }
            else
            {
                $html = $html . "<script type=\"text/javascript\">startFadeDec(50, 255, 255, 255, 255, 255, 20);</script>\n";
            }
	}
	else
		{
			$html = $html . "<p>Serial Invalid!</p>\n";
			$html = $html . "<script type=\"text/javascript\">startFadeDec(255, 50, 50, 255, 255, 255, 20);</script>\n";

		}
} else { $html = $html . "<h2>Please enter/scan a valid Asset Tag Barcode</h2>\n"; }

$html = $html . "<section id=\"container\" class=\"container\">
	<div id=\"interactive\" class=\"viewport\"></div>
	</section>
	<script src=\"/quaggaJS/example/vendor/jquery-1.9.0.min.js\" type=\"text/javascript\"></script>
	<script src=\"//webrtc.github.io/adapter/adapter-latest.js\" type=\"text/javascript\"></script>
	<script src=\"/quaggaJS/dist/quagga.js\" type=\"text/javascript\"></script>
	<script src=\"/scripts/scanScript.js\" type=\"text/javascript\"></script>
";

echo($html);

?>
