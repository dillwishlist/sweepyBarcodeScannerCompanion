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
    $html = $html . "<script type=\"text/javascript\">startFadeDec(50, 255, 50, 255, 255, 255, 20);</script>\n";
    if ($assetID) {
        $assetType = GetAssetType($assetID,$cfg['baseDomain']);
        if ($assetType == 901 || $assetType == 908)
        {
            if ($assetID == $assetRelationParentID)
            {
                $html = $html . "<h1>Container Unset</h1>\n";
                EchoAssetLinks($baseLSURL,$assetID);
                $addContainerAssetRelation = false;
                $assetRelationParentID = '';

            } else {
                $html = $html . "<h1>Container Set</h1>\n";
                SetAssetCustomBarcodeScanTime($assetID,$cfg['baseDomain']);
                InsertAssetCommentBarcodeScanTime($assetID,$cfg['baseDomain']);
                EchoAssetLinks($baseLSURL,$assetID);
                $addContainerAssetRelation = true;
                $assetRelationParentID = $assetID;
            }
        } else {
            if ($addContainerAssetRelation)
            {
                $html = $html . "<h1>Adding Asset Relationship</h1>\n";
                SetAssetCustomBarcodeScanTime($assetID,$cfg['baseDomain']);
                InsertAssetCommentBarcodeScanTime($assetID,$cfg['baseDomain']);
                EchoAssetLinks($baseLSURL,$assetID);
                AddAssetRelationToParent($assetID,$assetRelationParentID,$cfg['baseDomain'],$containerAssetRelationshipType);
            } else {
                if ($inventoryRoom)
                    {
                        $oldInventoryInfo = GetAssetInventoryInfo($assetID,$cfg['baseDomain']);
                        PrintAssetInventoryInfo($cfg['baseDomain'],$oldInventoryInfo);
                        InsertAssetCommentAuditTrail($assetID,$cfg['baseDomain'],$oldInventoryInfo,false);
                        UpdateAssetInventory($assetID,$inventoryLocation,$inventoryBuilding,$inventoryDepartment,$inventoryBranchoffice,$cfg['baseDomain']);
                    }
                SetAssetCustomBarcodeScanTime($assetID,$cfg['baseDomain']);
                InsertAssetCommentBarcodeScanTime($assetID,$cfg['baseDomain']);
                PrintAssetInventoryInfo($cfg['baseDomain'],GetAssetInventoryInfo($assetID,$cfg['baseDomain']),$assetID);
                PrintAssetUserRelations(GetAssetUserRelations($assetID,$cfg['baseDomain']));
            }
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
