<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Mink\Driver\Selenium2Driver;

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
  public function printLastResponseOnError(AfterStepScope $event)
  {
    if (!$event->getTestResult()->isPassed()) {
      $this->saveDebugScreenshot();
    }
  }

  /**
   * @Then /^save screenshot$/
   */
  public function saveDebugScreenshot()
  {
    $driver = $this->getSession()->getDriver();

    if (!$driver instanceof Selenium2Driver) {
      return;
    }

    if (!getenv('BEHAT_SCREENSHOTS')) {
      return;
    }

    $filename = microtime(true).'.png';
    $path = $this->getContainer()
        ->getParameter('kernel.root_dir').'/../behat_screenshots';

    if (!file_exists($path)) {
      mkdir($path);
    }

    $this->saveScreenshot($filename, $path);
  }

}
