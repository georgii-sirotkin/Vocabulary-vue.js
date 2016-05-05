<?php

class ValidateWordTest extends WordTest
{
    /** @test */
    public function word_is_required()
    {
        $this->visit(route('add_word'))
            ->press('Save')
            ->see('The word field is required.');
    }

    /** @test */
    public function just_word_is_not_enough()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->press('Save')
            ->seePageIs(route('add_word'));
    }

    /** @test */
    public function rejects_not_supported_mime_type()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->attach($this->getPathToTestFile('text_file.txt'), 'image')
            ->press('Save')
            ->seePageIs(route('add_word'));

    }

    /** @test */
    public function rejects_very_large_image()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->attach($this->getPathToTestFile('large_image.jpg'), 'image')
            ->press('Save')
            ->seePageIs(route('add_word'))
            ->see('too large');
    }

    /** @test */
    public function rejects_invalid_image_url()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->type('image.png', 'imageUrl')
            ->press('Save')
            ->seePageIs(route('add_word'));
    }

    /** @test */
    public function rejects_not_image_url()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->type('http://www.google.co.uk/robots.txt', 'imageUrl')
            ->press('Save')
            ->seePageIs(route('add_word'))
            ->see('Unable to get image.');
    }

    /** @test */
    public function rejects_duplicate_word_for_the_same_user()
    {
        $word = $this->createWordForUser();
        $this->visit(route('add_word'));
        $response = $this->call('POST', route('insert_word'), ['word' => $word->word, 'definitions' => ['test definition', 'another definition']]);
        $this->assertRedirectedToRoute('add_word');
        $this->visit(route('add_word'))
            ->see('You have already added this word.');
    }
}
