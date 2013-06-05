



<script src="<?php echo ABSOLUTE_PATH; ?>js/jquery-1.2.6.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/jquery.flip.min.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/script.js"></script>
<script type="text/javascript" src="<?php echo ABSOLUTE_PATH; ?>js/common_bc.js"></script>
<style>
.sponsor{
		width:520px;
}
.sponsorFlip{
		left:0;
		top:0;
		width:520px;
}
.sponsorData{
		display:none;
}

body{
	padding:0 !important;
	margin:0 !important;
	width:520px !important;
	min-width:520px !important;
	}
</style>

<link href="<?php echo ABSOLUTE_PATH; ?>style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="page-bg" class="translucent"></div>
<div class="subscribe-overlayer" id="overlayer" align="center"></div>
<div style="position:relative; width:520px;">
  <div class="sponsor" id="spons">
    <div class="sponsorFlip">
      <div class="flayerTopFB">
        <div class="flayerBottomFB">
          <div class="flayerMiddleFB">
            <div class="flayer" id="flayer" align="center">
			  <?php
			echo	getFlayerImage($event_image,'','','486');
			  ?>
            </div>
          </div>
        </div>
        <div class="clipImage" id="clickhere"><img src="<?php echo IMAGE_PATH; ?>click_here.png" alt="" title="Click to flip" /></div>
      </div>
    </div>
    <div class="sponsorData">
      <?php include("details.php"); ?>
    </div>
  </div>
</div>
</div>
</body>
</html>