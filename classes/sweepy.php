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

$html = <<<HTML
    <!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Sweepy</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css" />
    <script type="text/javascript">
      function stBrowserDidScanBarcode(type, data, stid) {
        document.getElementById("barcodeBox").value=data;
        document.forms[0].submit();
      }
      
      <!--
////////////////////////////////////////
// FP 10/2001
// background fade function
////////////////////////////////////////
var numSteps=0;
var startingRed=0;
var startingGreen=0;
var startingBlue=0;
var endingRed=0;
var endingGreen=0;
var endingBlue=0;
var deltaRed=0;
var deltaGreen=0;
var deltaBlue=0;
var currentRed=0;
var currentGreen=0;
var currentBlue=0;
var currentStep=0;
var timerID=0;

////////////////////////////////////////
// fade timer
////////////////////////////////////////
function startFadeDec(startR, startG, startB, 
   endR, endG, endB, nSteps)
{
//alert("sf");
    // need to parse, otherwise it thinks it's not a number
      currentRed=startingRed=parseInt(startR, 10);
      currentGreen=startingGreen=parseInt(startG, 10);
      currentBlue=startingBlue=parseInt(startB, 10);
      endingRed=parseInt(endR, 10);
      endingGreen=parseInt(endG, 10);
      endingBlue=parseInt(endB, 10);
      numSteps=parseInt(nSteps, 10);
      deltaRed=(endingRed-startingRed)/numSteps;
      deltaGreen=(endingGreen-startingGreen)/numSteps;
    deltaBlue=(endingBlue-startingBlue)/numSteps;
    currentStep=0;
    
/*    alert("cr="+currentRed+" cg="+currentGreen+" cb="+currentBlue);
    alert("dr="+deltaRed+" dg="+deltaGreen+" db="+deltaBlue);
    alert("er="+endingRed+" eg="+endingGreen+" eb="+endingBlue);
*/    
      fade();
}
  
////////////////////////////////////////
// fade timer
////////////////////////////////////////
function fade()
{
//    alert(color);
//      alert(document.bgColor);
      
      currentStep++;
      // if not done yet, change the backround
      if (currentStep<=numSteps)
      {
        // convert to hex    
        var hexRed=decToHex(currentRed);
        var hexGreen=decToHex(currentGreen);
        var hexBlue=decToHex(currentBlue);
    
        var color="#"+hexRed+""+hexGreen+""+hexBlue+"";
//    alert(color);
        
          document.bgColor=color;
//      alert(document.bgColor);

        // increment color
        currentRed+=deltaRed;
        currentGreen+=deltaGreen;
        currentBlue+=deltaBlue;
//    alert("cr="+currentRed+" cg="+currentGreen+" cb="+currentBlue);
        
          timerID=setTimeout("fade()", 200); // sets timer so that this function will
                                           // be called every 10 miliseconds
   }
}

////////////////////////////////////////
// convert decimal to hexadecimal number
////////////////////////////////////////
function decToHex(decNum)
{
//alert ("1");
    decNum=Math.floor(decNum);
    var decString=""+decNum;
    // make sure the number is valid
    for (var i=0; i<decString.length; i++)
    {
//alert ("2");
    
        if (decString.charAt(i)>='0' && decString.charAt(i)<='9')
        {
        }
        else
        {
            alert(decString+" is not a valid decimal number because it contains "+decString.charAt(i));
             return decNum;
        }
    }
    var result=decNum;
    var remainder="";
    // use string because math operation won't work with hex alphabet
    var hexNum="";

    var hexAlphabet=new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
//    alert("converting "+decNum+" to "+hexNum);
    while (result>0)
    {
        result=Math.floor(decNum/16);
        remainder=decNum%16;
        decNum=result;

/*        if (remainder>=10)
        {
            // use double quotes because Netscape 3 will give error if using single quote
            if (remainder==10)
                remainder="A";
            if (remainder==11)
                remainder="B";
            if (remainder==12)
                remainder="C";
            if (remainder==13)
                remainder="D";
            if (remainder==14)
                remainder="E";
            if (remainder==15)
                remainder="F";
        }*/
        // just append the next remainder to the beginning of the string
        hexNum=""+hexAlphabet[remainder]+""+hexNum;
    };
//    alert("converting "+decNum+" to "+hexNum);
    // make sure to have at least 2 digits
    if (hexNum.length==1)
        hexNum="0"+hexNum;
    else if (hexNum.length==0)
        hexNum="00";
    return hexNum;
}   

