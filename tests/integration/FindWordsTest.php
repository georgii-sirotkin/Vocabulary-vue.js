<?php

use App\Word;

class FindWordsTest extends WordTest
{
    /** @test */
    public function finds_words_with_like()
    {
        $words = [];
        $words[] = $this->createWordForUser(['word' => 'test']);
        $words[] = $this->createWordForUser(['word' => 'new test']);
        $words[] = $this->createWordForUser(['word' => 'new test!!!']);

        $this->call('GET', route('words'), ['search' => 'test']);

        foreach ($words as $word) {
            $this->see($word->word);
        }
    }

    /** @test */
    public function doesnt_find_different_words()
    {
        $this->createWordForUser(['word' => 'aaa']);

        $this->call('GET', route('words'), ['search' => 'bbb']);

        $this->dontSee('aaa');
    }

    /** @test */
    public function doesnt_find_words_that_are_too_short_or_too_long()
    {
        $minNumberOfCharsPerOneMistake = config('settings.min_number_of_chars_per_one_mistake_in_search');
        $word = str_repeat('a', $minNumberOfCharsPerOneMistake);
        $shortWord = str_repeat('b', $minNumberOfCharsPerOneMistake - 2);
        $longWord = str_repeat('c', $minNumberOfCharsPerOneMistake + 2);
        $this->createWordForUser(['word' => $shortWord]);
        $this->createWordForUser(['word' => $longWord]);

        $this->call('GET', route('words'), ['search' => $word]);

        $this->dontSee($shortWord);
        $this->dontSee($longWord);
    }

    /** @test */
    public function finds_words_with_levenshtein()
    {
        $minNumberOfCharsPerOneMistake = config('settings.min_number_of_chars_per_one_mistake_in_search');
        $searchString = str_repeat('a', $minNumberOfCharsPerOneMistake);
        $words = [];
        $words[] = $this->createWordForUser(['word' => substr($searchString, 1)]);
        $words[] = $this->createWordForUser(['word' => substr_replace($searchString, 'e', 0, 1)]);

        $this->call('GET', route('words'), ['search' => $searchString]);

        foreach ($words as $word) {
            $this->see($word->word);
        }
    }
}
