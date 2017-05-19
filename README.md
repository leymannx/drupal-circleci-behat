# Deploy and test a Drupal 8 multisite with Circle CI and Behat 

[![CircleCI](https://circleci.com/gh/leymannx/d8-multisite-behat-travis.svg?style=svg)](https://circleci.com/gh/leymannx/d8-multisite-behat-travis)

- First I created a new project from https://github.com/drupal-composer/drupal-project
- Then connected to Circle CI and added a `circle.yml` to the root
- It will install Drupal and run the tests
- If everythings went well it will run a deploy script on the server

Setting up the deployment wasn't that easy. In the end I created a new user on the server, added him to `www-data` and let him clone the repo. I created an SSH key pair, added the public key to his own `authorized_keys` file and the private key on Circle explicitly having a hostname set. In the end of `circle.yml` this user then `ssh` to the server and triggers the deploy script.

Next big step (for me) was to get `drush` running that then runs the Drupal installation. One of the most important things to remember were:
- in `circle.yml` each command is run in a separate shell, which means they do not share environments with preceding commands, so `cd` into one directory perform this and that and on the next line then you'll again start from project root.
- You'll run Circle CI's Ubuntu as user `ubuntu`. Same user `ubuntu` for MySQL. No password.

Now Behat.

- to be continued
