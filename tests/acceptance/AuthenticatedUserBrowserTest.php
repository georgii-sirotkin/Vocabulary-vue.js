<?php

use App\User;
use App\Word;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;

abstract class AuthenticatedUserBrowserTest extends BrowserTest
{
	protected $user;

	public function setUp()
	{
		parent::setUp();
		$this->user = User::where("email", config('credentials.vocabulary.email'))->first();
		$this->login();
	}

	public function tearDown()
	{
		$this->user->words()->delete();
		parent::tearDown();
	}

    protected function login()
    {
        $this->driver->get($this->baseUrl . '/login');
        $this->driver->findElement(WebDriverBy::name('email'))->click();
        $this->driver->getKeyboard()->sendKeys(config('credentials.vocabulary.email'));
        $this->driver->getKeyboard()->sendKeys(WebDriverKeys::TAB);
        $this->driver->getKeyboard()->sendKeys(config('credentials.vocabulary.password'));
        $this->driver->getKeyboard()->sendKeys(WebDriverKeys::ENTER);
    }

    protected function createWord(array $data)
    {
    	return $this->user->words()->save(factory(Word::class)->make($data));
    }
}