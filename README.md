# Deploy and test a Drupal 8 multisite with Circle CI and Behat 

[![CircleCI](https://circleci.com/gh/leymannx/drupal-circleci-behat.svg?style=svg)](https://circleci.com/gh/leymannx/d8-multisite-behat-travis)

- Created a new D8 project from https://github.com/drupal-composer/drupal-project.
- Created a new GitHub repo, connected it to Circle CI and added a `circle.yml`.
- Configured `circle.yml` to install Drupal, to run tests and to trigger `git pull` on remote server.

### Deployment
- Created a new user on the server, added him to `www-data` and let him clone the repo.
- Created an SSH key pair for this user, added the public key to his own `authorized_keys` file and the private key on Circle (hostname set).
- In the end of the Circle CI routine we'll `ssh` to the live/dev server as this user and have him run the deploy script.
- Note: Ensure to have pulled the necessary scripts at least once on the server you'll run them from

### Subshell
- In `circle.yml` each command is run in a separate shell, which means they do not share environments with preceding commands (each line you start from `~/project-name` no matter what you've done the line before).
- You'll run Circle CI's Ubuntu as user `ubuntu`.
- Same user `ubuntu` for MySQL. No password.

### Apache
- Circle docs provide a [sample config for Apache](https://circleci.com/docs/1.0/language-php/#php-apache) which won't work.
- You have to set some additional directives as done in my [`circle.conf`](https://github.com/leymannx/drupal-circleci-behat/blob/develop/circle.conf).
