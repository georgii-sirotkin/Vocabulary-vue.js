<?php

use App\User;
use App\Word;

class AddWordTest extends WordTest
{
    /** @test */
    public function can_add_word_with_image_url_and_definitions()
    {
        $this->call('POST', route('insert_word'), ['word' => 'test', 'definitions' => ['test definition', 'another definition'], 'imageUrl' => 'https://www.google.co.uk/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png']);

        $this->seeInDatabase('word', ['word' => 'test']);
        $this->seeInDatabase('definition', ['definition' => 'test definition']);
        $this->seeInDatabase('definition', ['definition' => 'another definition']);
        $word = Word::first();
        $this->assertEquals($this->user->id, $word->user_id);
        $this->assertEquals(2, $word->definitions()->count());
        $this->assertNotNull($word->image_filename);
        $this->assertTrue(Storage::exists($this->image->getFullFileName($word->image_filename)));
        $this->image->delete($word->image_filename);
    }

    /** @test */
    public function can_add_word_with_png_file()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->seePageIs(route('add_word'))
            ->attach($this->getPathToTestFile('image.png'), 'image')
            ->press('Save')
            ->seePageIs(route('words'));

        $word = Word::first();
        $this->assertEquals($this->user->id, $word->user_id);
        $this->assertEquals(0, $word->definitions()->count());
        $this->assertNotNull($word->image_filename);
        $this->assertTrue(Storage::exists($this->image->getFullFileName($word->image_filename)));
        $this->image->delete($word->image_filename);
    }

    /** @test */
    public function can_add_word_with_jpg_file()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->attach($this->getPathToTestFile('image.jpg'), 'image')
            ->press('Save')
            ->seePageIs(route('words'));

        $word = Word::first();
        $this->assertEquals($this->user->id, $word->user_id);
        $this->assertEquals(0, $word->definitions()->count());
        $this->assertNotNull($word->image_filename);
        $this->assertTrue(Storage::exists($this->image->getFullFileName($word->image_filename)));
        $this->image->delete($word->image_filename);
    }

    /** @test */
    public function add_word_with_definitions()
    {
        $this->call('POST', route('insert_word'), ['word' => 'test', 'definitions' => ['test definition', 'another definition']]);

        $this->assertRedirectedToRoute('words');
        $word = Word::first();
        $this->assertEquals(2, $word->definitions()->count());
        $this->assertNull($word->image_filename);
    }

    /** @test */
    public function accepts_png_image_url()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->type('https://www.google.co.uk/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png', 'imageUrl')
            ->press('Save')
            ->seePageIs(route('words'));

        $word = Word::first();
        $this->assertEquals($this->user->id, $word->user_id);
        $this->assertEquals(0, $word->definitions()->count());
        $this->assertNotNull($word->image_filename);
        $this->assertTrue(Storage::exists($this->image->getFullFileName($word->image_filename)));
        $this->image->delete($word->image_filename);
    }

    /** @test */
    public function two_users_can_have_the_same_word()
    {
        $this->add_word_with_definitions();
        $anotherUser = factory(User::class)->create();
        $this->actingAs($anotherUser);
        $this->add_word_with_definitions();

        $words = Word::withoutGlobalScope('currentUser')->get();
        $this->assertEquals(2, Word::withoutGlobalScope('currentUser')->count());
        $this->assertEquals($words[0]->slug, $words[1]->slug);
    }

    /** @test */
    public function two_similar_words_have_different_slugs()
    {
        $word = $this->createWordForUser(['word' => 'test word']);
        $anotherWord = $this->createWordForUser(['word' => 'test-word']);

        $this->assertFalse($word->slug == $anotherWord->slug);
    }
}
