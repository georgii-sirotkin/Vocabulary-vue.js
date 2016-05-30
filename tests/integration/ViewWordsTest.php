<?php

use App\User;

class ViewWordsTest extends WordTest
{
    /** @test */
    public function user_sees_his_words()
    {
        $words = [];
        foreach (range(0, 3) as $number) {
            $words[] = $this->createWordForUser()->word;
        }

        $this->visit(route('words'));
        foreach ($words as $word) {
            $this->see($word);
        }
    }

    /** @test */
    public function user_doesnt_see_other_users_words()
    {
        $words = [];
        foreach (range(0, 3) as $number) {
            $words[] = $this->createWordForUser()->word;
        }
        $this->assertEquals(4, $this->user->words()->count());
        $this->visit(route('words'));
        foreach ($words as $word) {
            $this->see(">$word</a>");
        }


        $anotherUser = factory(User::class)->create();
        $this->actingAs($anotherUser);
        $this->assertEquals(0, $anotherUser->words()->count());

        $this->visit(route('words'));

        foreach ($words as $word) {
            $this->dontSee(">$word</a>");
        }
    }

    /** @test */
    public function user_is_prompted_to_add_a_word_when_there_are_no_words()
    {
        $this->visit(route('words'));

        $this->see('Add a word');
    }
}
