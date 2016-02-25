<?php

use App\Definition;
use App\User;
use App\Word;

class ViewWordTest extends WordTest
{
    /** @test */
    public function can_get_word_by_slug()
    {
        $word = $this->createWordForUser();
        $this->visit(route('view_word', [$word->slug]))
            ->see($word->word);
    }

    /** @test */
    public function can_get_word_by_id()
    {
        $word = $this->createWordForUser();
        $this->visit(route('view_word', [$word->id]))
            ->see($word->word);
    }

    /** @test */
    public function user_sees_his_own_word_even_if_slugs_are_the_same()
    {
        $word = $this->createWordForUser(['word' => 'test']);
        $word->addDefinitionsWithoutTouch(factory(Definition::class, 3)->make()->all());

        $anotherUser = factory(User::class)->create();
        $this->actingAs($anotherUser);
        $theSameWord = factory(Word::class)->make(['word' => 'test']);
        $anotherUser->addWord($theSameWord);
        $theSameWord->addDefinitionsWithoutTouch(factory(Definition::class, 3)->make()->all());
        $this->assertTrue($word->slug == $theSameWord->slug);

        $this->visit(route('view_word', [$theSameWord->slug]));
        foreach ($theSameWord->definitions as $definition) {
            $this->see($definition);
        }

        $this->actingAs($this->user);
        $this->visit(route('view_word', [$word->slug]));
        foreach ($word->definitions as $definition) {
            $this->see($definition);
        }
    }

    /** @test */
    public function cant_view_other_users_word()
    {
        $word = $this->createWordForUser();
        $anotherUser = factory(User::class)->create();
        $this->actingAs($anotherUser);

        $this->call('GET', route('view_word', [$word->slug]), []);

        $this->assertResponseStatus(404);
    }
}
