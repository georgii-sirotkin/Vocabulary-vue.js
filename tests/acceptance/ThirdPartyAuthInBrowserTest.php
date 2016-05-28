<?php

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;

class ThirdPartyAuthInBrowserTest extends BrowserTest
{
	/** @test */
	public function login_with_facebook()
	{
		$this->driver->get($this->baseUrl . '/login');
		$this->driver->findElement(WebDriverBy::linkText('Log in with Facebook'))->click();
		$this->waitForElementToAppear(WebDriverBy::id('email'));
		$this->driver->findElement(WebDriverBy::id('email'))->click();
		$this->driver->getKeyboard()->sendKeys(config('credentials.facebook.login'));
		$this->driver->getKeyboard()->sendKeys(WebDriverKeys::TAB);
		$this->driver->getKeyboard()->sendKeys(config('credentials.facebook.password'));
		$this->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);
		
		$this->assertContains(route('home'), $this->driver->getCurrentUrl());
	}

	/** @test */
	public function login_with_google()
	{
		$this->driver->get($this->baseUrl . '/login');
		$this->driver->findElement(WebDriverBy::linkText('Log in with Google'))->click();
		$this->waitForElementToAppear(WebDriverBy::id('Email'));
		$this->driver->findElement(WebDriverBy::id('Email'))->click();
		$this->driver->getKeyboard()->sendKeys(config('credentials.google.email'));
		$this->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);
		$this->waitForElementToAppear(WebDriverBy::id('Passwd'));
		$this->driver->getKeyboard()->sendKeys(config('credentials.google.password'));
		$this->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);
		$this->driver->wait(10, 50)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('submit_approve_access')));
		$this->driver->findElement(WebDriverBy::id('submit_approve_access'))->click();

		$this->assertContains(route('home'), $this->driver->getCurrentUrl());
	}
}