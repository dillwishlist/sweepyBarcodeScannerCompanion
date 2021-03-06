<?php

    require_once '../classes/sweepy.php';


if (isset($_REQUEST["barcodeValue"])?$_REQUEST["barcodeValue"]:"")
    {
        $barcodeValue = (isset($_REQUEST["barcodeValue"])?$_REQUEST["barcodeValue"]:"");
        $explodedBarcode = explode("_",$barcodeValue);
    }

$html = $html . "
<h3>Sweepy Barcode Scanner Companion</h3>
<p></p>
<form action=\"#\" method=\"post\" name=\"barcodeForm\">
<p><label>Barcode: </label><input type=\"text\" id=\"barcodeBox\" name=\"barcodeValue\" value=\"\"/ /><button type=\"submit\">Submit</button></p>
</form>
";

$baseLSURL = "https://" . $cfg['baseDomain'] . "/asset.aspx?AssetID=";
$containerAssetRelationshipType = "5";



if ($inventoryRoom)
    {
        $html = $html . "<h3>Inventory Mode</h3>\n";
        $html = $html . "<h3>" . $inventoryLocation . " " . $inventoryBuilding . " " . $inventoryDepartment . " " . $inventoryBranchoffice . " <a href=\"#\" onclick=\"document.getElementById('barcodeBox').value='" . $cfg['roomPrefix'] . "';document.forms[0].submit();\">Exit Room</a></h3>\n";
    }

if ($explodedBarcode[0] == $cfg['roomPrefix'] && $inventoryRoom == false)
    {
        $inventoryRoom = true;
//        $html = $html . "<h1>Inventory Set</h1>\n";
        $inventoryLocation = $explodedBarcode[1];
        $inventoryBuilding = $explodedBarcode[2];
        $inventoryDepartment = $explodedBarcode[3];
        $inventoryBranchoffice = $explodedBarcode[4];
    }
elseif ($explodedBarcode[0] == $cfg['roomPrefix'] && $inventoryRoom == true)
    {
        $inventoryRoom = false;
        $html = $html . "<h1>Inventory Unset</h1>\n";
        $inventoryLocation = false;
        $inventoryBuilding = false;
        $inventoryDepartment = false;
        $inventoryBranchoffice = false;
    }
elseif (isset($barcodeValue) && $barcodeValue)
	{
        $assetID = GetAssetID($barcodeValue,$cfg['baseDomain']);
        $html = $html . "<script type=\"text/javascript\">startFadeDec(50, 255, 50, 255, 255, 255, 20);</script>\n";
        if ($assetID)
        {
            $assetType = GetAssetType($assetID,$cfg['baseDomain']);
            if ($assetType == 901 || $assetType == 908)
            {
                if ($assetID == $assetRelationParentID)
                {
                    $html = $html . "<h1>Container Unset</h1>\n";
                    EchoAssetLinks($baseLSURL,$assetID);
                    $addContainerAssetRelation = false;
                    $assetRelationParentID = '';
    
                }
                else
                {
                    $html = $html . "<h1>Container Set</h1>\n";
                    SetAssetCustomBarcodeScanTime($assetID,$cfg['baseDomain']);
                    InsertAssetCommentBarcodeScanTime($assetID,$cfg['baseDomain']);
                    EchoAssetLinks($baseLSURL,$assetID);
                    $addContainerAssetRelation = true;
                    $assetRelationParentID = $assetID;
                }
            }
            else
            {
                if ($addContainerAssetRelation)
                {
                    $html = $html . "<h1>Adding Asset Relationship</h1>\n";
                    SetAssetCustomBarcodeScanTime($assetID,$cfg['baseDomain']);
                    InsertAssetCommentBarcodeScanTime($assetID,$cfg['baseDomain']);
                    EchoAssetLinks($baseLSURL,$assetID);
                    AddAssetRelationToParent($assetID,$assetRelationParentID,$cfg['baseDomain'],$containerAssetRelationshipType);
                }
                else
                {
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
                    $html = $html . "<h2>User Relationships</h2>\n<hr />";
                    PrintAssetUserRelations(GetAssetUserRelations($assetID,$cfg['baseDomain']));
                    $html = $html . "<h2>Open Tickets</h2>\n<hr />";
                    PrintUserTickets(array_unique(GetAssetTickets($assetID,$cfg['baseDomain'])));
                }
            }
        }
        else
        {
            $html = $html . "<p>Barcode Invalid!</p>\n";
            $html = $html . "<script type=\"text/javascript\">startFadeDec(255, 50, 50, 255, 255, 255, 20);</script>\n";

        }
    }
    elseif ($previousAssetID)
    {
        switch ($manualAction)
        {
            case 'startRepair':
                BeginAssetRelation($previousAssetID,$cfg['baseDomain'],'Repair','addws');
                SetDispositionTicketWork($previousAssetID,$cfg['baseDomain']);
                RefreshPage();
                break;
            case 'endRepair':
                EndAssetRelation($previousAssetID,$cfg['baseDomain'],'Repair');
                SetDispositionReadyToDeploy($previousAssetID,$cfg['baseDomain']);
                RefreshPage();
                break;
            case 'endRelationships':
                EndAssetRelation($previousAssetID,$cfg['baseDomain'],'');
                RefreshPage();
                break;
            default:
        }
        $assetID = $previousAssetID;
        if ($assetID)
        {
            $assetType = GetAssetType($assetID,$cfg['baseDomain']);

            PrintAssetInventoryInfo($cfg['baseDomain'],GetAssetInventoryInfo($assetID,$cfg['baseDomain']),$assetID);
            PrintAssetUserRelations(GetAssetUserRelations($assetID,$cfg['baseDomain']));
            PrintUserTickets(array_unique(GetAssetTickets($assetID,$cfg['baseDomain'])));
        }

    }
    else
    {
        $html = $html . "<h2>Please enter/scan a valid Asset Tag Barcode</h2>\n";
    }

$html = $html . "<div id=\"manualButtonBox\"><a href=\"?manualAction=startRepair\"><button class=\"button button4\">Start Repair</button></a>&nbsp;<a href=\"?manualAction=endRepair\"><button class=\"button button4\">End Repair</button></a>&nbsp;<a href=\"?manualAction=endRelationships\"><button class=\"button button4\">End Relationships</button></a>&nbsp;</div>";

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
