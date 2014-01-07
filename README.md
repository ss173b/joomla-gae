joomla-gae
==========

GAE configuration and installation information for installation, deployment, and maintenance of the Joomla! CMS on the Google App Engine Platform


MY own personal website


==========
Original notes on setting up GAE with a number of false starts/bad paths

Setup Joomla site on Google Apps
Wordpress notes: https://developers.google.com/appengine/articles/wordpress


Prereq: Install and configure PHPStorm to your liking
Setup a Github account
Fork the CMS code: https://github.com/joomla/joomla-cms
Install and configure App Engine SDK[along with PHP and Mysql]

After that, beginning with Google Cloud Console:
Step 1: Create Project
Step 2: Setup Billing
Step 3: In PHP Storm: create new Project
	Select directory
	Enter name
	Select App Engine Project
	<continue>
	Enter Application Id
	Select Google SDK Directory
	<continue>
Step 4: Update Project Settings
	File-->settings
	"Google App Engine for PHP"
		Enter Email Address
		Enter Google Apps password
Step 5: Open app.yaml
	Change version to 'setup'
Step 6: Configure local dev engine
	Run->Edit Configuration
	Select path to php-cgi
		Notes: must install php5-cgi, php5-mysql(or mysqlnd)
		Caution: Google Apps engine won't run if the memcached extension is enabled.  If your using this locally for website development, make sure that to disable it for cgi.

Step 7: Run->Run

Step 8: open web browser, go to http://localhost:8080

Step 9: Tools-->Google App Engine-->Upload

Step 10: Go to your list of Google Projects
https://cloud.google.com/console#/project

Step 11: Open the google project for this site

Step 12: Go to App Engine --> Instances

Step 13: Go to Main --> Versions

Step 14: Confirm the instance is working by checking both the version url and the site url:
http://setup.overnumerousness-site.appspot.com/
http://overnumerousness-site.appspot.com/

Since we only have 1 version, both display the same information

Step 15: Explore the App Engine screen:
Dashboard, Instances, and Logs

Step 16: setup Git
go to directory where the files are
git init

edit/create a git ignore config file.  I use the one from Joomla:
nano .gitignore
# IDE & System Related Files #
.buildpath
.project
.settings
.DS_Store
.idea

# Local System Files (i.e. cache, logs, etc.) #
/administrator/cache
/cache
/logs
/tmp
/configuration.php
/.htaccess
/web.config

# Test Related Files #
/phpunit.xml
/tests/system/webdriver/tests/logs/

--
git add .
git commit -m 'First commit'
git branch -m master setup_project


Step 17: Refresh PHP Storm
File-->Synchronize

Step 18:
Change app.yaml:
Version: setup-1

Add to handlers:

setup info.php
- url: info.php
  script: info.php

Create a new file, info.php which just calls "phpinfo();"

Create a php.ini file in the root directory with the line:
google_app_engine.enable_functions = "phpversion, phpinfo, php_sapi_name"

These settings will allow us to do some sanity checking on how PHP is configured on the app engine server while we are deploying.  If you wish, you may disable them later.

Run App,
confirm http://localhost:8080/ works [still points to old code!]
confirm http://localhost:8080/info.php works

Deploy app, confirm there is a new version of the instance,
Confirm the new version url works:
http://setup-1.overnumerousness-site.appspot.com/
Confirm the info url works:
http://setup-1.overnumerousness-site.appspot.com/info.php

Confirm the info url does not work[more accurately, just loads main.php] for the old version:
http://setup.overnumerousness-site.appspot.com/info.php
Confirm the default is still using main.php
http://overnumerousness-site.appspot.com/info.php

Deploy app, confirm there is a new version of the instance, confirm it works the same as the old one did


Step 19:
Now add in Joomla Repositories:
https://help.github.com/articles/syncing-a-fork

git remote add origin git@github.com:garyamort/joomla-cms.git
git remote add upstream https://github.com/joomla/joomla-cms.git
git fetch origin
git branch --track master origin/master
git checkout master
git fetch upstream
git merge upstream/master
git push

Now create new setup branch
git checkout setup_project
git branch setup_joomla
git checkout setup_joomla

