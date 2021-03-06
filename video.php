<?php
require_once ('functions2.php');

$url = $_GET['url'];
$sourceCategory = $_GET['cat'];

$play = NULL;

if($url != ""){
	ensureDataLoaded();

	foreach ($data as $work) {
		if ($work['FileLocation'] == $url) {
			$play = $work;
			break;
		}
	}

	$previousVideo = NULL;
	$nextVideo = NULL;

	if($sourceCategory != ""){
		$categoryExploded = explode("/", $sourceCategory);
		$categoryName = $categoryExploded[sizeof($categoryExploded) - 1];
		$thumbnailCat = "images/section_icons/" . str_replace(" ", "_", str_replace("/", "!", $sourceCategory)) . ".png";
		if(!file_exists($thumbnailCat)){
			//get thumbnail for supercategory
			$thumbnailCat = str_replace(str_replace(" ", "_", str_replace("/", "!", "!" . $categoryName)), "", $thumbnailCat);
		}

		$fullCategory = getAllInCategory($sourceCategory);
		for($x = 0; $x < count($fullCategory); $x++){
			$work = $fullCategory[$x];
			if($work['FileLocation'] == $url){
				if($x != 0){
                    $previousVideo = $fullCategory[$x - 1];
                    $prevOffset = 2;
                    while ($previousVideo['FileLocation'] == "") {
                        $previousVideo = $fullCategory[$x - $prevOffset];
                        $prevOffset++;
                    }
                }
				if($x != (count($fullCategory) - 1)){
                    $nextVideo = $fullCategory[$x + 1];
                    $nextOffset = 2;
                    while ($nextVideo['FileLocation'] == "") {
                        $nextVideo = $fullCategory[$x + $nextOffset];
                        $nextOffset++;
                    }
                }
			}
		}
	}

}


?>

<!DOCTYPE html>
<!--[if IE 7 ]>    <html lang="en-gb" class="isie ie7 oldie no-js"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en-gb" class="isie ie8 oldie no-js"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en-gb" class="isie ie9 no-js"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="en-gb" class="no-js">
	<!--<![endif]-->
	<head>

		<?php
		if($play != null && $play['Title'] != "") echo "<title>" . $play['Title'] . " - Hear a Tale</title>";
		else echo "<title>Not Found - Hear a Tale</title>";
		include ($_SERVER['DOCUMENT_ROOT'] . '/globalHeader.php');
		?>

		<!-- flowplayer imports -->
		<link rel="stylesheet" type="text/css" href="//releases.flowplayer.org/5.4.6/skin/minimalist.css">
		<style>
			.flowplayer {
				width: 80%;
				background-color: #222;
				background-size: cover;
				max-width: 800px;
			}
			.flowplayer .fp-controls {
				background-color: rgba(235, 245, 255, 0.4)
			}
			.flowplayer .fp-timeline {
				background-color: rgba(187, 220, 252, 0.5)
			}
			.flowplayer .fp-progress {
				background-color: rgba(71, 166, 255, 1)
			}
			.flowplayer .fp-buffer {
				background-color: rgba(156, 207, 255, 1)
			}
			.flowplayer {
				background-color: rgba(222, 222, 222, 0)
			}
		</style>
		<script src="//releases.flowplayer.org/5.4.6/flowplayer.min.js"></script>
		<!-- end flowplayer imports -->
		<!-- flowplayer javascript customization -->
		<script>
			flowplayer(function(api, root) {

				api.bind("ready", function() {

					api.resume();

				});

				api.bind("finish", function() {
					<?php if($nextVideo != NULL){ ?>
					   window.location.href = "video.php?url=<?php echo $nextVideo['FileLocation'];?>&cat=<?php echo $sourceCategory;?>"
					<?php } ?>

				});
                
                api.bind("error", function (e, err) {
                    window.setInterval(function(){ location.reload(); }, 3000);
                });

			});
		</script>



	</head>

	<body>

		<?php
		include ($_SERVER['DOCUMENT_ROOT'] . '/globalBody.php');
		?>


