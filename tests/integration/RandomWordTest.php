<?php

use App\DataStructures\RingBuffer;
use App\User;
use App\Word;

class RandomWordTest extends WordTest
{
    /**
     * Status codes.
     */
    const INCORRECT_ANSWER = 0;
    const CORRECT_ANSWER = 1;
    const CORRECT_ANSWER_WITH_SPELLING_MISTAKES = 2;
    const NO_ANSWER = 3;

    /** @test */
    public function prompts_to_add_words_when_there_are_no_words()
    {
        $this->visit(route('random_word'));

        $this->see(route('add_word'));
    }

    /** @test */
    public function gets_one_of_several_words()
    {
        $wordIds = [];
        foreach (range(0, 3) as $number) {
            $wordIds[] = $this->createWordForUser()->id;
        }

        $this->visit(route('random_word'));

        $this->assertTrue(in_array(Session::get('mostRecentWordIds')->top(), $wordIds));
    }

    /** @test */
    public function doesnt_fetch_other_users_word()
    {
        $this->createWordForUser();
        $otherUser = factory(User::class)->create();
        $this->assertTrue(Word::count() == 1);

        $this->actingAs($otherUser);
        $this->visit(route('random_word'));

        $this->see(route('add_word'));
    }

    /** @test */
    public function prompts_to_add_a_word_when_not_checked_random_word_was_deleted()
    {
        $word = $this->createWordForUser();
        $this->assertEquals(1, Word::count());

        $this->visit(route('random_word'));
        $sessionData = Session::all();
        $word->delete();

        $this->withSession($sessionData)
            ->visit(route('random_word'));

        $this->see(route('add_word'));
    }

    /** @test */
    public function returns_new_words_and_remembers_old_ones()
    {
        $numberOfWordsToRemember = config('settings.number_of_words_to_remember');
        foreach (range(1, $numberOfWordsToRemember) as $number) {
            $this->createWordForUser(['right_guesses_number' => 0]);
        }

        $mostRecentWordIds = new RingBuffer($numberOfWordsToRemember);
        foreach (range(1, $numberOfWordsToRemember) as $number) {
            $this->withSession(['mostRecentWordIds' => $mostRecentWordIds])
                ->visit(route('next_random_word'));
            $mostRecentWordIds = Session::get('mostRecentWordIds');
            $this->assertEquals(count($mostRecentWordIds->getNonemptyElements()), $number);
            $this->assertEquals(count(array_unique($mostRecentWordIds->getNonemptyElements())), count($mostRecentWordIds->getNonemptyElements()));
        }
    }

    /** @test */
    public function doesnt_return_the_same_word_when_word_was_checked()
    {
        foreach (range(1, 2) as $number) {
            $this->createWordForUser(['right_guesses_number' => 0]);
        }

        $this->visit(route('random_word'));
        $sessionData = Session::all();

        $this->withSession($sessionData)
            ->json('POST', route('check_answer'), ['answer' => '']);
        $sessionData = Session::all();
        $this->assertTrue($sessionData['mostRecentWordHasBeenChecked']);

        $this->withSession($sessionData)
            ->visit(route('random_word'));

        $sessionData = Session::all();

        $this->assertFalse($sessionData['mostRecentWordHasBeenChecked']);

        $this->assertCount(2, $sessionData['mostRecentWordIds']->getNonemptyElements());
        $this->assertEquals(count(array_unique($sessionData['mostRecentWordIds']->getNonemptyElements())), count($sessionData['mostRecentWordIds']->getNonemptyElements()));
    }

    /** @test */
    public function returns_correct_answer_status_and_increases_number_of_right_guesses()
    {
        $word = $this->createWordForUser();

        $this->visit(route('random_word'));
        $mostRecentWordIds = Session::get('mostRecentWordIds');

        $this->withSession(['mostRecentWordIds' => $mostRecentWordIds])
            ->json('POST', route('check_answer'), ['answer' => $word->word])
            ->seeJson(["statusCode" => self::CORRECT_ANSWER]);

        $updatedWord = $word->fresh();

        $this->assertEquals($word->right_guesses_number + 1, $updatedWord->right_guesses_number);
    }

    /** @test */
    public function returns_correct_answer_status_when_case_doesnt_match()
    {
        $word = $this->createWordForUser(['word' => 'test']);

        $this->visit(route('random_word'));
        $mostRecentWordIds = Session::get('mostRecentWordIds');

        $this->withSession(['mostRecentWordIds' => $mostRecentWordIds])
            ->json('POST', route('check_answer'), ['answer' => 'Test'])
            ->seeJson(["statusCode" => self::CORRECT_ANSWER]);
    }

    /** @test */
    public function returns_correct_with_mistakes_and_increases_number_of_right_guesses()
    {
        $minNumberOfCharsPerOneMistake = config('settings.min_number_of_chars_per_one_mistake');
        $wordString = str_repeat('a', $minNumberOfCharsPerOneMistake);
        $word = $this->createWordForUser(['word' => $wordString]);

        $this->visit(route('random_word'));
        $mostRecentWordIds = Session::get('mostRecentWordIds');

        $this->withSession(['mostRecentWordIds' => $mostRecentWordIds])
            ->json('POST', route('check_answer'), ['answer' => $wordString . 'e']) // submit an answer with a spelling mistake
            ->seeJson(["statusCode" => self::CORRECT_ANSWER_WITH_SPELLING_MISTAKES]);

        $updatedWord = $word->fresh();

        $this->assertEquals($word->right_guesses_number + 1, $updatedWord->right_guesses_number);
    }

    /** @test */
    public function returns_incorrect_and_decreases_number_of_right_guesses()
    {
        $word = $this->createWordForUser(['word' => 'cat', 'right_guesses_number' => 1]);

        $this->visit(route('random_word'));
        $mostRecentWordIds = Session::get('mostRecentWordIds');

        $this->withSession(['mostRecentWordIds' => $mostRecentWordIds])
            ->json('POST', route('check_answer'), ['answer' => 'bed'])
            ->seeJson(["statusCode" => self::INCORRECT_ANSWER]);

        $updatedWord = $word->fresh();

        $this->assertEquals($word->right_guesses_number - 1, $updatedWord->right_guesses_number);
    }

    /** @test */
    public function returns_no_answer_code_and_descreases_number_of_right_guesses()
    {
        $word = $this->createWordForUser(['word' => 'cat', 'right_guesses_number' => 1]);

        $this->visit(route('random_word'));
        $mostRecentWordIds = Session::get('mostRecentWordIds');

        $this->withSession(['mostRecentWordIds' => $mostRecentWordIds])
            ->json('POST', route('check_answer'), ['answer' => ''])
            ->seeJson(["statusCode" => self::NO_ANSWER]);

        $updatedWord = $word->fresh();

        $this->assertEquals($word->right_guesses_number - 1, $updatedWord->right_guesses_number);
    }
}
