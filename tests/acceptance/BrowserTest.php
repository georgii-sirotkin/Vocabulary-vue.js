<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;

abstract class BrowserTest extends TestCase
{
	protected $driver;
	
	public function setUp()
	{
		putenv('DB_CONNECTION=mysql');
		parent::setUp();
		$capabilities = array(WebDriverCapabilityType::BROWSER_NAME => env('BROWSER_NAME'));
		$this->driver = RemoteWebDriver::create(env('SELENIUM_SERVER_URL'), $capabilities);
        $this->driver->manage()->timeouts()->implicitlyWait(6);
	}

    public function tearDown()
    {
    	$this->driver->quit();
    	parent::tearDown();
    	putenv('DB_CONNECTION=mysql_testing');
    }

    protected function waitForElementToAppear(WebDriverBy $element)
    {
    	$this->driver->wait(10, 50)->until(WebDriverExpectedCondition::visibilityOfElementLocated($element));
    }

    protected function waitForElementToDisappear(WebDriverBy $element)
    {
        $this->driver->wait(10, 50)->until(WebDriverExpectedCondition::invisibilityOfElementLocated($element));
    }
}