<div class="span9" style="margin-left:5px; margin-right:5px;">
	<div style="clear: both;"></div>

	<?php
		if(preg_match('/(?i)msie [1]/',$_SERVER['HTTP_USER_AGENT'])) {
	?>

	<div style="margin-top:20px; margin-left:0; width:80%;" class="IE-warning">
	<b>
		The version of Internet Explorer you are using may not support Hear a Tale's video player.<br>
		Please consider using a more reliable web browser:<br>
		<a href="https://www.google.com/chrome/browser/">
			<img src="http://icons.iconarchive.com/icons/google/chrome/128/Google-Chrome-icon.png">
		</a>
		<a href="https://www.mozilla.org/en-US/firefox/new/">
			<img src="http://img2.wikia.nocookie.net/__cb20090607180304/gta/pl/images/4/49/Firefox_(logo).png">
		</a>

	</b>
	</div>

	<?php } ?>

	<?php	if($play != null){  ?>

		<fieldset>
			<legend>
				<?php
					if($sourceCategory != ""){
						echo "<a href='subcategory.php?cat=" . $sourceCategory . "'>";
						if(file_exists($thumbnailCat)) echo "<img src='" . $thumbnailCat . "' style='width:50px;'>";
						if($categoryName == "Children") $categoryName = "Children's Section";
						echo $categoryName . ": ";
						echo "</a>";
					}
					echo "<i>" . $play['Title'] . "</i>";
					if($play['Author'] != ""){
						echo ' by ' . $play['Author'] . '';
					}
				?>

			</legend>
		</fieldset>

		<?php if(substr($url, -4) === ".mp3"){ ?>
			<div style='width:300px; height:auto; background-color:#dddddd;'>

				<img style='width=auto; height:auto; display: table; margin:0 auto;' src='Thumbnails/<?php echo $play['ThumbnailImage']; ?>'>

			</div>
		<?php } ?>

		<div data-swf="//releases.flowplayer.org/5.4.6/flowplayer.swf"
		class="flowplayer fixed-controls no-toggle play-button color-light"
		data-ratio="0.5625" data-embed="false">
			<?php if(substr($url, -4) === ".mp3"){ ?>
			<audio controls preload="auto">
				<source type="audio/mp3" src="podcasting/<?php echo $url;	?>" >
			</audio>
			<?php } else { ?>
			<video preload="auto">
				<source type="video/mp4" src="podcasting/<?php echo $url;	?>"/>
			</video>
			<?php } ?>
		</div>

		<br><br><br>
		<table style="width:80%;">
			<tr align="center">
				<td>
				<?php
					if($previousVideo != NULL){
						echo "<a title='" . $previousVideo['Title'] . "' href='video.php?url=" . $previousVideo['FileLocation'] . "&cat=" . $sourceCategory . "'>
						<img src='images/section_icons/arrow_left.png'></a>";
					}
				?>
				</td>
				<td><a href='children.php'><img src='images/section_icons/Children.png'></a></td>
				<td><a href='category.php?cat=Children/Rhymes'><img src='images/section_icons/Children!Rhymes.png'></a></td>
				<td><a href='category.php?cat=Children/Stories'><img src='images/section_icons/Children!Stories.png'></a></td>
				<td><a href='category.php?cat=Children/Rhymes and Stories'><img src='images/section_icons/Children!Rhymes_and_Stories.png'></a></td>
				<td>
				<?php
					if($nextVideo != NULL){
						echo "<a title='" . $nextVideo['Title'] . "' href='video.php?url=" . $nextVideo['FileLocation'] . "&cat=" . $sourceCategory . "'>
						<img src='images/section_icons/arrow_right.png'></a>";
					}
				?>
				</td>
			<tr>
			<tr align="center" valign="top">
				<td><br>
				<?php
					if($previousVideo != NULL) echo "Previous Video";
				?>
				</td>
				<td>Children's Section</td>
				<td>Rhymes</td>
				<td>Stories</td>
				<td>Rhymes and Stories</td>
				<td><br>
				<?php
					if($nextVideo != NULL) echo "Next Video";
				?>
				</td>
			</tr>
		</table>



<br/>
<br/>
<br/>

<?php
	} else error404('video');
	include ($_SERVER['DOCUMENT_ROOT'] . '/globalFooter.php');
?>
