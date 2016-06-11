<?php

use App\Definition;
use App\User;
use Facebook\WebDriver\WebDriverBy;

class AddWordInBrowserTest extends AuthenticatedUserBrowserTest
{
    /** @test */
    public function add_word_with_definitions()
    {
    	$this->driver->get(route('add_word'));
    	$this->waitForElementToAppear(WebDriverBy::id('wordInput'));
    	$this->driver->findElement(WebDriverBy::id('wordInput'))->click();
    	$this->driver->getKeyboard()->sendKeys('test');
    	$definitions = factory(Definition::class, 3)->make()->all();
    	$this->driver->findElement(WebDriverBy::xpath('//*[@id="definitionsContainer"]/div/div[1]/textarea'))->click();
    	$this->driver->getKeyboard()->sendKeys($definitions[0]->definition);
    	for ($i = 1; $i < count($definitions); $i ++) {
    		$this->driver->findElement(WebDriverBy::id('addDefinitionButton'))->click();
    		$textareaXpath = '//*[@id="definitionsContainer"]/div[' . ($i + 1) . ']/div[1]/textarea';
    		$this->waitForElementToAppear(WebDriverBy::xpath($textareaXpath));
    		$this->driver->findElement(WebDriverBy::xpath($textareaXpath))->sendKeys($definitions[$i]->definition);
    	}
    	$this->driver->findElement(WebDriverBy::cssSelector('.btn-primary'))->click();

        $this->assertEquals(route('words'), $this->driver->getCurrentUrl());
        $this->assertEquals(1, $this->user->words()->count());
        $word = $this->user->words()->first();
        $this->assertEquals('test', $word->word);
        $this->assertNull($word->image_filename);
        $this->assertEquals(count($definitions), $word->definitions()->count());
        for ($i = 0; $i < count($definitions); $i ++) {
        	$this->assertEquals($definitions[$i]->definition, $word->definitions[$i]->definition);
        }
    }

    /** @test */
    public function can_add_word_with_jpg_file()
    {
    	$this->driver->get(route('add_word'));
    	$this->waitForElementToAppear(WebDriverBy::id('wordInput'));
    	$this->driver->findElement(WebDriverBy::id('wordInput'))->click();
    	$this->driver->getKeyboard()->sendKeys('test');
    	$this->driver->findElement(WebDriverBy::linkText('Upload'))->click();
    	$this->waitForElementToAppear(WebDriverBy::name('image'));
    	$this->driver->findElement(WebDriverBy::name('image'))->sendKeys($this->getPathToTestFile('image.jpg'));
    	$this->driver->findElement(WebDriverBy::cssSelector('.btn-primary'))->click();

    	$this->assertEquals(route('words'), $this->driver->getCurrentUrl());
        $this->assertEquals(1, $this->user->words()->count());
        $word = $this->user->words()->first();
        $this->assertEquals('test', $word->word);
        $this->assertNotNull($word->image_filename);
        $this->assertTrue(Storage::disk('public')->exists($word->getImagePath()));
        Storage::disk('public')->delete($word->getImagePath());
        $this->assertEquals(0, $word->definitions()->count());
    }
}