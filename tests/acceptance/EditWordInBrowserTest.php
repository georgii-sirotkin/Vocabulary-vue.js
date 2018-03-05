<?php

use App\Definition;
use App\User;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class EditWordInBrowserTest extends AuthenticatedUserBrowserTest
{
    /** @test */
    public function can_remove_image_without_uploading_a_new_one()
    {
        $word = $this->createWord(['image_filename' => 'test.jpg']);
        Storage::disk('public')->put($word->getImagePath(), 'data');
        $definitions = factory(Definition::class, 3)->make();
        $word->addDefinitionsWithoutTouch($definitions->all());

        $this->driver->get(route('words.edit', $word));
        $this->driver->findElement(WebDriverBy::id('deleteOldImage'))->click();
        $this->waitForElementToDisappear(WebDriverBy::id('oldImage'));
        $this->driver->findElement(WebDriverBy::cssSelector('.btn-primary'))->click();

        $this->assertEquals(route('words.index'), $this->driver->getCurrentUrl());
        $this->assertEquals(1, $this->user->words()->count());
        $updatedWord = $this->user->words()->first();
        $this->assertNull($updatedWord->image_filename);
        $this->assertFalse(Storage::disk('public')->exists($word->getImagePath()));
        $this->assertEquals($word->title, $updatedWord->word);
        $this->assertEquals(count($definitions), $updatedWord->definitions()->count());
        for ($i = 0; $i < count($definitions); $i ++) {
        	$this->assertEquals($definitions[$i]->definition, $updatedWord->definitions[$i]->definition);
        }
    }

    /** @test */
    public function can_update_word_and_definitions()
    {
        $word = $this->createWord(['image_filename' => null]);
        $oldDefinitions = factory(Definition::class, 3)->make()->all();
        $word->addDefinitionsWithoutTouch($oldDefinitions);

        $this->driver->get(route('words.edit', $word));
        $wordInput = $this->driver->findElement(WebDriverBy::id('wordInput'));
        $wordInput->clear();
        $wordInput->sendKeys('changed word');
        $deleteDefinitionButtonXpath = '//*[@id="definitionsContainer"]/div[2]/div[2]/button';
        $this->driver->findElement(WebDriverBy::xpath($deleteDefinitionButtonXpath))->click();
        $this->waitForElementToDisappear(WebDriverBy::xpath($deleteDefinitionButtonXpath));
        $this->driver->findElement(WebDriverBy::id('addDefinitionButton'))->click();
        $textareaXpath = '//*[@id="definitionsContainer"]/div[3]/div[1]/textarea';
        $this->waitForElementToAppear(WebDriverBy::xpath($textareaXpath));
        $newDefinition = factory(Definition::class)->make();
        $this->driver->findElement(WebDriverBy::xpath($textareaXpath))->sendKeys($newDefinition->definition);
        $newDefinitions = [$oldDefinitions[0], $oldDefinitions[2], $newDefinition];
        $this->driver->findElement(WebDriverBy::cssSelector('.btn-primary'))->click();

        $this->assertEquals(route('words.index'), $this->driver->getCurrentUrl());
        $this->assertEquals(1, $this->user->words()->count());
        $updatedWord = $this->user->words()->first();
        $this->assertContains('changed word', $updatedWord->word);
        $this->assertEquals(count($newDefinitions), $updatedWord->definitions()->count());
        for ($i = 0; $i < count($newDefinitions); $i ++) {
            $this->assertEquals($newDefinitions[$i]->definition, $updatedWord->definitions[$i]->definition);
        }
    }
}