function fadeRandom()
{
    startFadeDec(sR, sG, sB, eR, eG, eB, 30);
}
// -->

      
    </script>
    <meta name="viewport" content="width=device-width; initial-scale=1.0; user-scalable=no" />
  </head>
  <body>
HTML;

function PrintAssetExplosion($explodedBarcode)
    {
        global $html;
            $html = $html . "Start Explosion\n";
            $html = $html . $barcodeValue;
            $html = $html . "\n";
            $html = $html . $explodedBarcode . "\n";
            $html = $html . "\nEnd Explosion\n";
            
            $html = $html . "<p>Location: " . $explodedBarcode[1] . "\n</p>";
            $html = $html . "<p>Building: " . $explodedBarcode[2] . "\n</p>";
            $html = $html . "<p>Department: " . $explodedBarcode[3] . "\n</p>";
            $html = $html . "<p>Branchoffice: " . $explodedBarcode[4] . "\n</p>";
    }

function AddAssetRelationToParent($assetID,$assetRelationParentID,$baseDomain,$containerAssetRelationshipType)
    {
        global $html;
        if (!CheckAssetRelationToParent($assetID,$assetRelationParentID,$baseDomain,$containerAssetRelationshipType))
        {
        $html = $html . "<h1>" . CheckAssetRelationToParent($assetID,$assetRelationParentID,$baseDomain,$containerAssetRelationshipType) . "</h1>";
        $html = $html . "<!-- Relation Does Not Exist -->";
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
            $html = $html . "Error!";
        }
        } else { $html = $html . "<!-- Relation Exists -->"; }
    }

function CheckAssetRelationToParent($assetID,$assetRelationParentID,$baseDomain,$containerAssetRelationshipType)
    {
        global $html;
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
            $html = $html . "Error!";
        }
    }


function EchoAssetLinks($baseLSURL,$assetID)
    {
        global $html;
        $html = $html . "<p><a href=\"" . $baseLSURL . $assetID . "\" target=\"_blank\">Open in new page</a></p>\n";
//        $html = $html . "<div height=\"100%\"><iframe src=\"" . $baseLSURL . $assetID . "\" width=\"100%\" height=\"410px\" /></div>\n";
    }

function PrintAssetInfo($assetName)
    {
        global $html;
        if(debug){$html = $html . "<h3>Asset Name:</h3>\n";}
        $html = $html . "<h2>" . $assetName . "</h2>\n";
    }

function PrintAssetInventoryInfo($baseLSURL,$assetInfoArray,$assetID)
    {
        global $html;
        if(debug){print_r($assetInfoArray);}
        $html = $html . "<a href=\"https://" . $baseLSURL . "/asset.aspx?AssetID=" . $assetID . "\" target=\"_blank\"><h3>Asset: " . $assetInfoArray[0] . "</h3></a>\n";
        $html = $html . "<p>Location: " . $assetInfoArray[1] . "</p>\n";
        $html = $html . "<p>Building: " . $assetInfoArray[2] . "</p>\n";
        $html = $html . "<p>Department: " . $assetInfoArray[3] . "</p>\n";
        $html = $html . "<p>Branch Office:" . $assetInfoArray[4] . "</p>\n";
        $html = $html . "<p>Last Physical Inventory: " . $assetInfoArray[5] . "</p>\n";
    }

function PrintAssetUserRelations($relationsArray)
    {
        global $html;
        if(debug){print_r($relationsArray);}
        foreach($relationsArray as $field) {
            $html = $html . "<h3>" . $field . "</h3>\n";
        }
    }


function OpenConnection($baseDomain)
    {
        global $html;
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
            $html = $html . "Error!";
        }
    }

function Alert($message)
{
        global $html;
    if (debug)
    {
        $html = $html . "<script type=\"text/javascript\">alert(\"" . $message . "\");</script>";
    }
}


function GetAssetID($barcodeValue,$baseDomain)
    {
        global $html;
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
            $html = $html . "Error!";
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


function GetAssetInfo($assetId,$baseDomain)
    {
        global $html;
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
            $html = $html . "Error!";
        }
    }

