<?php
	global $emotionListDefault;
	$emotionList = apply_filters("argon_emotion_list", $emotionListDefault);
?>
<div id="emotion_keyboard" class="emotion-keyboard card shadow-sm bg-white">
	<div class="emotion-keyboard-content">
		<?php
			foreach ($emotionList as $groupIndex => $group){ 
				$className = "emotion-group";
				if ($groupIndex != 0){
					$className .= " d-none";
				}
				echo "<div class='" . $className . "' index='" . $groupIndex . "'>";
				foreach ($group['list'] as $index => $emotion){
					if (isset($emotion['title'])){
						$title = $emotion['title'];
					}else{
						$title = "";
					}
					if ($emotion['type'] == 'text'){
						echo "<div class='emotion-item' title='" . $title . "'>" . $emotion['text'] . "</div>";
					}
					if ($emotion['type'] == 'sticker'){
						echo "<div class='emotion-item emotion-item-sticker' code='" . $emotion['code'] . "' title='" . $title . "'><img class='lazyload' src='data:image/svg+xml;base64,PHN2ZyBjbGFzcz0iZW1vdGlvbi1sb2FkaW5nIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9Im5vbmUiIHZpZXdCb3g9Ii04IC04IDQwIDQwIiBzdHJva2U9IiM4ODgiIG9wYWNpdHk9Ii41IiB3aWR0aD0iNjAiIGhlaWdodD0iNjAiPgogIDxwYXRoIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIgc3Ryb2tlLXdpZHRoPSIxLjUiIGQ9Ik0xNC44MjggMTQuODI4YTQgNCAwIDAxLTUuNjU2IDBNOSAxMGguMDFNMTUgMTBoLjAxTTIxIDEyYTkgOSAwIDExLTE4IDAgOSA5IDAgMDExOCAweiIvPgo8L3N2Zz4=' data-original='" . $emotion['src'] . "'/></div>";
					}
				}
				if (isset($group['description'])){
					echo "<div class='emotion-group-description'>" . $group['description'] . "</div>";
				}
				echo "</div>"; 
			}
		?>
	</div>
	<div class="emotion-keyboard-bar">
		<?php
			foreach ($emotionList as $groupIndex => $group){
				$className = "emotion-group-name";
				if ($groupIndex == 0){
					$className .= " active";
				}
				echo "<div class='" . $className . "' index='" . $groupIndex . "'>" . $group['groupname'] . "</div>"; 
			}
		?>
	</div>
</div>