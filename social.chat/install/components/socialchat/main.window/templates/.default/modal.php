<?php


$this->addExternalCss("/bitrix/components/socialchat/main.window/css/modal.css");
?>
<div id="modal-window">
	<div id="modal-blank">
		<div id="modal-close-cover"></div>
		<div id="modal-window-container">
			<div id="modal">
				<div id="modal-close-btn">X</div>
				<div id="modal-content"></div>
			</div>
		</div>
		<script type="text/javascript">
			$("body").on("click","#modal-close-cover,#modal-close-btn,.modal-close-btn",function(){
				$("#modal-window").hide();
			})
		</script>
	</div>
</div>