function GetAssetInventoryInfo($assetId,$baseDomain)
    {
        global $html;
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "SELECT dbo.tblAssets.AssetName,dbo.tblAssetCustom.Location,dbo.tblAssetCustom.Building,dbo.tblAssetCustom.Department,dbo.tblAssetCustom.Branchoffice,dbo.tblAssetCustom.Custom15 FROM dbo.tblAssets inner join dbo.tblAssetCustom on dbo.tblAssets.AssetID=dbo.tblAssetCustom.AssetID where dbo.tblAssets.AssetID=" . $assetId;
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
//            Alert($getAsset);
            while ($row = sqlsrv_fetch_array($getAsset))
                {
//                    Alert($row);
                    return array(htmlspecialchars($row['AssetName']),htmlspecialchars($row['Location']),htmlspecialchars($row['Building']),htmlspecialchars($row['Department']),htmlspecialchars($row['Branchoffice']),htmlspecialchars($row['Custom15']));
//                    $assetName,$assetLocation,$assetBuilding,$assetDepartment,$assetBranchOffice
                }
            sqlsrv_free_stmt($getAsset);
            sqlsrv_close($conn);
        }
        catch(Exception $e)
        {
            $html = $html . "Error!";
        }
    }

function GetAssetUserRelations($assetId,$baseDomain)
    {
        global $html;
        $relationsArray = array();
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "select dbo.tblAssetUserRelations.Username,tblAssetUserRelations.StartDate,tblAssetUserRelations.EndDate,tblAssetUserRelations.Comments from dbo.tblAssetUserRelations where dbo.tblAssetUserRelations.AssetID=" . $assetId;
            $getAsset = sqlsrv_query($conn, $tsql);
            if ($getAsset == FALSE)
                die(sqlsrv_errors());
//            Alert($getAsset);
            while ($row = sqlsrv_fetch_array($getAsset))
                {
                    Alert($row);
                    $relationsArray[] = ('<a href="https://' . $baseDomain . '/user.aspx?username=' . $row['Username'] . '&userdomain=orcsd" target="_blank">' . $row['Username'] . '</a> Since ' . date_format($row['StartDate'], 'Y-m-d') . ' through ' . date_format($row['EndDate'], 'Y-m-d') . ' Comments: ' . $row['Comments'] . '');
//                    $assetName,$assetLocation,$assetBuilding,$assetDepartment,$assetBranchOffice
                }
            sqlsrv_free_stmt($getAsset);
            sqlsrv_close($conn);
            return $relationsArray;
        }
        catch(Exception $e)
        {
            $html = $html . "Error!";
        }
    }

function UpdateAssetInventory($assetID,$assetLocation,$assetBuilding,$assetDepartment,$assetBranchoffice,$baseDomain)
    {
        global $html;
        try
        {
            $conn = OpenConnection($baseDomain);
            $tsql = "UPDATE dbo.tblAssetCustom SET dbo.tblAssetCustom.Location='" . $assetLocation . "',dbo.tblAssetCustom.Building='" . $assetBuilding . "',dbo.tblAssetCustom.Department='" . $assetDepartment . "',dbo.tblAssetCustom.Branchoffice='" . $assetBranchoffice . "' where AssetID=" . $assetID;
            if(debug){$html = $html . "SQL: " . $tsql;}
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
            $html = $html . "Error!";
        }
    }

function GetAssetType($assetID,$baseDomain)
    {
        global $html;
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
            $html = $html . "Error!";
        }
    }

function SetAssetCustomBarcodeScanTime($assetID,$baseDomain)
    {
        global $html;
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
            $html = $html . "Error!";
        }
    }

function InsertAssetCommentBarcodeScanTime($assetID,$baseDomain,$comment="Barcode Scanned")
    {
        global $html;
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
            $html = $html . "Error!";
        }
    }

/*function PrintAssetInventoryInfo($assetInfoArray)
    {
        global $html;
        if(debug){print_r($assetInfoArray);}
        $html = $html . "<h3>Asset: " . $assetInfoArray[0] . "</h3>\n";
        $html = $html . "<p>Location: " . $assetInfoArray[1] . "</p>\n";
        $html = $html . "<p>Building: " . $assetInfoArray[2] . "</p>\n";
        $html = $html . "<p>Department: " . $assetInfoArray[3] . "</p>\n";
        $html = $html . "<p>Branch Office:" . $assetInfoArray[4] . "</p>\n";
    }*/


function InsertAssetCommentAuditTrail($assetID,$baseDomain,$inventoryInfoArray,$isNew)
    {
        global $html;
        if ($isNew)
            {
                $comment = "Audit: New L: " . $inventoryInfoArray[1] . " B: " . $inventoryInfoArray[2] . " D: " . $inventoryInfoArray[3] . " bO: " . $inventoryInfoArray[4];
            }
        else
            {
                $comment = "Audit: Was L: " . $inventoryInfoArray[1] . " B: " . $inventoryInfoArray[2] . " D: " . $inventoryInfoArray[3] . " bO: " . $inventoryInfoArray[4];
            }
        $sqlComment = htmlspecialchars($comment);
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
            $html = $html . "Error!";
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