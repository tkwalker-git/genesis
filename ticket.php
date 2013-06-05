<?php
include_once('admin/database.php'); 
include_once('site_functions.php');




include_once('includes/header.php');

?>

<div class="topContainer">
  <div class="welcomeBox"></div>
 
  <!--End Hadding -->
  <!-- Start Middle-->
  <div id="middleContainer">
    <div class="creatAnEventMdl" style="font-size:55px; text-align:center; width:100%"> Buy Tickets </div>
    <div class="clr"></div>
    <div class="gredBox">
      
    
        <div class="whiteTop">
          <div class="whiteBottom">
            <div class="whiteMiddle" style="padding-top:1px;">
             
			 
			 	<!--Start new code-->
				
				<div class="buyTicketp1">
				
					<div class="thumb-1">
						<button class="thumb_btn"></button>
						<img src="<?php echo IMAGE_PATH; ?>thumb-1.png" width="420" height="578" border="0" />
					
					</div> <!--end thumb-1-->
					
					<div class="thumb-1-detail">
						
						<span class="ew-heading">Royal Qrush - Omega Alumini Centennial</span><!--end ew-heading-->
						
						<div class="ew-heading-behind">
							
							<span>Celebration</span>
						
						</div> <!--end ew-heading-behind-->
						
						<span class="ew-heading-a" style="margin-top:11px; display: block;">Nov 19, 2011</span>
						
						<div class="ew-price-area">
							
							<span  class="ew-heading-a">Price:&nbsp;<span style="color:#ff4e1f;">$99.00</span><button></button></span>
							
						</div> <!--end ew-price-area-->
						
						
						<div class="ew-when-where">
							
							<span class="ew-when-heading">When</span>
							<span>
								Saturday, November 19th <br />
								10&nbsp;PM - 2&nbsp;AM
								
							</span>
							
						</div> <!--end ew-when-where-->
						
						<div class="ew-when-where">
							
							<span class="ew-when-heading">Where</span>
							<span>
								Heaven Event Center <br />
								(Off Sand Lake Road) <br />
								8240 Exchange Dr. <br />
								Orlando, FL 32809 <a href="#">[+] See Map</a>
								
							</span>
							
						</div> <!--end ew-when-where-->
						
						<div class="ew-when-where">
							
							<span class="ew-when-heading">Summary</span>
							<span>
								Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. <a href="#">[More]</a>
								
							</span>
							
						</div> <!--end ew-when-where-->
					
					</div> <!--end thumb-1-detail-->
					
					<br class="clear" />
				
				</div> <!--end buyTicketp1-->
				
				
				
					<div class="nav_new" style="margin-right:180px;">
						<ul>
							<li><a href="">Nearby Deals</a></li>
							<li><a href="">Location Info</a></li>
							<li class="active"><a href="">Event Detail</a></li>
							<li><a href="">Gallery</a></li>
							<li><a href="">Videos</a></li>
							<li><a href="">Special Events</a></li>
						</ul>
						
					</div><!--end nav_new-->
				
				<br class="clear" />
				
				
				<div class="blocker">
					<div class="blockerTop"></div> <!--end blockerTop-->
					<div class="blockerRepeat">
						
						<span class="ew-heading">Event Detail</span>
						
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
					
					</div> <!--end blockerRepeat-->
					<div class="blockerBottom"></div> <!--end blockerBottom-->
				</div> <!--end blocker-->
				
				
				<div class="blocker">
					<div class="blockerTop"></div> <!--end blockerTop-->
					<div class="blockerRepeat">
						
						<span class="ew-heading" style="font-size:18px;">Event Similar to Omega Alumni Centenniel Celebration <a href="">How did we determine these events ?</a></span>
						<span class="dottedSeparator"></span>
						
						<div class="ew-sugesstions">
						
						<span class="ew-left-control"></span>
						
						<span class="ew-right-control"></span>
							
							<?php 
								for($i=0 ;  $i <= 3; $i++){
							?>
							<div class="ew-suggetions-block">
							
								<div class="ew-suggetion-top"></div><!--end ew-suggetion-top-->
								
								<div class="ew-suggetion-center">
								
									<span class="ew-suggetion-tiny-heading">OACD - Crimson</span>
									
									<span class="ew-suggetion-date">Dec 10, 2011</span>
									
									<span class="ew-suggetion-separator"></span>
									
									<img src="<?php echo IMAGE_PATH; ?>suggetion-thumb.png" width="156" height="196" border="0" />
								
								</div><!--end ew-suggetion-center-->
								
								<div class="ew-suggetion-bottom"></div><!--end ew-suggetion-bottom-->
								
								<button class="suggetion-btn"></button>
								
								<ul class="ratingUL">
									<li><img src="<?php echo IMAGE_PATH; ?>on-star.png" width="17" height="16" border="0" /></li>
									<li><img src="<?php echo IMAGE_PATH; ?>on-star.png" width="17" height="16" border="0" /></li>
									<li><img src="<?php echo IMAGE_PATH; ?>on-star.png" width="17" height="16" border="0" /></li>
									<li><img src="<?php echo IMAGE_PATH; ?>off-star.png" width="17" height="16" border="0" /></li>
									<li><img src="<?php echo IMAGE_PATH; ?>off-star.png" width="17" height="16" border="0" /></li>
								</ul>
							
							</div><!--end ew-suggetions-block-->
							<?php }?>
						</div> <!--end ew-sugesstions-->
						
						<br class="clear" />
					
					</div> <!--end blockerRepeat-->
					<div class="blockerBottom"></div> <!--end blockerBottom-->
				</div> <!--end blocker-->
				
			 	<!--End new code-->
			 
			 
            </div>
          </div>
        </div>
        <div class="create_event_submited">
       
        </div>
      </form>

    </div>
  </div>
</div>
<?php include_once('includes/footer.php');?>
