<?php

@session_start();

$pages = array(

	array("title" => "Default Settings",
		"page" => "default_settings.php",
		"link" => "direct",
		"icon" => "ico_homes.png"

	),
	

	
	array("title" => "Learning Library",
		"id" => "",	
		"table" => "learning_library",
		//"filter" => "page_type=''",
		"add" => "yes",
		"delete"=>"yes",
		"page" => "learning_library.php",
		"add_new_button" => "Add Video",
		"default_orderby_column" => "id",
		"show" => array("Video name"=>"title"),
		"searchFieldsList" => array("Video Name"=>"title","Video id"=>"id"),
		"orderFieldsList" => array("ID"=>"id", "Video Name"=>"title"),
		"icon" => "article_cat.png", 
		//"add_sort_button" => "sorting.php",
        "add_sort_Title" => "title"
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
        "add_sort_Title" => "name"
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
        "add_sort_Title" => "name"
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
        "add_sort_Title" => "page_title"
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
	
	
	
	
	array("title" => "Events",
 		"type" => "parent",
		"icon" => "event_icon.png",
		"id" => "eventstab"
	),
	
	array("title" => "All Events",
		"id" => "eventstab",	
		"table" => "events",

	//	"customSQL" => "SELECT *,(select name from categories where id=events.category_id) as category, (select name from sub_categories where id=events.subcategory_id) as scategory,if( event_status = '0', 'Inactive', if( event_status = '2', 'Pending Approval', 'Active' ) ) AS sts,if( event_type = '0', 'Simple', if( event_type = '2', 'Premium', 'Flyer' ) ) AS event_type FROM events where 1=1",
		
		"customSQL" => "SELECT *,(select name from categories where id=events.category_id) as category, (select name from sub_categories where id=events.subcategory_id) as scategory,

		if( event_status = '0', 'Inactive', if( event_status = '2', 'Pending Approval', 'Active' ) ) AS sts,

		if( is_private = '1', 'Active', if( event_status = '1', 'Active', 'Inactive' ) ) AS sts,

		if( event_type = '0', 'Simple', if( event_type = '2', 'Premium', 'Flyer' ) ) AS event_type

		FROM events where 1=1",

		//"filter" => "page_type=''",
		"delete"=>"yes",
		"add" => "yes",
		"add_new_button" => "Add Event",
		"addExtraBtn" => "no",
		"addExtraBtn2" => "no",
		"addExtraBtn3" => "no",
		"add_extra_button"	=> "Add Digital Flyer",
		"add_extra_button2"	=> "Add Premium Event",
		"add_extra_button3"	=> "Showcase Creator",
		"extrapage" => "events.php?type=flyer",
		"extrapage3" => "events.php?type=showcase",
		"extrapage2" => "events.php?type=premium",		
		"page" => "events.php",
		"default_orderby_column" => "id",
		"download"=>"../load_xls.php",
		"show" => array("Event Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory","Status"=>"sts","Type"=>"event_type"),
		"searchFieldsList" => array( "Name"=>"event_name", "Description" => "event_description"),
		"orderFieldsList" => array("ID"=>"id", "Name"=>"event_name","Category"=>"category","Sub Category"=>"scategory"),
		"icon" => "ico_homes.png", 
		//"add_sort_button" => "sorting.php",
        "add_sort_Title" => "Event name",
		"bulk_delete" => "yes"
	),
	
	
	
	array("title" => "Clinics",
		"id" => "",	
		"table" => "clinic",
		//"filter" => "page_type=''",
		"add" => "yes",
		"delete"=>"yes",
		"page" => "clinics.php",
		"add_new_button" => "Add Clinic",
		"default_orderby_column" => "id",
		"show" => array("Clinic Name"=>"clinicname","Address"=>"address1","City"=>"city","Zip"=>"zip"),
		"searchFieldsList" => array("Clinic Name"=>"clinicname","Clinic id"=>"id"),
		"orderFieldsList" => array("ID"=>"id", "Clinic Name"=>"clinicname"),
		"icon" => "article_cat.png", 
		//"add_sort_button" => "sorting.php",
        "add_sort_Title" => "clinicname"
	),
	
	array("title" => "Users",
		"id" => "",	
		"table" => "users",
		//"filter" => "page_type=''",
		"add" => "yes",
		"delete"=>"yes",
		"page" => "users.php",
		"add_new_button" => "Add user",
		"default_orderby_column" => "id",
		"show" => array("First Name"=>"firstname","Last Name"=>"lastname","Email"=>"email","City"=>"city","Zip"=>"zip"),
		"searchFieldsList" => array("First Name"=>"firstname","User id"=>"id"),
		"orderFieldsList" => array("ID"=>"id", "First Name"=>"firstname"),
		"icon" => "article_cat.png", 
		//"add_sort_button" => "sorting.php",
        "add_sort_Title" => "firstname"
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
	
		
	array("title" => "Change Password",
		"page" => "changePass.php",
		"link" => "direct",
		"icon" => "ico_homes.png"

	),
	
	
);




?>