<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Behat\Hook\Scope\AfterStepScope;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawDrupalContext implements SnippetAcceptingContext {

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }

  /**
   * @AfterStep
   */
  public function takeScreenShotAfterFailedStep(afterStepScope $scope)
  {
    if (99 === $scope->getTestResult()->getResultCode()) {
      $driver = $this->getSession()->getDriver();
      if (!($driver instanceof Selenium2Driver)) {
        return;
      }

      $filename = microtime(true).'.png';
      $path = $this->getContainer()
          ->getParameter('kernel.root_dir').'/../behat_screenshots';

      if (!file_exists($path)) {
        mkdir($path);
      }

      $this->saveScreenshot($filename, $path);
      file_put_contents($path . $filename, $this->getSession()->getDriver()->getScreenshot());
    }
  }

}
