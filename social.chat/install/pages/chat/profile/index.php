<?php
require("../core/header.php");

$APPLICATION->SetTitle("Profile");
?>
<?php

if($USER->IsAuthorized()) {
	$APPLICATION->IncludeComponent(
	"socialchat:chat.profile",
	"",
	Array()
	);

}else{
	require("../login.php");
}
?>

<?php
require("../core/footer.php");
?>