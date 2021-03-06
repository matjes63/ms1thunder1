<?php

namespace Drupal\Tests\select2\FunctionalJavascript\Form;

use Drupal\entity_test\Entity\EntityTestMulRevPub;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Tests the select2 element.
 *
 * @group select2
 */
class ElementTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['select2_form_test', 'entity_test'];

  /**
   * Tests select2 optgroups.
   */
  public function testOptgroups() {
    $this->drupalGet('/select2-optgroup-form');

    $this->click('.form-item-select2-optgroups .select2-selection.select2-selection--single');

    $this->assertNotEmpty($this->assertSession()->waitForElementVisible('css', '.select2-results__group'));

    $this->assertSession()->elementTextContains('css', '.select2-results__group', 'Baba');
    $this->assertSession()->elementTextContains('css', 'ul.select2-results__options li.select2-results__option ul.select2-results__options--nested li.select2-results__option', 'Nana');
  }

  /**
   * Test that in-between ajax calls are not creating new entities.
   */
  public function testAjaxCallbacksInBetween() {

    $page = $this->getSession()->getPage();
    $this->drupalGet('/select2-ajax-form');

    $this->click('.form-item-select2-ajax .select2-selection.select2-selection--multiple');
    $this->assertNotEmpty($this->assertSession()->waitForElementVisible('css', '.select2-search__field'));

    $page->find('css', '.select2-search__field')->setValue('New value');
    $page->find('css', '.select2-results__option--highlighted')->click();
    $page->pressButton('Call ajax');

    $this->assertEmpty(EntityTestMulRevPub::loadMultiple());
  }

  /**
   * Test loading of seven theme style.
   */
  public function testSevenTheme() {
    $this->container->get('theme_installer')->install(['seven']);
    $this->config('system.theme')
      ->set('default', 'seven')
      ->set('admin', 'seven')
      ->save();

    $this->drupalGet('/select2-optgroup-form');

    $this->assertSession()->elementExists('css', '.select2-container--seven');

    $select2_js = $this->xpath("//script[contains(@src, 'select2/js/select2.js')]");
    $this->assertEquals(1, count($select2_js));
    $select2_js = $this->xpath("//script[contains(@src, 'select2/dist/js/select2.min.js')]");
    $this->assertEquals(1, count($select2_js));
  }

}
