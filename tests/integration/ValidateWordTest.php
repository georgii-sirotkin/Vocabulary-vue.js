<?php

class ValidateWordTest extends WordTest
{
    /** @test */
    public function word_is_required()
    {
        $this->visit(route('add_word'))
            ->press('Save')
            ->see('The title field is required.');
    }

    /** @test */
    public function just_word_is_not_enough()
    {
        $this->visit(route('add_word'))
            ->type('test', 'title')
            ->press('Save')
            ->seePageIs(route('add_word'));
    }

    /** @test */
    public function rejects_not_supported_mime_type()
    {
        $this->visit(route('add_word'))
            ->type('test', 'title')
            ->attach($this->getPathToTestFile('text_file.txt'), 'image')
            ->press('Save')
            ->seePageIs(route('add_word'));

    }

    /** @test */
    public function rejects_very_large_image()
    {
        $this->visit(route('add_word'))
            ->type('test', 'title')
            ->attach($this->getPathToTestFile('large_image.jpg'), 'image')
            ->press('Save')
            ->seePageIs(route('add_word'))
            ->see('too large');
    }

    /** @test */
    public function rejects_invalid_image_url()
    {
        $this->visit(route('add_word'))
            ->type('test', 'title')
            ->type('image.png', 'imageUrl')
            ->press('Save')
            ->seePageIs(route('add_word'));
    }

    /** @test */
    public function rejects_not_image_url()
    {
        $this->visit(route('add_word'))
            ->type('test', 'title')
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
        $response = $this->call('POST', route('insert_word'), ['title' => $word->title, 'definitions' => ['test definition', 'another definition']]);
        $this->assertRedirectedToRoute('add_word');
        $this->visit(route('add_word'))
            ->see('The title has already been taken.');
    }
}
