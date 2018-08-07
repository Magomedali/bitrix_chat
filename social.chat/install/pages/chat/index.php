<?php
require("core/header.php");

$APPLICATION->SetTitle("Chat");


?>
<?php

if($USER->IsAuthorized()) {
    

	$APPLICATION->IncludeComponent(
	"socialchat:main.window",
	"",
	Array()
	);

}else{
	require("login.php");
}
?>

<?php
require("core/footer.php");
?>