Now merge joomla into this branch
git merge master
	Create a merge message, for example "Combining GAE project with Joomla CMS"
NOTE: for a cleaner directory structure, you can merge the joomla directory into a subdirectory called "joomla".  This is the method used in the wordpress tutorial, all the wordpress code goes to a folder called "wordpress".  I choose not to because I am merging git repositories and prefer to avoid moving files to ensure version history logs are sane and easily mergable.

Confirm:
check code directory
run git log to show code


Step 20: Refresh PHP Storm
File-->Synchronize

Step 21:
Change app.yaml:
Version: setup-joomla-1

Run App, confirm http://localhost:8080/ works [still points to old code!]
Deploy app, confirm there is a new version of the instance, confirm it works the same as the old one did

Step 22: Commit changes in PHPStorm
File-->Settings
-->Version Control, make sure root directory is set to use Git as it's VCS
Apply, Ok
right click on root directory in Project
go to Git-->Commit Directory
--make sure "Perform code analysis" and "Check TODO" are not clicked
Enter a commit message, such as "Version name change for Joomla merge"
Commit


Step 22: Get Joomla ready to install
Edit app.yaml

Bump the version:
Version: setup-joomla-2

Replace the handlers.  In groups:
- url: /media/(.*\.(css$|$js|ico$|jpg$|png$|gif$))
  static_files: media/\1
  upload: /media/(.*\.(css$|$js|ico$|jpg$|png$|gif$))
  application_readable: false

- url: /templates/(.*\.(css$|$js|ico$|jpg$|png$|gif$))
  static_files: templates/\1
  upload: templates/(.*\.(css$|$js|ico$|jpg$|png$|gif$))
  application_readable: false

- url: /administrator/templates/(.*\.(css$|$js|ico$|jpg$|png$|gif$))
  static_files: administrator/templates/\1
  upload: administrator/templates/(.*\.(css$|$js|ico$|jpg$|png$|gif$))
  application_readable: false
  secure: always

- url: /installation/template/(.*\.(css$|$js|ico$|jpg$|png$|gif$))
  static_files: installation/template/\1
  upload: installation/template/(.*\.(css$|$js|ico$|jpg$|png$|gif$))
  application_readable: false
  secure: always

These mappings do 4-5 things:
1)url: defines a pattern to look for in a url.  In this case the url has to end with a media file extension - css, js, ico, jpg, png, or gif AND has to be in one of the 4 directories that we expect media files to be in - media, templates, administrator/templates or installation/templates

2) static_files: creates a pattern to find the static file on the file system.  So for files in the media folder for example, it will take everything after the /media/ and stuff it into the variable \1
Then that variable is appended to the path media/  Kind of a long way to state "do nothing" but this gives us the flexibility to stick media files elsewhere and have the url look like something different

3) upload: defines a file pattern for what static files should be uploaded

4) application_readable: can these files be read by our PHP scripts... the only reason to do so is if we are using them for image processing, or trying to compress the javascript or css.  We don't do that by default, so it is disabled

5) secure: should this url only be accessed via https.  For installation and administration the answer is yes.  For everything else, it is not


Ok, so now we need some handlers for PHP scripts:
- url: /installation/(.+)
  script: installation/\1
  secure: always

- url: /installation/
  script: installation/index.php
  secure: always

- url: /administrator/(.+)
  script: administrator/\1
  secure: always

- url: /administrator/
  script: administrator/index.php
  secure: always

- url: /(.+)?/?
  script: index.php


These lines should, if I've read everything correctly, force the index.php file to be used for all requests.  As an exception, requests going to the administrator or installation directories will instead use the index.php file IN those directories.  I've also configured things so that accessing those directories on app engine will always be done via HTTPS

Delete main.php



Session Bug fixes:
Google has a special implementation of Memcache and Memcached available.  Memcache is also used in GAE for session storage.   The Joomla Memcached storage handler checks to see if both the Memcached extension is loaded AND the Memcached class exists cannot be used if both are not true.

Therefore, I created a gaememcached storage engine based on memcached in the folder
libraries/joomla/session/storage/gaememcached.php
https://gist.github.com/garyamort/7758245

I need to go back and clean it up, plus find a better way to "install" it for use.  In the meantime, this file must be created before Joomla can be installed.