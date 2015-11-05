<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "EvaluateAnswersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$EvaluateAnswers_delete = NULL; // Initialize page object first

class cEvaluateAnswers_delete extends cEvaluateAnswers {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{A1E1A318-A966-4120-A8D8-F8227648CCB2}";

	// Table name
	var $TableName = 'EvaluateAnswers';

	// Page object name
	var $PageObjName = 'EvaluateAnswers_delete';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-error ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<table class=\"ewStdTable\"><tr><td><div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div></td></tr></table>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (EvaluateAnswers)
		if (!isset($GLOBALS["EvaluateAnswers"]) || get_class($GLOBALS["EvaluateAnswers"]) == "cEvaluateAnswers") {
			$GLOBALS["EvaluateAnswers"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["EvaluateAnswers"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'EvaluateAnswers', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->EvaluateAnswerID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("EvaluateAnswerslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in EvaluateAnswers class, EvaluateAnswersinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->EvaluateAnswerID->setDbValue($rs->fields('EvaluateAnswerID'));
		$this->EvAnswer->setDbValue($rs->fields('EvAnswer'));
		$this->lastModified->setDbValue($rs->fields('lastModified'));
		$this->Date->setDbValue($rs->fields('Date'));
		$this->ID->setDbValue($rs->fields('ID'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->EvaluateAnswerID->DbValue = $row['EvaluateAnswerID'];
		$this->EvAnswer->DbValue = $row['EvAnswer'];
		$this->lastModified->DbValue = $row['lastModified'];
		$this->Date->DbValue = $row['Date'];
		$this->ID->DbValue = $row['ID'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// EvaluateAnswerID
		// EvAnswer
		// lastModified
		// Date
		// ID

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// EvaluateAnswerID
			$this->EvaluateAnswerID->ViewValue = $this->EvaluateAnswerID->CurrentValue;
			$this->EvaluateAnswerID->ViewCustomAttributes = "";

			// lastModified
			$this->lastModified->ViewValue = $this->lastModified->CurrentValue;
			$this->lastModified->ViewValue = ew_FormatDateTime($this->lastModified->ViewValue, 5);
			$this->lastModified->ViewCustomAttributes = "";

			// Date
			$this->Date->ViewValue = $this->Date->CurrentValue;
			$this->Date->ViewValue = ew_FormatDateTime($this->Date->ViewValue, 5);
			$this->Date->ViewCustomAttributes = "";

			// ID
			if (strval($this->ID->CurrentValue) <> "") {
				$sFilterWrk = "`ID`" . ew_SearchString("=", $this->ID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `ID`, `Email` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `Users`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->ID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->ID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->ID->ViewValue = $this->ID->CurrentValue;
				}
			} else {
				$this->ID->ViewValue = NULL;
			}
			$this->ID->ViewCustomAttributes = "";

			// EvaluateAnswerID
			$this->EvaluateAnswerID->LinkCustomAttributes = "";
			$this->EvaluateAnswerID->HrefValue = "";
			$this->EvaluateAnswerID->TooltipValue = "";

			// lastModified
			$this->lastModified->LinkCustomAttributes = "";
			$this->lastModified->HrefValue = "";
			$this->lastModified->TooltipValue = "";

			// Date
			$this->Date->LinkCustomAttributes = "";
			$this->Date->HrefValue = "";
			$this->Date->TooltipValue = "";

			// ID
			$this->ID->LinkCustomAttributes = "";
			$this->ID->HrefValue = "";
			$this->ID->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$conn->BeginTrans();

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['EvaluateAnswerID'];
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
		} else {
			$conn->RollbackTrans(); // Rollback changes
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "EvaluateAnswerslist.php", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, ew_CurrentUrl());
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($EvaluateAnswers_delete)) $EvaluateAnswers_delete = new cEvaluateAnswers_delete();

// Page init
$EvaluateAnswers_delete->Page_Init();

// Page main
$EvaluateAnswers_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$EvaluateAnswers_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var EvaluateAnswers_delete = new ew_Page("EvaluateAnswers_delete");
EvaluateAnswers_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = EvaluateAnswers_delete.PageID; // For backward compatibility

// Form object
var fEvaluateAnswersdelete = new ew_Form("fEvaluateAnswersdelete");

// Form_CustomValidate event
fEvaluateAnswersdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fEvaluateAnswersdelete.ValidateRequired = true;
<?php } else { ?>
fEvaluateAnswersdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fEvaluateAnswersdelete.Lists["x_ID"] = {"LinkField":"x_ID","Ajax":null,"AutoFill":false,"DisplayFields":["x__Email","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($EvaluateAnswers_delete->Recordset = $EvaluateAnswers_delete->LoadRecordset())
	$EvaluateAnswers_deleteTotalRecs = $EvaluateAnswers_delete->Recordset->RecordCount(); // Get record count
if ($EvaluateAnswers_deleteTotalRecs <= 0) { // No record found, exit
	if ($EvaluateAnswers_delete->Recordset)
		$EvaluateAnswers_delete->Recordset->Close();
	$EvaluateAnswers_delete->Page_Terminate("EvaluateAnswerslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $EvaluateAnswers_delete->ShowPageHeader(); ?>
<?php
$EvaluateAnswers_delete->ShowMessage();
?>
<form name="fEvaluateAnswersdelete" id="fEvaluateAnswersdelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="EvaluateAnswers">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($EvaluateAnswers_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_EvaluateAnswersdelete" class="ewTable ewTableSeparate">
<?php echo $EvaluateAnswers->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($EvaluateAnswers->EvaluateAnswerID->Visible) { // EvaluateAnswerID ?>
		<td><span id="elh_EvaluateAnswers_EvaluateAnswerID" class="EvaluateAnswers_EvaluateAnswerID"><?php echo $EvaluateAnswers->EvaluateAnswerID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($EvaluateAnswers->lastModified->Visible) { // lastModified ?>
		<td><span id="elh_EvaluateAnswers_lastModified" class="EvaluateAnswers_lastModified"><?php echo $EvaluateAnswers->lastModified->FldCaption() ?></span></td>
<?php } ?>
<?php if ($EvaluateAnswers->Date->Visible) { // Date ?>
		<td><span id="elh_EvaluateAnswers_Date" class="EvaluateAnswers_Date"><?php echo $EvaluateAnswers->Date->FldCaption() ?></span></td>
<?php } ?>
<?php if ($EvaluateAnswers->ID->Visible) { // ID ?>
		<td><span id="elh_EvaluateAnswers_ID" class="EvaluateAnswers_ID"><?php echo $EvaluateAnswers->ID->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$EvaluateAnswers_delete->RecCnt = 0;
$i = 0;
while (!$EvaluateAnswers_delete->Recordset->EOF) {
	$EvaluateAnswers_delete->RecCnt++;
	$EvaluateAnswers_delete->RowCnt++;

	// Set row properties
	$EvaluateAnswers->ResetAttrs();
	$EvaluateAnswers->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$EvaluateAnswers_delete->LoadRowValues($EvaluateAnswers_delete->Recordset);

	// Render row
	$EvaluateAnswers_delete->RenderRow();
?>
	<tr<?php echo $EvaluateAnswers->RowAttributes() ?>>
<?php if ($EvaluateAnswers->EvaluateAnswerID->Visible) { // EvaluateAnswerID ?>
		<td<?php echo $EvaluateAnswers->EvaluateAnswerID->CellAttributes() ?>>
<span id="el<?php echo $EvaluateAnswers_delete->RowCnt ?>_EvaluateAnswers_EvaluateAnswerID" class="control-group EvaluateAnswers_EvaluateAnswerID">
<span<?php echo $EvaluateAnswers->EvaluateAnswerID->ViewAttributes() ?>>
<?php echo $EvaluateAnswers->EvaluateAnswerID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($EvaluateAnswers->lastModified->Visible) { // lastModified ?>
		<td<?php echo $EvaluateAnswers->lastModified->CellAttributes() ?>>
<span id="el<?php echo $EvaluateAnswers_delete->RowCnt ?>_EvaluateAnswers_lastModified" class="control-group EvaluateAnswers_lastModified">
<span<?php echo $EvaluateAnswers->lastModified->ViewAttributes() ?>>
<?php echo $EvaluateAnswers->lastModified->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($EvaluateAnswers->Date->Visible) { // Date ?>
		<td<?php echo $EvaluateAnswers->Date->CellAttributes() ?>>
<span id="el<?php echo $EvaluateAnswers_delete->RowCnt ?>_EvaluateAnswers_Date" class="control-group EvaluateAnswers_Date">
<span<?php echo $EvaluateAnswers->Date->ViewAttributes() ?>>
<?php echo $EvaluateAnswers->Date->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($EvaluateAnswers->ID->Visible) { // ID ?>
		<td<?php echo $EvaluateAnswers->ID->CellAttributes() ?>>
<span id="el<?php echo $EvaluateAnswers_delete->RowCnt ?>_EvaluateAnswers_ID" class="control-group EvaluateAnswers_ID">
<span<?php echo $EvaluateAnswers->ID->ViewAttributes() ?>>
<?php echo $EvaluateAnswers->ID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$EvaluateAnswers_delete->Recordset->MoveNext();
}
$EvaluateAnswers_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fEvaluateAnswersdelete.Init();
</script>
<?php
$EvaluateAnswers_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$EvaluateAnswers_delete->Page_Terminate();
?>
