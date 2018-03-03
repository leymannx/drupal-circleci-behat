# Drupal CirceCI Behat

kick-start example

[![CircleCI](https://circleci.com/gh/leymannx/drupal-circleci-behat/tree/develop.svg?style=svg)](https://circleci.com/gh/leymannx/drupal-circleci-behat/tree/develop)

```
version: 2
jobs:
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
          name: Apache
          command: |
            sudo cp .circleci/drupal-circleci-behat.conf /etc/apache2/sites-available/drupal-circleci-behat.conf
            sudo a2ensite drupal-circleci-behat
            sudo service apache2 start
            
      - run:
          name: Host
          command: |
            echo 127.0.0.1 drupal-circleci-behat.localhost | sudo tee -a /etc/hosts
            cat /etc/hosts
            curl drupal-circleci-behat.localhost

      - run:
          name: Tools
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
          name: Site Install
          command: |
            cd web
            drush site:install --db-url=mysql://root@127.0.0.1/circle_test --site-name='Hello World' -y

      - run:
          name: Tests
          command: |
            behat --no-snippets -f pretty -o std

  deploy:
    machine:
      enabled: true
    working_directory: ~/drupal-circleci-behat
    steps:
      - checkout
      - run: echo "$CIRCLE_BRANCH"

workflows:
  version: 2
  build-and-deploy:
    jobs:
      - build
      - deploy:
          requires:
            - build
```
