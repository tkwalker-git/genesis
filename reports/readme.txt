Installation instructions

1. Create a directory on the web server i.e. 'reports' and unzip all files there.
2. Before starting rename config-sample.php file to config.php.
3. Edit config.php file

   - $config["databaseType"] - database type (mysql,access,mssql,postgre,oracle).
   - connection parameters for selected database type
   - $config["locale"] - locale (by default: 1033, US English)

  Note: make sure that connection settings need to point to existing database 

4. Run install.php in browser and follow instructions
   http://yourwebsite.com/reports/install.php

5. To change visual style copy all files from selected style folder to 'include' folder.
Example: copy files from 'styles/Amsterdam' to 'include' folder.