<?php

// https://github.com/vicenteguerra/git-deploy
define("TOKEN", "secret-token");
define("REMOTE_REPOSITORY", "git@github.com:username/custom-project.git");
define("DIR", "/var/www/vhosts/repositories/custom-project");
define("BRANCH", "refs/heads/master");
define("LOGFILE", "deploy.log");
define("GIT", "/usr/bin/git");
define("AFTER_PULL", "/usr/bin/node ./node_modules/gulp/bin/gulp.js default");
