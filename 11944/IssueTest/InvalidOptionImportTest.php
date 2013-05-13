<?php

require_once 'CiviTest/CiviSeleniumTestCase.php';

class WebTest_IssueTest_InvalidOptionImportTest extends ImportCiviSeleniumTestCase {
  
  private $id, $value;

  protected function setUp() {
    parent::setUp();
  }

  function testImport() {
	
	$path = '/home/nishant/Downloads/test.csv';
	
    $this->open($this->sboxPath);

    $this->webtestLogin(TRUE);
    $this->open($this->sboxPath . "civicrm/import/contact?reset=1");
    $this->waitForElementPresent('uploadFile', $path);
    $this->type('uploadFile', '');
    $this->click('skipColumnHeader');
    $this->click('CIVICRM_QFID_4_4');
    $this->click('_qf_DataSource_upload-bottom');
    $this->waitForPageToLoad('300000');
    $this->select('mapper_0_0', 'value=id');
    $this->select('mapper_1_0', 'value=custom_1');
    $this->click('_qf_MapField_next-bottom');
    $this->waitForPageToLoad('300000');
    $this->id = $this->getText("xpath=//div[@id='map-field']/table/tbody/tr[2]/td[2]");
    $this->value = $this->getText("xpath=//div[@id='map-field']/table/tbody/tr[3]/td[2]");
    
    $this->assertTrue(!$this->isTextPresent("invalid data"), "Invalid Data in CSV");
    $this->click('_qf_Preview_next-top');
    $this->waitForPageToLoad($this->getTimeoutMsec());
    $this->assertTrue((bool)preg_match("/^Are you sure you want to Import now[\s\S]$/", $this->getConfirmation()));
    $this->chooseOkOnNextConfirmation();
    $this->waitForPageToLoad($this->getTimeoutMsec());

    // Visit summary page.
    $this->waitForElementPresent("_qf_Summary_next");
    $this->waitForPageToLoad('300000');
    $this->_checkBug();
  }

  function _checkBug() {
    $this->open($this->sboxPath . "civicrm/contact/view?reset=1&cid=" . $this->id);
    $this->click("xpath=//div[@class='collapsible-title'][text()[contains(., 'Constituent Information')]]");
    $text = $this->getText("xpath=//div[@class='crm-label'][text()[contains(., 'Most Important Issue')]]/following-sibling::div");
    $this->assertTrue($this->value == $text ? true : false, "Failed");
  }

}
