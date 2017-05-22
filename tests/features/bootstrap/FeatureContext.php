<?php

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
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
  public function takeScreenShotAfterFailedStep(afterStepScope $scope) {

    if (99 === $scope->getTestResult()->getResultCode()) {

      $filename = microtime(true).'.html';
      $html = $this->getSession()->getDriver()->getContent();
      file_put_contents('../tests/screenshots/' . $filename, $html);
    }
  }

}
