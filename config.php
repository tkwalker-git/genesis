<?php



@session_start();



$pages = array(



	array("title" => "Default Settings",

		"page" => "default_settings.php",

		"link" => "direct",

		"icon" => "ico_homes.png"



	),

	

	array("title" => "Categories",

		"id" => "",	

		"table" => "categories",

		//"filter" => "page_type=''",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "categories.php",

		"add_new_button" => "Add Categories",

		"default_orderby_column" => "id",

		"show" => array("Category name"=>"name"),

		"searchFieldsList" => array("Category Name"=>"name","Category id"=>"id"),

		"orderFieldsList" => array("ID"=>"id", "Category Name"=>"name"),

		"icon" => "article_cat.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "name",

	),

	

	array("title" => "Sub Categories",

		"id" => "",	

		"table" => "sub_categories",

		//"filter" => "page_type=''",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "sub_categories.php",

		"add_new_button" => "Add Sub Categories",

		"default_orderby_column" => "id",

		"show" => array("Sub Category name"=>"name","Parent Category"=>"categoryid"),

		"searchFieldsList" => array("Sub Category Name"=>"name"),

		"orderFieldsList" => array("ID"=>"id", "Sub Category Name"=>"name"),

		"icon" => "blog_cat.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "name",

	),

	

	array("title" => "Music",

		"id" => "",	

		"table" => "music",

		"customSQL" => "SELECT * FROM music",

		//"filter" => "page_type=''",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "music.php",

		"add_new_button" => "Add Music Name",

		"default_orderby_column" => "id",

		"show" => array("Music name"=>"name"),

		"searchFieldsList" => array("Music Name"=>"name"),

		"orderFieldsList" => array("ID"=>"id", "Music Name"=>"name"),

		"icon" => "music_icon.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "name",

	),

	

	

	array("title" => "Site Pages",

		"id" => "",	

		"table" => "site_pages",

	//	"customSQL" => "SELECT * FROM site_pages",

		//"filter" => "page_type=''",

		"addExtraBtn" => "no",

		"add_extra_button"	=> "Add Feature Page",

		"extrapage" => "site_pages.php?type=feature",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "site_pages.php",

		"add_new_button" => "Add Simple Page",

		"default_orderby_column" => "id",

		"show" => array("Page Title"=>"page_title", "Page Type"=>"page_type"),

		"searchFieldsList" => array("ID"=>"id", "Page Title"=>"page_title"),

		"orderFieldsList" => array("ID"=>"id", "Page Title"=>"page_title"),

		"icon" => "articles.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "page_title",

	),

	

	array("title" => "Blogs",

		"type" => "parent",

		"icon" => "blog.png",

		"id" => "blog"



	),

	

	array("title" => "All Blogs",

		"id" => "blog",

		"table" => "blog_posts",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "blog.php",

		"add_new_button" => "Add New Blog",

		"default_orderby_column" => "id",

		"customSQL" => "SELECT *,if( status = '0', 'Inactive', if( status = '2', 'Pending Approval', 'Active' ) ) AS sts FROM blog_posts ",

		"show" => array("Title"=>"title","Status"=>"sts"),

		"searchFieldsList" => array("title"=>"title"),

		"orderFieldsList" => array("ID"=>"id", "Title"=>"title"),

		"icon" => "blog.png"

	),

	

	array("title" => "Inactive Blogs",

		"id" => "blog",

		"table" => "blog_posts",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "blog.php",

		"add_new_button" => "Add New Blog",

		"default_orderby_column" => "id",

		"customSQL" => "SELECT *,if( status = '0', 'Inactive', if( status = '2', 'Pending Approval', 'Active' ) ) AS sts FROM blog_posts where status='0'",

		"show" => array("Title"=>"title","Status"=>"sts"),

		"searchFieldsList" => array("title"=>"title"),

		"orderFieldsList" => array("ID"=>"id", "Title"=>"title"),

		"icon" => "blog.png"

	),

	

	array("title" => "Pending Approval",

		"id" => "blog",

		"table" => "blog_posts",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "blog.php",

		"add_new_button" => "Add New Blog",

		"default_orderby_column" => "id",

		"customSQL" => "SELECT *,if( status = '0', 'Inactive', if( status = '2', 'Pending Approval', 'Active' ) ) AS sts FROM blog_posts where status='2'",

		"show" => array("Title"=>"title","Status"=>"sts"),

		"searchFieldsList" => array("title"=>"title"),

		"orderFieldsList" => array("ID"=>"id", "Title"=>"title"),

		"icon" => "blog.png"

	),

	

	

	array("title" => "Members",

		"id" => "",	

		"table" => "members",

		"customSQL" => "SELECT * FROM members",

		//"filter" => "page_type=''",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "members.php",

		"add_new_button" => "Add members",

		"default_orderby_column" => "id",

		"show" => array("Member Name"=>"name"),

		"searchFieldsList" => array( "Name"=>"event_name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name"),

		"icon" => "ico_team.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "name",

	),

	

	array("title" => "Orders",

		"type" => "parent",

		"icon" => "ico_order.png",

		"id" => "orders"

	),

	

	array("title" => "Products Orders",

		"id" => "orders",	

		"table" => "orders",

		//"filter" => "page_type=''",

		"add" => "no",

		"delete"=>"yes",

		"page" => "order.php",

	//	"add_new_button" => "Add Ticket",

		"default_orderby_column" => "id",

		"customSQL"=>"select *,(select username from members where id=orders.user_id) as sname from orders where type='product'",

		"show" => array("User Name"=>"sname","Date"=>"date","Price ($)"=>"total_price"),

		"searchFieldsList" => array( "Ticket Name"=>"name", "Description" => "ticket_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_order.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Titcket name",

		"bulk_delete" => "yes"

	),

	

array("title" => "Tickets Orders",

		"id" => "orders",	

		"table" => "orders",

		//"filter" => "page_type=''",

		"add" => "no",

		"delete"=>"yes",

		"page" => "orderTickets.php",

	//	"add_new_button" => "Add Ticket",

		"default_orderby_column" => "id",

		"customSQL"=>"select *,(select username from members where id=orders.user_id) as sname from orders where type='ticket'",

		"show" => array("User Name"=>"sname","Date"=>"date","Price ($)"=>"total_price"),

		"searchFieldsList" => array( "Ticket Name"=>"name", "Description" => "ticket_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_order.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Titcket name",

		"bulk_delete" => "yes"

	),

	

	

	array("title" => "Sponsored Brands",

		"id" => "",	

		"table" => "sponsor",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "sponsor.php",

		"add_new_button" => "Add Sponsor",

		"default_orderby_column" => "id",

		"show" => array("Member Name"=>"name"),

		"searchFieldsList" => array( "Name"=>"event_name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name"),

		"icon" => "icon_sponsors.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "name",

	),

	

	array("title" => "Market Place",

		"type" => "parent",

		"icon" => "market.png",

		"id" => "market"

	),

	

	array("title" => "Category",

		"id" => "market",	

		"table" => "market_category",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "market_category.php",

		"add_new_button" => "Add Category",

		"default_orderby_column" => "id",

		"show" => array("Category Name"=>"name"),

		"searchFieldsList" => array( "Name"=>"name"),

	//	"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Category Name",

		"bulk_delete" => "yes"

	),

	

	

	array("title" => "Products",

		"id" => "market",	

		"table" => "products",

		"customSQL"=>"select *,(select name from market_category where id = products.category_id) as gc  from products",

		"show" => array("Product Name"=>"name","Category Name"=>"gc"),

		"add" => "yes",

		"delete"=>"yes",

		"page" => "products.php",

		"add_new_button" => "Add Product",

		"default_orderby_column" => "id",

		"show" => array("Product Name"=>"name","Category Name"=>"gc","Sale Price"=>"sale_price"),

		"searchFieldsList" => array( "Name"=>"name"),

	//	"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Category Name",

		"bulk_delete" => "yes"

	),

	

	/*array("title" => "Age",

		"id" => "age",	

		"table" => "age",

		"delete"=>"yes",

		"add" => "yes",

		"page" => "age.php",

		"add_new_button" => "Add New Age",

		"default_orderby_column" => "id",

		"show" => array( "Age"=>"name"),

		"searchFieldsList" => array( "Age"=>"name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id"),

		"icon" => "ico_homes.png", 

		"bulk_delete" => "yes"*/

		

		

	array("title" => "Age",

		"id" => "",	

		"table" => "age",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "age.php",

		"add_new_button" => "Add New Age",

		"default_orderby_column" => "id",

		"show" => array( "Age"=>"name"),

		"searchFieldsList" => array( "Age"=>"name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id"),

		"icon" => "ico_homes.png", 

		"bulk_delete" => "yes"

	),

	

	array("title" => "Events",

 		"type" => "parent",

		"icon" => "event_icon.png",

		"id" => "eventstab"

	),

	

	array("title" => "All Events",

		"id" => "eventstab",	

		"table" => "events",

		"customSQL" => "SELECT *,(select name from categories where id=events.category_id) as category, (select name from sub_categories where id=events.subcategory_id) as scategory,if( event_status = '0', 'Inactive', if( event_status = '2', 'Pending Approval', 'Active' ) ) AS sts,if( event_type = '0', 'Simple', if( event_type = '1', 'Flyer', 'Flyer' ) ) AS event_type FROM events where 1=1",

		//"filter" => "page_type=''",

		"delete"=>"yes",

		"add" => "yes",

		"addExtraBtn" => "yes",

		"add_extra_button"	=> "Add Digital Flyer",

		"extrapage" => "events.php?type=flyer",

		"page" => "events.php",

		"add_new_button" => "Add Simple Event",

		"default_orderby_column" => "id",

		"show" => array("Event Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory","Status"=>"sts","Type"=>"event_type"),

		"searchFieldsList" => array( "Name"=>"event_name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Event name",

		"bulk_delete" => "yes"

	),

	

	array("title" => "Admin Events",

		"id" => "eventstab",	

		"table" => "events",

		"customSQL" => "SELECT *,(select name from categories where id=events.category_id) as category, (select name from sub_categories where id=events.subcategory_id) as scategory,if( event_status = '0', 'Inactive', if( event_status = '2', 'Pending Approval', 'Active' ) ) AS sts,if( event_type = '0', 'Simple', if( event_type = '1', 'Flyer', 'Flyer' ) ) AS event_type FROM events where event_source='Admin'",

		//"filter" => "page_type=''",

		"delete"=>"yes",

		"add" => "yes",

		"addExtraBtn" => "yes",

		"add_extra_button"	=> "Add Digital Flyer",

		"extrapage" => "events.php?type=flyer",

		"page" => "events.php",

		"add_new_button" => "Add Simple Event",

		"default_orderby_column" => "id",

		"show" => array("Event Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory","Status"=>"sts","Type"=>"event_type"),

		"searchFieldsList" => array( "Name"=>"event_name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Event name",

		"bulk_delete" => "yes"

	),

	array("title" => "EvevntFull Events",

		"id" => "eventstab",	

		"table" => "events",

		"customSQL" => "SELECT *,(select name from categories where id=events.category_id) as category, (select name from sub_categories where id=events.subcategory_id) as scategory,if( event_status = '0', 'Inactive', if( event_status = '2', 'Pending Approval', 'Active' ) ) AS sts FROM events where event_source='EventFull'",

		//"filter" => "page_type=''",

		"add" => "yes",

		"addExtraBtn" => "yes",

		"page" => "events.php",

		"extrapage" => "events.php?type=flyer",

		"add_new_button" => "Add Simple Event",

		"add_extra_button"	=> "Add Digital Flyer",

		"delete"=>"yes",

		"default_orderby_column" => "id",

		"show" => array("Event Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory","Status"=>"sts"),

		"searchFieldsList" => array( "Name"=>"event_name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_homes.png", 

		"bulk_delete" => "yes"

		//"add_sort_button" => "sorting.php",

       // "add_sort_Title" => "Event name",

	),

	array("title" => "Meetup Events",

		"id" => "eventstab",	

		"table" => "events",

		"customSQL" => "SELECT *,(select name from categories where id=events.category_id) as category, (select name from sub_categories where id=events.subcategory_id) as scategory,if( event_status = '0', 'Inactive', if( event_status = '2', 'Pending Approval', 'Active' ) ) AS sts FROM events where event_source='Meetup'",

		//"filter" => "page_type=''",

		"delete"=>"yes",

		"add" => "yes",

		"addExtraBtn" => "yes",

		"page" => "events.php",

		"extrapage" => "events.php?type=flyer",

		"add_new_button" => "Add Simple Event",

		"add_extra_button"	=> "Add Digital Flyer",

		"default_orderby_column" => "id",

		"show" => array("Event Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory","Status"=>"sts"),

		"searchFieldsList" => array( "Name"=>"event_name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Event name",

		"bulk_delete" => "yes"

	),

	array("title" => "FaceBook Events",

		"id" => "eventstab",	

		"table" => "events",

		"customSQL" => "SELECT *,(select name from categories where id=events.category_id) as category, (select name from sub_categories where id=events.subcategory_id) as scategory,if( event_status = '0', 'Inactive', if( event_status = '2', 'Pending Approval', 'Active' ) ) AS sts FROM events where event_source='FaceBook'",

		//"filter" => "page_type=''",		

		"delete"=>"yes",

		"add" => "yes",

		"addExtraBtn" => "yes",

		"page" => "events.php",

		"extrapage" => "events.php?type=flyer",

		"add_new_button" => "Add Simple Event",

		"add_extra_button"	=> "Add Digital Flyer",

		"default_orderby_column" => "id",

		"show" => array("Event Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory","Status"=>"sts","Added By"=>"added_by"),

		"searchFieldsList" => array( "Name"=>"event_name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Event name",

		"bulk_delete" => "yes"

	),

	

	array("title" => "Events without Images",

		"id" => "eventstab",	

		"table" => "events",

		"customSQL" => "SELECT *,(select name from categories where id=events.category_id) as category, (select name from sub_categories where id=events.subcategory_id) as scategory,if( event_status = '0', 'Inactive', if( event_status = '2', 'Pending Approval', 'Active' ) ) AS sts FROM events where event_image=''",

		//"filter" => "page_type=''",

		"delete"=>"yes",

		"add" => "yes",

		"addExtraBtn" => "yes",

		"page" => "events.php",

		"extrapage" => "events.php?type=flyer",

		"add_new_button" => "Add Simple Event",

		"add_extra_button"	=> "Add Digital Flyer",

		"default_orderby_column" => "id",

		"show" => array("Event Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory","Status"=>"sts"),

		"searchFieldsList" => array( "Name"=>"event_name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Event name",

		"bulk_delete" => "yes"

	),

	

	array("title" => "Promoter Events",

		"id" => "eventstab",	

		"table" => "events",

		"customSQL" => "SELECT *,(select name from categories where id=events.category_id) as category, (select name from sub_categories where id=events.subcategory_id) as scategory,if( event_status = '0', 'Inactive', if( event_status = '2', 'Pending Approval', 'Active' ) ) AS sts FROM events where event_source='Promoter'",

		//"filter" => "page_type=''",

		"delete"=>"yes",

		"add" => "yes",

		"addExtraBtn" => "yes",

		"page" => "events.php",

		"extrapage" => "events.php?type=flyer",

		"add_new_button" => "Add Simple Event",

		"add_extra_button"	=> "Add Digital Flyer",

		"default_orderby_column" => "id",

		"show" => array("Event Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory","Status"=>"sts"),

		"searchFieldsList" => array( "Name"=>"event_name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Event name",

		"bulk_delete" => "yes"

	),

	

	array("title" => "Member Events",

		"id" => "eventstab",	

		"table" => "events",

		"customSQL" => "SELECT *,(select name from categories where id=events.category_id) as category, (select name from sub_categories where id=events.subcategory_id) as scategory,if( event_status = '0', 'Inactive', if( event_status = '2', 'Pending Approval', 'Active' ) ) AS sts,if( event_type = '0', 'Simple', if( event_type = '1', 'Flyer', 'Flyer' ) ) AS event_type FROM events where event_source='User'",

		//"filter" => "page_type=''",

		"delete"=>"yes",

		"add" => "yes",

		"addExtraBtn" => "yes",

		"page" => "events.php",

		"extrapage" => "events.php?type=flyer",

		"add_new_button" => "Add Simple Event",

		"add_extra_button"	=> "Add Digital Flyer",

		"default_orderby_column" => "id",

		"show" => array("Event Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory","Status"=>"sts","Type"=>"event_type"),

		"searchFieldsList" => array( "Name"=>"event_name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Event name",

		"bulk_delete" => "yes"

	),

	

	array("title" => "Event Tickets",

		"id" => "eventstab",	

		"table" => "event_ticket",

		//"filter" => "page_type=''",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "tickets.php",

		"add_new_button" => "Add Ticket",

		"default_orderby_column" => "id",

		"show" => array("Titcket Name"=>"name"),

		"searchFieldsList" => array( "Ticket Name"=>"name", "Description" => "ticket_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Titcket name",

		"bulk_delete" => "yes"

	),

	

	array("title" => "Pending Approval",

		"id" => "eventstab",	

		"table" => "events",

		"customSQL" => "SELECT *,(select name from categories where id=events.category_id) as category, (select name from sub_categories where id=events.subcategory_id) as scategory,'Pending Approval' AS sts FROM events where event_status='2'",

		//"filter" => "page_type=''",

		"delete"=>"yes",

		"add" => "yes",

		"addExtraBtn" => "yes",

		"page" => "events.php",

		"extrapage" => "events.php?type=flyer",

		"add_new_button" => "Add Simple Event",

		"add_extra_button"	=> "Add Digital Flyer",

		"default_orderby_column" => "id",

		"show" => array("Event Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory","Status"=>"sts"),

		"searchFieldsList" => array( "Name"=>"event_name", "Description" => "event_description"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Event name",

		"bulk_delete" => "yes"

	),

		

	array("title" => "Annual Event",

		"id" => "",	

		"table" => "specials",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "specials.php",

		"add_new_button" => "Add Special",

		"default_orderby_column" => "id",

		"show" => array("Name"=>"name"),

		"searchFieldsList" => array("ID"=>"id", "Name"=>"name"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"name"),

		"icon" => "event_icon.png", 

        "add_sort_Title" => "page_title",

	),

	array("title" => "Venues",

 		"type" => "parent",

		"icon" => "venues_icon.png",

		"id" => "venuestab"

	),

	array("title" => "All Venues",

		"id" => "venuestab",	

		"table" => "venues",

		"customSQL" => "SELECT * FROM venues where 1=1",

		//"filter" => "page_type=''",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "venues.php",

		"add_new_button" => "Add Venue",

		"default_orderby_column" => "id",

		"show" => array("Name"=>"venue_name", "Address"=>"venue_address", "City"=>"venue_city", "State"=>"venue_state", "Zip"=>"venue_zip"),

		"searchFieldsList" => array("Name"=>"venue_name", "Address"=>"venue_address", "City"=>"venue_city", "State"=>"venue_state", "Zip"=>"venue_zip"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"venue_name", "Address"=>"venue_address", "City"=>"venue_city", "State"=>"venue_state", "Zip"=>"venue_zip"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Event name",

	),	

	array("title" => "City Grid Venues",

		"id" => "venuestab",	

		"table" => "venues",

		"customSQL" => "SELECT * FROM venues where source_id like 'CG-%'",

		//"filter" => "page_type=''",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "venues.php",

		"add_new_button" => "Add Venue",

		"default_orderby_column" => "id",

		"show" => array("Name"=>"venue_name", "Address"=>"venue_address", "City"=>"venue_city", "State"=>"venue_state", "Zip"=>"venue_zip"),

		"searchFieldsList" => array("Name"=>"venue_name", "Address"=>"venue_address", "City"=>"venue_city", "State"=>"venue_state", "Zip"=>"venue_zip"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"venue_name", "Address"=>"venue_address", "City"=>"venue_city", "State"=>"venue_state", "Zip"=>"venue_zip"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Event name",

	),	

	array("title" => "MeetUp Venues",

		"id" => "venuestab",	

		"table" => "venues",

		"customSQL" => "SELECT * FROM venues where source_id like 'MP-%'",

		//"filter" => "page_type=''",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "venues.php",

		"add_new_button" => "Add Venue",

		"default_orderby_column" => "id",

		"show" => array("Name"=>"venue_name", "Address"=>"venue_address", "City"=>"venue_city", "State"=>"venue_state", "Zip"=>"venue_zip"),

		"searchFieldsList" => array("Name"=>"venue_name", "Address"=>"venue_address", "City"=>"venue_city", "State"=>"venue_state", "Zip"=>"venue_zip"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"venue_name", "Address"=>"venue_address", "City"=>"venue_city", "State"=>"venue_state", "Zip"=>"venue_zip"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Event name",

	),	

	array("title" => "EventFull Venues",

		"id" => "venuestab",	

		"table" => "venues",

		"customSQL" => "SELECT * FROM venues where source_id like 'EF-%'",

		//"filter" => "page_type=''",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "venues.php",

		"add_new_button" => "Add Venue",

		"default_orderby_column" => "id",

		"show" => array("Name"=>"venue_name", "Address"=>"venue_address", "City"=>"venue_city", "State"=>"venue_state", "Zip"=>"venue_zip"),

		"searchFieldsList" => array("Name"=>"venue_name", "Address"=>"venue_address", "City"=>"venue_city", "State"=>"venue_state", "Zip"=>"venue_zip"),

		"orderFieldsList" => array("ID"=>"id", "Name"=>"venue_name", "Address"=>"venue_address", "City"=>"venue_city", "State"=>"venue_state", "Zip"=>"venue_zip"),

		"icon" => "ico_homes.png", 

		//"add_sort_button" => "sorting.php",

        "add_sort_Title" => "Event name",

	),	

	

	array("title" => "Facebook Groups",

		"id" => "",	

		"table" => "fb_groups",

		"customSQL" => "SELECT * FROM fb_groups",

		//"filter" => "page_type=''",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "fb_groups.php",

		"add_new_button" => "Add Group",

		"default_orderby_column" => "id",

		"show" => array("Group Id"=>"gid", "Group Name"=>"gname"),

		"searchFieldsList" => array("ID"=>"id", "Group Name"=>"gname", "Tags" => "tags"),

		"orderFieldsList" => array("ID"=>"id",  "Group Name"=>"gname"),

		//"add_sort_button" => "sorting.php",

       		 //"add_sort_Title" => "name",

		"icon" => "social.jpeg"

),

	

	

	array("title" => "Ad Slots",

		"type" => "parent",

		"icon" => "blog.png",

		"id" => "slots"



	),

	

	array("title" => "Slots",

		"id" => "slots",

		"table" => "slots",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "slots.php",

		"add_new_button" => "Add New Slot",

		"default_orderby_column" => "id",

		"show" => array("Slot ID"=>"id","Price"=>"price"),

		"searchFieldsList" => array("ID"=>"id"),

		"orderFieldsList" => array("ID"=>"id", "Price"=>"price"),

		"icon" => "blog.png"

	),

	/*

	array("title" => "Booked Slots",

		"id" => "slots",

		"table" => "sold_slots",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "sold-slots.php",

		"add_new_button" => "Add New Slot",

		"default_orderby_column" => "id",

		"show" => array("Slot ID"=>"id","Price"=>"price"),

		"searchFieldsList" => array("ID"=>"id"),

		"orderFieldsList" => array("ID"=>"id", "Price"=>"price"),

		"icon" => "blog.png"

	),

	*/

	

	array("title" => "Data Collection",

 		"type" => "parent",

		"icon" => "ico_home.png",

		"id" => "apis"

	),

	

	array("title" => "City Grid",

 		"id" => "apis",

		"page" => "citygrid.php",

		"link" => "direct",

		//"icon" => "ico_homes.png" 

	),

	array("title" => "Yelp Venues",

 		"id" => "apis",

		"page" => "yelp.php",

		"link" => "direct",

		//"icon" => "ico_homes.png" 

	),

	array("title" => "EventFull",

 		"id" => "apis",

		"page" => "eventfull.php",

		"link" => "direct",

		//"icon" => "ico_homes.png" 

	),

	array("title" => "EventFull Tags",

 		"id" => "apis",

		"page" => "eventfull_tags.php",

		"link" => "direct",

		//"icon" => "ico_homes.png" 

	),

	array("title" => "Meet Up",

 		"id" => "apis",

		"page" => "meetup.php",

		"link" => "direct",

		//"icon" => "ico_homes.png" 

	),

	array("title" => "Facebook (Groups)",

 		"id" => "apis",

		"page" => "get_fb_events.php",

		"link" => "direct",

		//"icon" => "ico_homes.png" 

	),

	array("title" => "Facebook (Import File)",

 		"id" => "apis",

		"page" => "get_fb_events_new.php",

		"link" => "direct",

		//"icon" => "ico_homes.png" 

	),

	

	array("title" => "Categorization( Tag Matching)",

		"link" => "direct",

		"page" => "categorization.php",

		"icon" => "ico_homes.png", 

	),

	/*

	array("title" => "Refresh Master Venue List",

		"link" => "direct",

		"page" => "master_venue_list.php",

		"icon" => "ico_homes.png", 

	),

	*/

	array("title" => "Cleanup Expired Events",

		"link" => "direct",

		"page" => "database_cleanup.php",

		"icon" => "ico_homes.png", 

	),

	

	 array("title" => "Coupon Codes",



		"table" => "coupons",

		"add" => "yes",

		"delete"=>"yes",

		"page" => "coupons.php",

		"add_new_button" => "Add New Coupon",

		"default_orderby_column" => "id",

		"show" => array("Code"=>"code"),

		"searchFieldsList" => array("code"=>"code"),

		"orderFieldsList" => array("ID"=>"id", "code"=>"code"),

		"icon" => "ico_pages.png"



	),

	

	array("title" => "Authorize.net",

		"page" => "marchent_settings.php",

		"link" => "direct",

		"icon" => "ico_home.png"



	),

		

	array("title" => "Change Password",

		"page" => "changePass.php",

		"link" => "direct",

		"icon" => "ico_homes.png"



	),

	

	

);









?>