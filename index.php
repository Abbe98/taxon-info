<?php
require_once('classes/Taxon.php');

try {
  if (!empty($_GET['settings'])) {
      $settings = Taxon::loadSettings($_GET['settings']);
  } else {
    $settings = Taxon::loadSettings('default');
  }
  $taxon = new Taxon($_GET['wikidata'], $settings);
  $dataToRender = $taxon->getTaxon();
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $dataToRender->title; ?></title>
		<meta charset="UTF-8"/>
		<meta id="viewport" name="viewport" content ="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<link rel="stylesheet" type="text/css" href="assets/style.css" />
	</head>
	<body>
		<header>
			<img src="<?php echo $dataToRender->rawImage; ?>" />
			<h1><?php echo $dataToRender->title; ?></h1>
		</header>
		<main>
			<p><?php echo $settings->ui->read_more; ?>:
				<ul>
<?php
foreach ($dataToRender->topLanguages as $article) {
  echo '					<li><a href="' . $article->link . '">' . $article->title . ' (' . $article->language . ')</a></li>';
}
?>
				</ul>
			</p>
			<div>
				<a class="btn" href="https://commons.wikimedia.org/wiki/Category:<?php echo $dataToRender->commons; ?>">
					<?php echo $settings->ui->view_images; ?>
				</a>
				<a class="btn" href="https://commons.wikimedia.org/wiki/Special:UploadWizard?categories=<?php echo $dataToRender->commons; ?>">
					<?php echo $settings->ui->upload_image; ?>
				</a>
			</div>
		</main>
	</body>
</html>
<?php
} catch (Exception $e) {
  echo 'Server error ',  $e->getMessage(), '\n';
  die();
}
