<?include get_cfg_var("cartulary_conf").'/includes/env.php';?>
<?include "$confroot/$templates/php_page_init.php"?>
<?
  //Did they specify how many articles they wanted in the list?
  if( isset($_REQUEST['max']) ) {
    $max = $_REQUEST['max'];
  } else if ( isset($prefs['maxlist']) ){
    $max = $prefs['maxlist'];
  } else {
    $max = $default_max_list;
  }

  //Handle max all request
  if($max == 0) {
    $max = 9999;
  }

  //Any dates specified?
  if( isset($_REQUEST['dstart']) ) {
	$dstart = $_REQUEST['dstart'];
  } else {
	$dstart = date('m/d/Y', strtotime('-5 days', time()));
  }
  if( isset($_REQUEST['dend']) ) {
	$dend = $_REQUEST['dend'];
  } else {
	$dend = date('m/d/Y', time());
  }

  //Get feed locations
  if( s3_is_enabled($uid) || sys_s3_is_enabled() ) {
    $s3cartfeed = get_s3_url($uid, NULL, get_cartulary_feed_filename($uid));
    $s3cartopml = get_s3_url($uid, NULL, $default_opml_file_name);
  }

  $section = "Articles";
  $tree_location = "Articles";
?>

<?include "$confroot/$templates/$template_html_prehead"?>
<head>
<?include "$confroot/$templates/$template_html_meta"?>
<title><?echo $tree_location?></title>
<?include "$confroot/$templates/$template_html_styles"?>
<?include "$confroot/$templates/$template_html_scripts"?>
<script>
<?include "$confroot/$scripts/articles.js"?>
</script>
<style>#ddm<?echo $tree_location?> a.dropdown-toggle { color:white;font-weight:bold; }</style>
</head>
<?include "$confroot/$templates/$template_html_posthead"?>
<body>
<?//--- Include the logo and menu bar html fragments --?>
<?include "$confroot/$templates/$template_html_logotop"?>
<?include "$confroot/$templates/$template_html_menubar"?>

<div class="row page-header" id="divPageTitle">
        <h1><?echo $tree_location?>
            <a href="<?echo $s3cartfeed?>" label="RSS"><img class="icon-rss" alt="" src="/images/blank.gif" /></a><a id="aOpmlExport" href="<?echo $s3cartopml?>" label="OPML"><img class="icon-opml" alt="" src="/images/blank.gif" /></a><small><span id="message"></span></small>
        </h1>
</div>

<?//--- Stuff between the title and content --?>
<?include "$confroot/$templates/$template_html_precontent"?>

<div class="statline"><ul><li><a class="showall"><a href="?max=0"> Show all </a></li><?if($platform == "mobile") {?><li><a class="showdatepicker" href="#">Date Range</a></li><?}?></ul></div>
<div class="row" id="divArticleList">

        <div id="date-line">
        <form id="frmArticleGetRange" name="rangeget" method="GET" action="<?echo $_SERVER['PHP_SELF']?>">
	  <fieldset>
            <div class="divDateSelect">
            <div class="divStartDate">
              Start: <input id="start-date" name="dstart" class="txtDatePicker" type="text" disabled value="<?echo $dstart?>" />
            </div>
            <div class="divEndDate">
              End: <input id="end-date" name="dend" class="txtDatePicker" type="text" disabled value="<?echo $dend?>" />
            </div>
	    </div>
            <input id="btnSubmitDates" type="submit" class="btn btn-primary" name="submitdateget" value="Get" />
          </fieldset>
	</form>
        </div>

        <?
        //If a date range of articles was requested, we need to honor that
        $articles = get_articles_in_range($uid, NULL, NULL, $dstart, $dend);
        ?>

        <div id="divArticles">
        <?if ($articles > 0) {?>
	<form name="export" id="frmArticleExport" method="POST" action="<?echo $articleexportcgi?>">
        <?$acount = 1?>
        <?$lastdate = ''?>
	<?$today = date('D. F j, Y', time());?>
	<?foreach( $articles as $article ) {
		$thisdate = date('D. F j, Y', $article['linkedon']);
		if($thisdate == $today) {
			$thisdate = "Today, ".$thisdate;
		}
		if($thisdate != $lastdate) {
			if($lastdate != '') {
				?></ul><?
			}
			?><h3 class="article-section-header"><?echo $thisdate?></h3><?
			?><ul class="article-section-list"><?
		}
		?>

		<li id="tr<?echo $article['id']?>" data-artitle="<?echo $article['title']?>">
			<div class="divArticleLink">
			<?if($platform == "mobile") {?><a class="aDeleteArticle" href='<?echo $deletearticlecgi."?"."aid=".$article['id'];?>'><img class="icon-remove" src="/images/blank.gif" alt="" /></a><?}?>
                        <input class="exparticle" id="chkArticle<?echo $acount?>" type="checkbox" name="articles[]" value="<?echo $article['id']?>" /> <a href='<?echo $showarticlepage."?"."aid=".$article['id'];?>'><?echo $article['title']?></a>
			<?if($platform != "mobile") {?>
                          [
                           <a href="<?echo $article['url']?>"><img class="icon-extlink-small" src="/images/blank.gif" alt="" /></a>
                           <a class="aDeleteArticle" href='<?echo $deletearticlecgi."?"."aid=".$article['id'];?>'><img class="icon-remove-small" src="/images/blank.gif" alt="" /></a>
                          ]
			<?}?>
                        </div>
                </li>
		<?$acount++?>

		<?$lastdate = $thisdate;?>
        <?}?>
	<input id="btnSubmitArticles" type="submit" name="btnsub" style="display:none;" />
	</form>
        <?} else {?>
                <p>No articles yet. Use the <a href="<?echo sprintf($bookmarklet_url, $system_url)?>">Cartulize</a> bookmarklet save some.</p>
        <?}?>
        </div>
</div>

<?//--- Include the footer bar html fragments -----------?>
<?include "$confroot/$templates/$template_html_footerbar"?>
</body>

<?include "$confroot/$templates/$template_html_postbody"?>
</html>

