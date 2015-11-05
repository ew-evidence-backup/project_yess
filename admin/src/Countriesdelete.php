<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "Countriesinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$Countries_delete = NULL; // Initialize page object first

class cCountries_delete extends cCountries {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{A1E1A318-A966-4120-A8D8-F8227648CCB2}";

	// Table name
	var $TableName = 'Countries';

	// Page object name
	var $PageObjName = 'Countries_delete';

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

		// Table object (Countries)
		if (!isset($GLOBALS["Countries"]) || get_class($GLOBALS["Countries"]) == "cCountries") {
			$GLOBALS["Countries"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Countries"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'Countries', TRUE);

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
		$this->CountryID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->Page_Terminate("Countrieslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in Countries class, Countriesinfo.php

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
		$this->CountryID->setDbValue($rs->fields('CountryID'));
		$this->Code->setDbValue($rs->fields('Code'));
		$this->Name->setDbValue($rs->fields('Name'));
		$this->FullName->setDbValue($rs->fields('FullName'));
		$this->ISO3->setDbValue($rs->fields('ISO3'));
		$this->Number->setDbValue($rs->fields('Number'));
		$this->ContinentCode->setDbValue($rs->fields('ContinentCode'));
		$this->DisplayOrder->setDbValue($rs->fields('DisplayOrder'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->CountryID->DbValue = $row['CountryID'];
		$this->Code->DbValue = $row['Code'];
		$this->Name->DbValue = $row['Name'];
		$this->FullName->DbValue = $row['FullName'];
		$this->ISO3->DbValue = $row['ISO3'];
		$this->Number->DbValue = $row['Number'];
		$this->ContinentCode->DbValue = $row['ContinentCode'];
		$this->DisplayOrder->DbValue = $row['DisplayOrder'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// CountryID
		// Code
		// Name
		// FullName
		// ISO3
		// Number
		// ContinentCode
		// DisplayOrder

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// CountryID
			$this->CountryID->ViewValue = $this->CountryID->CurrentValue;
			$this->CountryID->ViewCustomAttributes = "";

			// Code
			$this->Code->ViewValue = $this->Code->CurrentValue;
			$this->Code->ViewCustomAttributes = "";

			// Name
			$this->Name->ViewValue = $this->Name->CurrentValue;
			$this->Name->ViewCustomAttributes = "";

			// FullName
			$this->FullName->ViewValue = $this->FullName->CurrentValue;
			$this->FullName->ViewCustomAttributes = "";

			// ISO3
			$this->ISO3->ViewValue = $this->ISO3->CurrentValue;
			$this->ISO3->ViewCustomAttributes = "";

			// Number
			$this->Number->ViewValue = $this->Number->CurrentValue;
			$this->Number->ViewCustomAttributes = "";

			// ContinentCode
			$this->ContinentCode->ViewValue = $this->ContinentCode->CurrentValue;
			$this->ContinentCode->ViewCustomAttributes = "";

			// DisplayOrder
			$this->DisplayOrder->ViewValue = $this->DisplayOrder->CurrentValue;
			$this->DisplayOrder->ViewCustomAttributes = "";

			// CountryID
			$this->CountryID->LinkCustomAttributes = "";
			$this->CountryID->HrefValue = "";
			$this->CountryID->TooltipValue = "";

			// Code
			$this->Code->LinkCustomAttributes = "";
			$this->Code->HrefValue = "";
			$this->Code->TooltipValue = "";

			// Name
			$this->Name->LinkCustomAttributes = "";
			$this->Name->HrefValue = "";
			$this->Name->TooltipValue = "";

			// FullName
			$this->FullName->LinkCustomAttributes = "";
			$this->FullName->HrefValue = "";
			$this->FullName->TooltipValue = "";

			// ISO3
			$this->ISO3->LinkCustomAttributes = "";
			$this->ISO3->HrefValue = "";
			$this->ISO3->TooltipValue = "";

			// Number
			$this->Number->LinkCustomAttributes = "";
			$this->Number->HrefValue = "";
			$this->Number->TooltipValue = "";

			// ContinentCode
			$this->ContinentCode->LinkCustomAttributes = "";
			$this->ContinentCode->HrefValue = "";
			$this->ContinentCode->TooltipValue = "";

			// DisplayOrder
			$this->DisplayOrder->LinkCustomAttributes = "";
			$this->DisplayOrder->HrefValue = "";
			$this->DisplayOrder->TooltipValue = "";
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
				$sThisKey .= $row['CountryID'];
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
		$Breadcrumb->Add("list", $this->TableVar, "Countrieslist.php", $this->TableVar, TRUE);
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
if (!isset($Countries_delete)) $Countries_delete = new cCountries_delete();

// Page init
$Countries_delete->Page_Init();

// Page main
$Countries_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$Countries_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var Countries_delete = new ew_Page("Countries_delete");
Countries_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = Countries_delete.PageID; // For backward compatibility

// Form object
var fCountriesdelete = new ew_Form("fCountriesdelete");

// Form_CustomValidate event
fCountriesdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fCountriesdelete.ValidateRequired = true;
<?php } else { ?>
fCountriesdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($Countries_delete->Recordset = $Countries_delete->LoadRecordset())
	$Countries_deleteTotalRecs = $Countries_delete->Recordset->RecordCount(); // Get record count
if ($Countries_deleteTotalRecs <= 0) { // No record found, exit
	if ($Countries_delete->Recordset)
		$Countries_delete->Recordset->Close();
	$Countries_delete->Page_Terminate("Countrieslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $Countries_delete->ShowPageHeader(); ?>
<?php
$Countries_delete->ShowMessage();
?>
<form name="fCountriesdelete" id="fCountriesdelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="Countries">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($Countries_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_Countriesdelete" class="ewTable ewTableSeparate">
<?php echo $Countries->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($Countries->CountryID->Visible) { // CountryID ?>
		<td><span id="elh_Countries_CountryID" class="Countries_CountryID"><?php echo $Countries->CountryID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Countries->Code->Visible) { // Code ?>
		<td><span id="elh_Countries_Code" class="Countries_Code"><?php echo $Countries->Code->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Countries->Name->Visible) { // Name ?>
		<td><span id="elh_Countries_Name" class="Countries_Name"><?php echo $Countries->Name->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Countries->FullName->Visible) { // FullName ?>
		<td><span id="elh_Countries_FullName" class="Countries_FullName"><?php echo $Countries->FullName->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Countries->ISO3->Visible) { // ISO3 ?>
		<td><span id="elh_Countries_ISO3" class="Countries_ISO3"><?php echo $Countries->ISO3->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Countries->Number->Visible) { // Number ?>
		<td><span id="elh_Countries_Number" class="Countries_Number"><?php echo $Countries->Number->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Countries->ContinentCode->Visible) { // ContinentCode ?>
		<td><span id="elh_Countries_ContinentCode" class="Countries_ContinentCode"><?php echo $Countries->ContinentCode->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Countries->DisplayOrder->Visible) { // DisplayOrder ?>
		<td><span id="elh_Countries_DisplayOrder" class="Countries_DisplayOrder"><?php echo $Countries->DisplayOrder->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$Countries_delete->RecCnt = 0;
$i = 0;
while (!$Countries_delete->Recordset->EOF) {
	$Countries_delete->RecCnt++;
	$Countries_delete->RowCnt++;

	// Set row properties
	$Countries->ResetAttrs();
	$Countries->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$Countries_delete->LoadRowValues($Countries_delete->Recordset);

	// Render row
	$Countries_delete->RenderRow();
?>
	<tr<?php echo $Countries->RowAttributes() ?>>
<?php if ($Countries->CountryID->Visible) { // CountryID ?>
		<td<?php echo $Countries->CountryID->CellAttributes() ?>>
<span id="el<?php echo $Countries_delete->RowCnt ?>_Countries_CountryID" class="control-group Countries_CountryID">
<span<?php echo $Countries->CountryID->ViewAttributes() ?>>
<?php echo $Countries->CountryID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Countries->Code->Visible) { // Code ?>
		<td<?php echo $Countries->Code->CellAttributes() ?>>
<span id="el<?php echo $Countries_delete->RowCnt ?>_Countries_Code" class="control-group Countries_Code">
<span<?php echo $Countries->Code->ViewAttributes() ?>>
<?php echo $Countries->Code->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Countries->Name->Visible) { // Name ?>
		<td<?php echo $Countries->Name->CellAttributes() ?>>
<span id="el<?php echo $Countries_delete->RowCnt ?>_Countries_Name" class="control-group Countries_Name">
<span<?php echo $Countries->Name->ViewAttributes() ?>>
<?php echo $Countries->Name->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Countries->FullName->Visible) { // FullName ?>
		<td<?php echo $Countries->FullName->CellAttributes() ?>>
<span id="el<?php echo $Countries_delete->RowCnt ?>_Countries_FullName" class="control-group Countries_FullName">
<span<?php echo $Countries->FullName->ViewAttributes() ?>>
<?php echo $Countries->FullName->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Countries->ISO3->Visible) { // ISO3 ?>
		<td<?php echo $Countries->ISO3->CellAttributes() ?>>
<span id="el<?php echo $Countries_delete->RowCnt ?>_Countries_ISO3" class="control-group Countries_ISO3">
<span<?php echo $Countries->ISO3->ViewAttributes() ?>>
<?php echo $Countries->ISO3->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Countries->Number->Visible) { // Number ?>
		<td<?php echo $Countries->Number->CellAttributes() ?>>
<span id="el<?php echo $Countries_delete->RowCnt ?>_Countries_Number" class="control-group Countries_Number">
<span<?php echo $Countries->Number->ViewAttributes() ?>>
<?php echo $Countries->Number->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Countries->ContinentCode->Visible) { // ContinentCode ?>
		<td<?php echo $Countries->ContinentCode->CellAttributes() ?>>
<span id="el<?php echo $Countries_delete->RowCnt ?>_Countries_ContinentCode" class="control-group Countries_ContinentCode">
<span<?php echo $Countries->ContinentCode->ViewAttributes() ?>>
<?php echo $Countries->ContinentCode->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Countries->DisplayOrder->Visible) { // DisplayOrder ?>
		<td<?php echo $Countries->DisplayOrder->CellAttributes() ?>>
<span id="el<?php echo $Countries_delete->RowCnt ?>_Countries_DisplayOrder" class="control-group Countries_DisplayOrder">
<span<?php echo $Countries->DisplayOrder->ViewAttributes() ?>>
<?php echo $Countries->DisplayOrder->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$Countries_delete->Recordset->MoveNext();
}
$Countries_delete->Recordset->Close();
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
fCountriesdelete.Init();
</script>
<?php
$Countries_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$Countries_delete->Page_Terminate();
?>
