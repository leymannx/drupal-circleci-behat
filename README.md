# Drupal CirceCI Behat

kick-start example

[![CircleCI](https://img.shields.io/circleci/project/github/leymannx/drupal-circleci-behat/develop.svg)](https://circleci.com/gh/leymannx/drupal-circleci-behat/tree/develop)

Based on https://github.com/drupal-composer/drupal-project

```
version: 2
jobs:
  # THE BUILD STEP
  build:
    docker:
      - image: circleci/php:7.1-apache-node-browsers
      - image: circleci/mariadb:latest
        environment:
          - MYSQL_ROOT_HOST=%
    working_directory: ~/drupal-circleci-behat

    steps:
      - checkout
      - run:
          name: Setup Apache
          command: |
            sudo cp .circleci/drupal-circleci-behat.conf /etc/apache2/sites-available/drupal-circleci-behat.conf
            sudo a2ensite drupal-circleci-behat
            sudo service apache2 start
            echo 127.0.0.1 drupal-circleci-behat.localhost | sudo tee -a /etc/hosts
      - run:
          name: Setup tools
          command: |
            sudo apt-get -qq update && sudo apt-get -qqy upgrade
            sudo apt-get -yqq install libpng-dev mariadb-client nano
            sudo docker-php-ext-install gd mbstring mysqli pdo pdo_mysql
            sudo service apache2 restart
      - restore_cache:
          keys:
          - v1-dependencies-{{ checksum "composer.lock" }}
          - v1-dependencies-
      - run:
          name: Composer Install
          command: |
            composer install -n --prefer-dist
            echo 'export PATH=$HOME/drupal-circleci-behat/bin:$PATH' >> $BASH_ENV
            source /home/circleci/.bashrc
      - save_cache:
          paths:
            - ./vendor
          key: v1-dependencies-{{ checksum "composer.lock" }}
      - run:
          name: Setup Drupal
          command: |
            cd web
            drush site:install --db-url=mysql://root@127.0.0.1/circle_test --site-name='Hello World' -y
      - run:
          name: Tests
          command: |
            mkdir -p test-results/junit
            behat --no-snippets -f pretty -o std -f junit -o test-results/junit/junit.xml
      - store_test_results:
          path: test-results

  # THE DEPLOY STEP
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
            - "14:09:a1:b2:b3:c4:d5:e6:f7:g8:19:81:"
      - run:
          name: Deploy master
          command: if [ "${CIRCLE_BRANCH}" == "master" ]; then ssh -p "${LIVE_PORT}" "${LIVE_USER}"@"${LIVE_IP}" 'cd /var/www/drupal-circleci-behat/scripts/deployment && . deploy.sh'; else echo "Skipped"; fi
      - run:
          name: Deploy dev
          command: if [ "${CIRCLE_BRANCH}" == "develop" ]; then ssh -p "${DEV_PORT}" "${DEV_USER}"@"${DEV_IP}" 'cd /var/www/drupal-circleci-behat/scripts/deployment && . deploy.sh'; else echo "Skipped"; fi

# ONLY ON DEVELOP AND ON MASTER BRANCHES
workflows:
  version: 2
  build-and-deploy:
    jobs:
      - build:
          filters:
            branches:
              only:
                - develop
                - master
      - deploy:
          requires:
            - build
          filters:
            branches:
              only:
                - develop
                - master
```
