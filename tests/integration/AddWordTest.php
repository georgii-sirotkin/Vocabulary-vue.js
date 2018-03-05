<?php

use App\User;
use App\Word;

class AddWordTest extends WordTest
{
    /** @test */
    public function can_add_word_with_image_url_and_definitions()
    {
        $this->call('POST', route('words.store'), [
            'title' => 'test',
            'definitions' => [
                'test definition',
                'another definition'
            ],
            'imageUrl' => 'https://www.google.co.uk/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png',
            '_token' => csrf_token(),
        ]);

        $this->seeInDatabase('words', ['title' => 'test']);
        $this->seeInDatabase('definitions', ['text' => 'test definition']);
        $this->seeInDatabase('definitions', ['text' => 'another definition']);
        $word = Word::first();
        $this->assertEquals($this->user->id, $word->user_id);
        $this->assertEquals(2, $word->definitions()->count());
        $this->assertNotNull($word->image_filename);
        $this->assertTrue(Storage::disk('public')->exists($word->getImagePath()));
        Storage::disk('public')->delete($word->getImagePath());
    }

    /** @test */
    public function can_add_word_with_png_file()
    {
        $this->visit(route('words.create'))
            ->type('test', 'title')
            ->seePageIs(route('words.create'))
            ->attach($this->getPathToTestFile('image.png'), 'image')
            ->press('Save')
            ->seePageIs(route('words.index'));

        $word = Word::first();
        $this->assertEquals($this->user->id, $word->user_id);
        $this->assertEquals(0, $word->definitions()->count());
        $this->assertNotNull($word->image_filename);
        $this->assertTrue(Storage::disk('public')->exists($word->getImagePath()));
        Storage::disk('public')->delete($word->getImagePath());
    }

    /** @test */
    public function accepts_png_image_url()
    {
        $this->visit(route('words.create'))
            ->type('test', 'title')
            ->type('https://www.google.co.uk/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png', 'imageUrl')
            ->press('Save')
            ->seePageIs(route('words.index'));

        $word = Word::first();
        $this->assertEquals($this->user->id, $word->user_id);
        $this->assertEquals(0, $word->definitions()->count());
        $this->assertNotNull($word->image_filename);
        $this->assertTrue(Storage::disk('public')->exists($word->getImagePath()));
        Storage::disk('public')->delete($word->getImagePath());
    }

    /** @test */
    public function two_users_can_have_the_same_word()
    {
        $this->addWordWithDefinitions();
        $anotherUser = factory(User::class)->create();
        $this->actingAs($anotherUser);
        $this->addWordWithDefinitions();

        $words = Word::withoutGlobalScope('currentUser')->get();
        $this->assertEquals(2, Word::withoutGlobalScope('currentUser')->count());
        $this->assertEquals($words[0]->slug, $words[1]->slug);
    }

    /** @test */
    public function two_similar_words_have_different_slugs()
    {
        $word = $this->createWordForUser(['title' => 'test word']);
        $anotherWord = $this->createWordForUser(['title' => 'test-word']);

        $this->assertNotEquals($word->slug, $anotherWord->slug);
    }

    protected function addWordWithDefinitions()
    {
        $this->call('POST', route('words.store'), ['title' => 'test', 'definitions' => ['test definition', 'another definition']]);
    }
}
