<?php
/* $Revision: 1.3 $ */
$title = "Receive Controlled Items";
$PageSecurity = 11;

/* Session started in header.inc for password checking and authorisation level check */
include("includes/DefinePOClass.php");
include("includes/DefineSerialItems.php");
include("includes/session.inc");
include("includes/header.inc");

if (!isset($_SESSION['PO'])) {
	/* This page can only be called with a purchase order number for receiving*/
	echo "<CENTER><A HREF='" . $rootpath . "/PO_SelectPurchOrder.php?" . SID . "'>Select a purchase order to receive</A></CENTER><br>";
	prnMsg("<BR>This page can only be opened if a purchase order and line item has been selected. Please do that first.<BR>","error");
	include( "includes/footer.inc");
	exit;

}

if ($_GET['LineNo']>0){
	$LineNo = $_GET["LineNo"];
} else if ($_POST['LineNo']>0){
	$LineNo = $_POST["LineNo"];
} else {
	echo "<CENTER><A HREF='" . $rootpath . "/GoodsReceived.php?" . SID . "'>Select a line Item to Receive</A></CENTER>";
	prnMsg("<BR>This page can only be opened if a Line Item on a PO has been selected. Please do that first.<BR>", "error");
	include( "includes/footer.inc");
	exit;
}

$LineItem = &$_SESSION['PO']->LineItems[$LineNo];

if ($LineItem->Controlled !=1 ){ /*This page only relavent for controlled items */

	echo "<CENTER><A HREF='" . $rootpath . "/GoodsReceived.php?" . SID . "'>Back to the Purchase Order</A></CENTER>";
	prnMsg("<BR>Notice - the line being recevied must be controlled as defined in the item defintion", "error");
	include( "includes/footer.inc");
	exit;
}

if ($_POST['AddBatches']=='Enter'){

	for ($i=0;$i < 10;$i++){
		if($_POST['SerialNo' . $i] != ""){
			/*If the user enters a duplicate serial number the later one over-writes
			the first entered one - no warning given though ? */
			$LineItem->SerialItems[$_POST['SerialNo' . $i]] = new SerialItem ($_POST['SerialNo' . $i], $_POST['Qty' . $i]);

		}
	}
}

if (isset($_GET['Delete'])){
	unset($LineItem->SerialItems[$_GET['Delete']]);
}

echo "<CENTER><FORM METHOD='POST' ACTION='" . $_SERVER['PHP_SELF'] . "?" . SID . "'>";

echo "<INPUT TYPE=HIDDEN NAME='LineNo' VALUE=$LineNo>";

echo "<br><a href='$rootpath/GoodsReceived.php?" . SID . "'>Back To Purchase Order # " . $_SESSION['PO']->OrderNo . "</a>";

echo "<br><FONT SIZE=2><B>Receive controlled item " . $LineItem->StockID  . " - " . $LineItem->ItemDescription . " on order " . $_SESSION['PO']->OrderNo . " from " . $_SESSION['PO']->SupplierName . "</B></FONT>";


include ("includes/InputSerialItems.php");


/*TotalQuantity set inside this include file from the sum of the bundles
of the item selected for dispatch */
$_SESSION['PO']->LineItems[$LineItem->LineNo]->ReceiveQty = $TotalQuantity;

echo "</TR></table><br><INPUT TYPE=SUBMIT NAME='AddBatches' VALUE='Enter'><BR>";

echo "</FORM>";
include( "includes/footer.inc");
exit;
?>

