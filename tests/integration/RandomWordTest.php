<?php

use App\DataStructures\RingBuffer;
use App\User;
use App\Word;

class RandomWordTest extends WordTest
{
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
    public function returns_the_same_word_when_repeated_request()
    {
        $word = $this->createWordForUser();
        $this->assertTrue(Word::count() == 1);

        $this->visit(route('random_word'));
        $word->delete();
        $this->setExpectedException('Illuminate\Foundation\Testing\HttpException');
        $this->visit(route('random_word'));

        $this->dontSee(route('add_word'));
    }

    /** @test */
    public function returns_new_words_and_remembers_old_ones()
    {
        $numberOfWordsToRemember = 5;
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
        $mostRecentWordIds = Session::get('mostRecentWordIds');

        $this->withSession(['mostRecentWordIds' => $mostRecentWordIds, 'mostRecentWordHaveBeenChecked' => true])
            ->visit(route('random_word'));
        $mostRecentWordIds = Session::get('mostRecentWordIds');

        $this->assertTrue(count($mostRecentWordIds->getNonemptyElements()) == 2);
        $this->assertEquals(count(array_unique($mostRecentWordIds->getNonemptyElements())), count($mostRecentWordIds->getNonemptyElements()));
    }
}
