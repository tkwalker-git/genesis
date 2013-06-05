<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
    <title></title>
</head>

<body>
    <div class="eventManger_left">
        <ul>
            
            <li class="icon_myEvents"><a href="javascript:void(0)">SHOWCASE CREATOR</a>

                <ul>
                    <li><a style="font-size:12px;" href="<?php echo ABSOLUTE_PATH; ?>create_showcase.php?type=showcase" <?php if($_GET['p']=='create-showcase'){ echo "class='active'";} ?>>Create New Showcase</a></li> 
                    <li><a style="font-size:12px;" href="<?php echo ABSOLUTE_PATH;?>marketing_manager.php?p=showcase" <?php if($_GET['p']=='view-showcase' || $_GET['p'] == ''){ echo "class='active'";} ?>>View Showcases</a></li>                                       
                    <li><a style="font-size:12px;" href="<?php echo ABSOLUTE_PATH;?>fbapp/promote_step2.php" <?php if($_GET['p']=='promote'){ echo "class='active'";} ?>>Promote on Facebook</a></li>                                 
                </ul>
            </li>                       

            <li class="border"></li>

             <li class="icon_myEvents"><a href="javascript:void(0)">CLINIC EVENTS</a>
                
                <ul>
                    <li><a style="font-size:12px;" href="?p=clinic-events" <?php if($_GET['p']=='clinic-events'){ echo "class='active'";} ?>>Private Events</a></li>

                    <li><a style="font-size:12px;" href="?p=community-events" <?php if($_GET['p']=='community-events'){ echo "class='active'";} ?>>Public Events</a></li>
                </ul>
            </li>
            
        </ul>
    </div>
</body>
</html>
