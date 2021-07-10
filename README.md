# Drupal CirceCI Behat Selenium

kick-start example

[![CircleCI](https://circleci.com/gh/leymannx/drupal-circleci-behat.svg?style=svg)](https://circleci.com/gh/leymannx/drupal-circleci-behat)

## [`tests/behat/behat.yml`](https://github.com/leymannx/drupal-circleci-behat/blob/develop/tests/behat/behat.yml):

```yaml
default:
  suites:
    default:
      contexts:
        - FeatureContext
        - Drupal\DrupalExtension\Context\DrupalContext
        - Drupal\DrupalExtension\Context\MinkContext
        - Drupal\DrupalExtension\Context\MessageContext
        - Drupal\DrupalExtension\Context\DrushContext
  extensions:
    Behat\MinkExtension:
      base_url: http://drupal-circleci-behat.localhost
      goutte: ~
      selenium2:
        wd_host: http://drupal-circleci-behat.localhost:4444/wd/hub
        capabilities:
          marionette: true
          browser: chrome
      browser_name: chrome
    Drupal\DrupalExtension:
      blackbox: ~
      api_driver: drupal
      drupal:
        drupal_root: '%paths.base%/../../web'
```

## [`.circleci/config.yml`](https://github.com/leymannx/drupal-circleci-behat/blob/develop/.circleci/config.yml):

```yaml
version: 2
jobs:
  build:
    docker:
      - image: circleci/php:7.4-apache-node-browsers
      - image: circleci/mariadb:latest
        environment:
          - MYSQL_ROOT_HOST=%
    working_directory: ~/drupal-circleci-behat
    steps:
      - checkout
      - run:
          name: Setup Apache
          command: |
            sudo cp .circleci/env/drupal-circleci-behat.conf /etc/apache2/sites-available/drupal-circleci-behat.conf
            sudo a2ensite drupal-circleci-behat
            sudo service apache2 start
            echo 127.0.0.1 drupal-circleci-behat.localhost | sudo tee -a /etc/hosts
      - run:
          name: Setup tools
          command: |
            sudo apt-get -qq update && sudo apt-get -qqy upgrade
            sudo apt-get -yqq install libpng-dev libonig-dev mariadb-client nano xvfb
            sudo docker-php-ext-install gd mbstring mysqli pdo pdo_mysql
            sudo service apache2 restart
      - run:
          name: Start Xvfb
          command: |
            sudo Xvfb :7055
            export DISPLAY=:7055
          background: true
      - run:
          name: Download Selenium
          command: |
            curl -O http://selenium-release.storage.googleapis.com/3.141/selenium-server-standalone-3.141.5.jar
      - run:
          name: Start Selenium
          command: |
            mkdir -p /tmp/selenium
            java -jar selenium-server-standalone-3.141.5.jar -log /tmp/selenium/selenium.log
          background: true
      - restore_cache:
          keys:
            - v1-dependencies-{{ checksum "composer.lock" }}
      - run:
          name: Composer Install
          command: |
            composer install -n --prefer-dist
            echo 'export PATH=$HOME/drupal-circleci-behat/vendor/bin:$PATH' >> $BASH_ENV
            source /home/circleci/.bashrc
      - save_cache:
          paths:
            - ./vendor
          key: v1-dependencies-{{ checksum "composer.lock" }}
      - run:
          name: Setup Drupal
          command: |
            cp .circleci/env/.htaccess web/.htaccess
            cp .circleci/env/settings.local.php web/sites/default/settings.local.php
            cd web
            drush -y site:install --existing-config
      - run:
          name: Tests
          command: |
            mkdir -p tests/behat/test-results/junit
            cd tests/behat
            behat --no-snippets -f pretty -o std -f junit -o test-results/junit/junit.xml
      - store_test_results:
          path: tests/behat/test-results
      - store_artifacts:
          path: /tmp/selenium
  deploy:
    machine:
      enabled: true
    working_directory: ~/drupal-circleci-behat
    steps:
      - checkout
      - run:
          name: Fix ssh Could not resolve hostname
          command: |
            ssh-keyscan "${LIVE_IP}" >> ~/.ssh/known_hosts
            ssh-keyscan "${DEV_IP}" >> ~/.ssh/known_hosts
      - add_ssh_keys:
          fingerprints:
            - "14:09:a1:b2:b3:c4:d5:e6:f7:g8:h9:81:"
      - run:
          name: Deploy main
          command: if [ "${CIRCLE_BRANCH}" == "main" ]; then ssh -p "${LIVE_PORT}" "${LIVE_USER}"@"${LIVE_IP}" "cd /var/www/wordpress-circleci-behat/scripts/deployment && . deploy.sh ${CIRCLE_SHA1}"; else echo "Skipped"; fi
      - run:
          name: Deploy dev
          command: if [ "${CIRCLE_BRANCH}" == "develop" ]; then ssh -p "${DEV_PORT}" "${DEV_USER}"@"${DEV_IP}" "cd /var/www/wordpress-circleci-behat/scripts/deployment && . deploy.sh ${CIRCLE_SHA1}"; else echo "Skipped"; fi
      - run: echo "${CIRCLE_BRANCH}"; echo "${CIRCLE_SHA1}";
workflows:
  version: 2
  build-and-deploy:
    jobs:
      - build
      - deploy:
          requires:
            - build
```
