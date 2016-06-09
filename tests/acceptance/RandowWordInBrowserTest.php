<?php

use Facebook\WebDriver\WebDriverBy;

class RandomWordInBrowserTest extends AuthenticatedUserBrowserTest
{
    /** @test */
    public function doesnt_return_the_same_word_when_word_was_checked()
    {
        $words = [];
        foreach (range(1, 2) as $number) {
            $words[] = $this->createWord(['right_guesses_number' => 0])->word;
        }

        $this->driver->findElement(WebDriverBy::linkText('Random'))->click();
        $firstWord = $this->submitEmptyAnswerAndGetCorrectOne();
        $this->assertContains($firstWord, $words);

        $this->driver->findElement(WebDriverBy::id('nextButton'))->click();
        $secondWord = $this->submitEmptyAnswerAndGetCorrectOne();
        $this->assertContains($secondWord, $words);

        $this->assertNotEquals($firstWord, $secondWord);
    }

    private function submitEmptyAnswerAndGetCorrectOne()
    {
        $this->waitForElementToAppear(WebDriverBy::id('answerForm'));
        $this->driver->findElement(WebDriverBy::xpath('//*[@id="answerForm"]/div/span/button'))->click();
        $this->waitForElementToAppear(WebDriverBy::id('responseArea'));
        return $this->driver->findElement(WebDriverBy::cssSelector('.page-header h3'))->getText();
    }
}