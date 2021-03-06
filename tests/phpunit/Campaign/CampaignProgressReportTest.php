<?php

require_once __DIR__ . '/../BaseTestClass.php';

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * Test contribution DetailExtended class.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test class.
 *    Simply create corresponding functions (e.g. "hook_civicrm_post(...)" or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or test****() functions will
 *    rollback automatically -- as long as you don't manipulate schema or truncate tables.
 *    If this test needs to manipulate schema or truncate tables, then either:
 *       a. Do all that using setupHeadless() and Civi\Test.
 *       b. Disable TransactionalInterface, and handle all setup/teardown yourself.
 *
 * @group headless
 */
class CampaignProgressReportTest extends BaseTestClass implements HeadlessInterface, HookInterface, TransactionalInterface {

  protected $contacts = array();

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp() {
    parent::setUp();
    $this->enableAllComponents();
    $contact = $this->callAPISuccess('Contact', 'create', array('first_name' => 'Wonder', 'last_name' => 'Woman', 'contact_type' => 'Individual'));
    $this->contacts[] = $contact['id'];
  }

  public function tearDown() {
    parent::tearDown();
  }

  /**
   * Test the Progress report.
   *
   * @dataProvider getReportParameters
   *
   * @param array $params
   *   Parameters to pass to the report
   */
  public function testProgressReport($params) {
    $this->callAPISuccess('Order', 'create', array('contact_id' => $this->contacts[0], 'total_amount' => 5, 'financial_type_id' => 2));
    // Just checking no error at the moment.
    $this->getRows($params);
  }

  /**
   * Get datasets for testing the report
   */
  public function getReportParameters() {
    return array(
      'basic' => array(array(
        'report_id' => 'campaign/progress',
        'fields' => array(
          'campaign_id' => '1',
          'total_amount' => '1',
          'paid_amount' => '1',
          'balance_amount' => '1',
        ),
      )),
      'group_by_variant' => array(array(
        'report_id' => 'campaign/progress',
        'fields' => array(
          'campaign_id' => '1',
          'total_amount' => '1',
          'paid_amount' => '1',
          'balance_amount' => '1',
        ),
        'group_bys' => array(
          'campaign_id' => '1',
        ),
      )),
    );
  }

}
