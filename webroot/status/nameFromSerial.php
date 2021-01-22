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
		PrintAssetNameOnly($cfg['baseDomain'],GetAssetInventoryInfo($assetID,$cfg['baseDomain']),$assetID);
	}
}

?>
