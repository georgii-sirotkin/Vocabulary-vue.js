<?php

use App\Word;

class FindWordsTest extends WordTest
{
    /** @test */
    public function finds_words_with_like()
    {
        $words = [];
        $words[] = $this->createWordForUser(['title' => 'test']);
        $words[] = $this->createWordForUser(['title' => 'new test']);
        $words[] = $this->createWordForUser(['title' => 'new test!!!']);

        $this->call('GET', route('words.index'), ['search' => 'test']);

        foreach ($words as $word) {
            $this->see($word->title);
        }
    }

    /** @test */
    public function doesnt_find_different_words()
    {
        $this->createWordForUser(['title' => 'aaa']);

        $this->call('GET', route('words.index'), ['search' => 'bbb']);

        $this->dontSee('aaa');
    }

    /** @test */
    public function finds_words_with_levenshtein()
    {
        $minNumberOfCharsPerOneMistake = config('settings.min_number_of_chars_per_one_mistake_in_search');
        $searchString = str_repeat('z', $minNumberOfCharsPerOneMistake);
        $words = [];
        $words[] = $this->createWordForUser(['title' => substr($searchString, 1)]);
        $words[] = $this->createWordForUser(['title' => substr_replace($searchString, 'e', 0, 1)]);

        $this->call('GET', route('words.index'), ['search' => $searchString]);

        foreach ($words as $word) {
            $this->see(">$word->title</");
        }
    }
}
