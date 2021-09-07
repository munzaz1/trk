<!DOCTYPE html>
<html lang="cs" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="author" content="http://www.senon.cz" />
  <meta name="robots" content="index,follow" />

  <title><?=$tp['PAGE_TITLE']?></title>

  <link rel="SHORTCUT ICON" href="favicon.ico" />

  <link rel="stylesheet" href="<?=$tp['STYLE_PATH']?>style.css?build=<?=ClientBuildConstants::CLIENT_BUILD?>" type="text/css" />

  <link rel="stylesheet" href="js/baguetteBox.min.css">
  <script src="js/baguetteBox.min.js"></script>
  <script>
    window.onload = function() {
        baguetteBox.run('.photos', {
          // Custom options
        });
    };
  </script>
</head>
<body>
