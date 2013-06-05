<?php

require_once("config.php");





$LeftMenu  = '<div><img src="images/website_sections.gif" /></div>' . "\n";

$LeftMenu .= '<ul id="LeftMenu">' . "\n";



for( $link=0; $link<count($pages); $link++ ) {



	$linkTitle 		= $pages[$link]['title'];

	$linkDirect 	= $pages[$link]['page'];

	$linkList   	= "list.php?link=$link";

	$linkIcon 		= $pages[$link]['icon'];

	$linkCurrent 	= ( (isset($_GET['link']) && $_GET['link'] == $link) || ( $linkDirect == basename($_SERVER['PHP_SELF']) ) )   ? 'class="selected"' : '';



	if( $pages[$link]['id'] ) {

		$ParentID  = $pages[$link]['id'];

		

		if( $pages[$link]['type'] == 'parent' ) {

			$LeftMenu .= '<li><a href="javascript:return false;" class="togSub" id="'.$ParentID.'"><img src="images/'.$linkIcon.'" align="absmiddle" border="0" /> '.$linkTitle.'</a>' . "\n";

			$LeftMenu .= childs( $pages, $ParentID, $link );

			$LeftMenu .= '</li>' . "\n";

		}

	}

	else

		$LeftMenu .= ( $pages[$link]['link'] == 'direct' ) ? '<li><a '.$linkCurrent.' href="'.$linkDirect.'"><img src="images/'.$linkIcon.'" align="absmiddle" border="0" /> '.$linkTitle.'</a></li>' : '<li><a '.$linkCurrent.' href="'.$linkList.'"><img src="images/'.$linkIcon.'" align="absmiddle" border="0" /> '.$linkTitle.'</a></li>' . "\n";

}

$LeftMenu .= '</ul>' . "\n";



echo $LeftMenu;





function childs( $pages, $parent, $link ) {

	$return = '<ul class="subMenu" id="sh'.$parent.'">';

	for( $i=0; $i<count($pages); $i++ ) {

		if( $pages[$i]['id'] == $parent && ( $pages[$i]['type'] != 'parent' ) ) {

			$Title 		= $pages[$i]['title'];

			$Direct 	= $pages[$i]['page'];

			$Current 	= ( (isset($_GET['link']) && $_GET['link'] == $i) || ( $Direct == basename($_SERVER['PHP_SELF']) ) )   ? 'class="selected"' : '';

			

			$return .= ( $pages[$i]['link'] == 'direct' ) ? '<li><a '.$Current.' href="'.$Direct.'" class="cds">&raquo; '.$Title.'</a></li>' . "\n" : '<li><a '.$Current.' href="list.php?link='.$i.'" class="cds">&raquo; '.$Title.'</a></li>' . "\n";

		}

	}

	return $return . '</ul>';

}





/** SINGLE MENU WITHOUT PARENT - CHILD RELATION

$menu  = '<div><img src="images/website_sections.gif" /></div>';

$menu .= '<ul id="LeftMenu">';

for ($i=0;$i<count($pages); $i++) {

		

	if ($pages[$i]['link'] != 'direct') {

		$LeftMenuClass = ( isset($_GET['link']) && $_GET['link'] == $i ) ? 'class="selected"' : '';

		$menu .= '<li><a '.$LeftMenuClass.' href="list.php?link='.$i.'">'.$pages[$i]['title'].'</a></li>';

	}

	else {

		$directLink = $pages[$i]['page'];

		$LeftMenuClass = ( $directLink == basename($_SERVER['PHP_SELF']) ) ? 'class="selected"' : '';

		$menu .= '<li><a '.$LeftMenuClass.' href="'.$directLink.'">'.$pages[$i]['title'].'</a></li>';

	}

}

echo $menu .= '</ul>';

**/



?>



<script type="text/javascript" src="js/jquery.cookies.js"></script>

<script type="text/javascript">

$(".subMenu").hide();

$(".togSub").click(function() {

	var id = $(this).attr('id');

	$("#sh" + id).slideToggle('slow');

})

$(".cds").click(function() {

	var pID = $(this).parents('ul').attr("id");

	$.cookie("ParOpen", pID);

});



if( $.cookie("ParOpen") ) {

	var p_id = $.cookie("ParOpen");

	$("#" + p_id).show();

	//$.cookie("ParOpen", null);

}



</script>