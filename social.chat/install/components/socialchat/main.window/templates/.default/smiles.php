<?php


$this->addExternalCss("/bitrix/components/socialchat/main.window/css/smiles.css");


$count = 40;

$path = "/chat/template/smiles/";
$server_path = $_SERVER['DOCUMENT_ROOT'].$path;

?>

<div id="smiles_block">
	<span id="open_smiles"><i class="em em-slightly_smiling_face"></i></span>
	<div id="smiles">
		<ul>
			<?php
				for ($i=0; $i < $count; $i++) {
					if(file_exists($server_path.$i.".png")){
						?>
						<li>
							<img src="<?php echo $path.$i.'.png'?>">
						</li>
						<?php
					}
				}
			?>
		</ul>
	</div>
	
</div>