<?php

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WordValidationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $user = factory(User::class)->create();
        $this->actingAs($user);
    }

    /** @test */
    public function word_is_required()
    {
        $this->visit(route('add_word'))
            ->press('Add word')
            ->see('The word field is required.');
    }

    /** @test */
    public function just_word_is_not_enough()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->press('Add word')
            ->seePageIs(route('add_word'));
    }

    /** @test */
    public function passes_when_word_and_definition_are_given()
    {
        $response = $this->call('POST', route('insert_word'), ['word' => 'test', 'definitions' => ['test definition']]);
        $this->seePageIs(route('words'));
    }

    /** @test */
    public function rejects_not_supported_mime_type()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->attach($this->getPathToTestFile('text_file.txt'), 'image')
            ->press('Add word')
            ->seePageIs(route('add_word'));

    }

    /** @test */
    public function accepts_png()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->attach($this->getPathToTestFile('image.png'), 'image')
            ->press('Add word')
            ->seePageIs(route('words'));
    }

    /** @test */
    public function accepts_jpg()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->attach($this->getPathToTestFile('image.jpg'), 'image')
            ->press('Add word')
            ->seePageIs(route('words'));
    }

    /** @test */
    public function rejects_very_large_image()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->attach($this->getPathToTestFile('large_image.jpg'), 'image')
            ->press('Add word')
            ->seePageIs(route('add_word'))
            ->see('too large');
    }

    /** @test */
    public function rejects_invalid_image_url()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->type('image.png', 'imageUrl')
            ->press('Add word')
            ->seePageIs(route('add_word'));
    }

    /** @test */
    public function rejects_not_images_url()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->type('http://www.google.co.uk/robots.txt', 'imageUrl')
            ->press('Add word')
            ->seePageIs(route('add_word'))
            ->see('Unable to get image.');
    }

    /** @test */
    public function accepts_png_image_url()
    {
        $this->visit(route('add_word'))
            ->type('test', 'word')
            ->type('https://www.google.co.uk/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png', 'imageUrl')
            ->press('Add word')
            ->seePageIs(route('words'));
    }

    /** @test */
    public function two_users_can_have_the_same_word()
    {

    }

    private function getPathToTestFile($filename)
    {
        return base_path() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'integration' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $filename;
    }
}
