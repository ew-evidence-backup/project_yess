<!-- Begin Main Menu -->
<div class="ewMenu">
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(18, $Language->MenuPhrase("18", "MenuText"), "", -1, "", IsLoggedIn(), TRUE, TRUE);
$RootMenu->AddMenuItem(14, $Language->MenuPhrase("14", "MenuText"), "Userslist.php", 18, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(13, $Language->MenuPhrase("13", "MenuText"), "UserTypeslist.php", 18, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(15, $Language->MenuPhrase("15", "MenuText"), "UsersMetalist.php", 18, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(19, $Language->MenuPhrase("19", "MenuText"), "", -1, "", IsLoggedIn(), TRUE, TRUE);
$RootMenu->AddMenuItem(1, $Language->MenuPhrase("1", "MenuText"), "Answerslist.php", 19, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(6, $Language->MenuPhrase("6", "MenuText"), "EvaluateAnswerslist.php", 19, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(7, $Language->MenuPhrase("7", "MenuText"), "EvaluateQuestionslist.php", 19, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(4, $Language->MenuPhrase("4", "MenuText"), "Essayslist.php", 19, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(5, $Language->MenuPhrase("5", "MenuText"), "Evaluatelist.php", 19, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(8, $Language->MenuPhrase("8", "MenuText"), "Scoreslist.php", 19, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(11, $Language->MenuPhrase("11", "MenuText"), "TopicQuestionslist.php", 19, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(12, $Language->MenuPhrase("12", "MenuText"), "Topicslist.php", 19, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(9, $Language->MenuPhrase("9", "MenuText"), "Showlist.php", 19, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(21, $Language->MenuPhrase("21", "MenuText"), "", -1, "", IsLoggedIn(), TRUE, TRUE);
$RootMenu->AddMenuItem(2, $Language->MenuPhrase("2", "MenuText"), "Continentslist.php", 21, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(3, $Language->MenuPhrase("3", "MenuText"), "Countrieslist.php", 21, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(10, $Language->MenuPhrase("10", "MenuText"), "Stateslist.php", 21, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(22, $Language->MenuPhrase("22", "MenuText"), "", -1, "", IsLoggedIn(), TRUE, TRUE);
$RootMenu->AddMenuItem(17, $Language->MenuPhrase("17", "MenuText"), "Videoslist.php", 22, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(16, $Language->MenuPhrase("16", "MenuText"), "VideoCategorieslist.php", 22, "", IsLoggedIn(), FALSE);
$RootMenu->AddMenuItem(-1, $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
</div>
<!-- End Main Menu -->
