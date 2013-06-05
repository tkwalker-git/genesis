<?php

//Type DB: mysql,access,mssql,postgre,oracle,sqlite,firebird

$config["databaseType"]="mysql";

//MySQL
$config["mysql"]["host"]="localhost";
$config["mysql"]["username"]="tkwalker";
$config["mysql"]["password"]="mepy2ama2";
$config["mysql"]["port"]="3306";
$config["mysql"]["dbname"]="zadmin_pangea";


//MSSQL

$config["mssql"]["host"]="localhost";

$config["mssql"]["username"]="";

$config["mssql"]["password"]="";

$config["mssql"]["dbname"]="";



//MS Access

$config["access"]["odbc_string"]="Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=c:\\database\\examples.mdb;Uid=;Pwd=";



//Oracle

$config["oracle"]["servername"]="localhost";

$config["oracle"]["username"]="";

$config["oracle"]["password"]="";



//Postgre

$config["postgre"]["host"]="localhost";

$config["postgre"]["username"]="";

$config["postgre"]["password"]="";

$config["postgre"]["options"]="";

$config["postgre"]["dbname"]="";



//SQLite

$config["sqlite"]["dbname"]="C:\\sqlite\\database_name.db";



//Firebird

$config["firebird"]["odbc_string"]="Driver={Firebird/InterBase(r) driver};Uid=login;Pwd=password; DbName=c:\\firebird\\darabase_name.fdb";

$config["firebird"]["uid"]="login";

$config["firebird"]["pwd"]="password";



//Locale



$config["locale"]=1033; //English (United States)



//$config["locale"]= 2057; //English (United Kingdom)

//$config["locale"]= 3081; //English (Australia

//$config["locale"]= 4105; //English (Canada)

//$config["locale"]= 1036; //French (France)

//$config["locale"]= 1031; //German (Germany)

//$config["locale"]= 1032; //Greek (Greece)

//$config["locale"]= 1040; //Italian (Italy)

//$config["locale"]= 1034; //Spanish (Spain, Traditional Sort)

//$config["locale"]= 1043; //Dutch (Netherlands)

//$config["locale"]= 1046; //Portuguese (Brazil)

//$config["locale"]= 2070; //Portuguese (Portugal)

//$config["locale"]= 2058; //Spanish (Mexico)



// if your locale is not listed here proceed to 

// http://reportsmaestro.com/locale_instructions.htm for detailed instructions



?>
