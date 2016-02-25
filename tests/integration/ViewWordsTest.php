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
        $anotherUser = factory(User::class)->create();
        $this->actingAs($anotherUser);

        $this->visit(route('words'));

        foreach ($words as $word) {
            $this->dontSee($word);
        }
    }
}
