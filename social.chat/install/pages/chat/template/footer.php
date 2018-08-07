<?require_once($_SERVER["DOCUMENT_ROOT"]."/settings.php"); // site settings?>
<?IncludeTemplateLangFile(__FILE__);?>

				
		  
    <script type="text/javascript">
      var ajaxPath = "<?=SITE_DIR?>ajax.php";
      var SITE_DIR = "<?=SITE_DIR?>";
      var SITE_ID  = "<?=SITE_ID?>";
      var TEMPLATE_PATH = "<?=SITE_TEMPLATE_PATH?>";
    </script>
	<script type="text/javascript" src="/chat/socket/js/chatState.js"></script>
    	<script type="text/javascript" src="/chat/socket/js/client.js"></script>
</body>
</html>