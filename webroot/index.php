<?php

    require_once '../classes/sweepy.php';

    $barcodeValue = (isset($_REQUEST["barcodeValue"])?$_REQUEST["barcodeValue"]:"");

$html = $html . "
<h3>Sweepy Barcode Scanner Companion</h3>
<p></p>
<form action=\"#\" method=\"post\" name=\"barcodeForm\">
<p><label>Barcode: </label><input type=\"text\" id=\"barcodeBox\" name=\"barcodeValue\" value=\"\"/ /><button type=\"submit\">Submit</button></p>
</form>
";

$baseLSURL = "https://" . $cfg['baseDomain'] . "/asset.aspx?AssetID=";
$containerAssetRelationshipType = "5";

            $explodedBarcode = explode("_",$barcodeValue);
//            PrintAssetExplosion($explodedBarcode);

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
elseif ($barcodeValue) {
    $assetID = GetAssetID($barcodeValue,$cfg['baseDomain']);
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
            $html = $html . "<p>Barcode Invalid!</p>\n";
            $html = $html . "<script type=\"text/javascript\">startFadeDec(255, 50, 50, 255, 255, 255, 20);</script>\n";

        }
} else { $html = $html . "<h2>Please enter/scan a valid Asset Tag Barcode</h2>\n"; }

$html = $html . "        <div class=\"controls\">
            <fieldset class=\"input-group\">
                <button class=\"stop\">Stop</button>
            </fieldset>
            <fieldset class=\"reader-config-group\">
                <label>
                    <span>Barcode-Type</span>
                    <select name=\"decoder_readers\">
                        <option value=\"code_128\" selected=\"selected\">Code 128</option>
                        <option value=\"code_39\">Code 39</option>
                        <option value=\"code_39_vin\">Code 39 VIN</option>
                        <option value=\"ean\">EAN</option>
                        <option value=\"ean_extended\">EAN-extended</option>
                        <option value=\"ean_8\">EAN-8</option>
                        <option value=\"upc\">UPC</option>
                        <option value=\"upc_e\">UPC-E</option>
                        <option value=\"codabar\">Codabar</option>
                        <option value=\"i2of5\">Interleaved 2 of 5</option>
                        <option value=\"2of5\">Standard 2 of 5</option>
                        <option value=\"code_93\">Code 93</option>
                    </select>
                </label>
                <label>
                    <span>Resolution (width)</span>
                    <select name=\"input-stream_constraints\">
                        <option value=\"320x240\">320px</option>
                        <option selected=\"selected\" value=\"640x480\">640px</option>
                        <option value=\"800x600\">800px</option>
                        <option value=\"1280x720\">1280px</option>
                        <option value=\"1600x960\">1600px</option>
                        <option value=\"1920x1080\">1920px</option>
                    </select>
                </label>
                <label>
                    <span>Patch-Size</span>
                    <select name=\"locator_patch-size\">
                        <option value=\"x-small\">x-small</option>
                        <option value=\"small\">small</option>
                        <option selected=\"selected\" value=\"medium\">medium</option>
                        <option value=\"large\">large</option>
                        <option value=\"x-large\">x-large</option>
                    </select>
                </label>
                <label>
                    <span>Half-Sample</span>
                    <input type=\"checkbox\" checked=\"checked\" name=\"locator_half-sample\" />
                </label>
                <label>
                    <span>Workers</span>
                    <select name=\"numOfWorkers\">
                        <option value=\"0\">0</option>
                        <option value=\"1\">1</option>
                        <option value=\"2\">2</option>
                        <option selected=\"selected\" value=\"4\">4</option>
                        <option value=\"8\">8</option>
                    </select>
                </label>
                <label>
                    <span>Camera</span>
                    <select name=\"input-stream_constraints\" id=\"deviceSelection\">
                    </select>
                </label>
                <label style=\"display: none\">
                    <span>Zoom</span>
                    <select name=\"settings_zoom\"></select>
                </label>
                <label style=\"display: none\">
                    <span>Torch</span>
                    <input type=\"checkbox\" name=\"settings_torch\" />
                </label>
            </fieldset>
        </div>
      <div id=\"result_strip\">
        <ul class=\"thumbnails\"></ul>
        <ul class=\"collector\"></ul>
      </div>
      <div id=\"interactive\" class=\"viewport\"></div>
    </section>
    <script src=\"/quaggaJS/example/vendor/jquery-1.9.0.min.js\" type=\"text/javascript\"></script>
    <script src=\"//webrtc.github.io/adapter/adapter-latest.js\" type=\"text/javascript\"></script>
    <script src=\"/quaggaJS/dist/quagga.js\" type=\"text/javascript\"></script>
    <script src=\"/quaggaJS/example/live_w_locator.js\" type=\"text/javascript\"></script>
";

echo($html);

?>
