<?php
/**
 *
 * @param string $title
 * @param array $javascripts
 * @param array $styles
 * @param JavascriptCssManager $javascript_css_manager
 */
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">

    <script src="/js/jquery-min.js"></script>
    <script src="/js/bootstrap/bootstrap.min.js"></script>
    <script src="/js/bootstrap/bootmetro.js"></script>
    <script src="/js/bootstrap/bootmetro-charms.js"></script>
    <script src="/js/core.js"></script>
    
    <link rel="stylesheet" href="/css/bootstrap.min.css" />
<!--     <link rel="stylesheet" href="/css/bootstrap-responsive.min.css" /> -->
    <link rel="stylesheet" href="/css/bootmetro.css" />
    <link rel="stylesheet" href="/css/bootmetro-tiles.css" />
    <link rel="stylesheet" href="/css/bootmetro-charms.css" />
    <link rel="stylesheet" href="/css/metro-ui-light.css" />
    <link rel="stylesheet" href="/css/icomoon.css" />
    <link rel="stylesheet" href="/css/tl-main.css" />
    <link rel="shortcut icon" href="/images/favicon.ico" />

    <?php $javascript_css_manager->outputAll();?>
    
	<title><?php echo $title;?></title>
</head>
<body>

<!--[if lt IE 7]>
<p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p>
<![endif]-->

<?php
$this->navbar->tryToDisplay();
?>
