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

        $this->visit(route('words.show', $word))
            ->see($word->title);
    }

    /** @test */
    public function cant_get_word_by_id()
    {
        $word = $this->createWordForUser();
        $this->expectException(\Laravel\BrowserKitTesting\HttpException::class);
        $this->visit(route('words.show', [$word->id]));
    }

    /** @test */
    public function user_sees_his_own_word_even_if_slugs_are_the_same()
    {
        $word = $this->createWordForUser(['title' => 'test']);
        $word->addDefinitionsWithoutTouch(factory(Definition::class, 3)->make()->all());

        $anotherUser = factory(User::class)->create();
        $this->actingAs($anotherUser);
        $theSameWord = factory(Word::class)->make(['title' => 'test']);
        $anotherUser->addWord($theSameWord);
        $theSameWord->addDefinitionsWithoutTouch(factory(Definition::class, 3)->make()->all());
        $this->assertEquals($word->slug, $theSameWord->slug);

        $this->visit(route('words.show', [$theSameWord->slug]));
        foreach ($theSameWord->definitions as $definition) {
            $this->see($definition->text);
        }

        $this->actingAs($this->user);
        $this->visit(route('words.show', $word));
        foreach ($word->definitions as $definition) {
            $this->see($definition->text);
        }
    }

    /** @test */
    public function cant_view_other_users_word()
    {
        $word = $this->createWordForUser();
        $anotherUser = factory(User::class)->create();
        $this->actingAs($anotherUser);

        $this->call('GET', route('words.show', $word), []);

        $this->assertResponseStatus(404);
    }
}
