<?php

use App\User;
use App\Word;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WordTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $image;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
        $this->image = app('App\Services\ImageService');
    }

    protected function createWordForUser(array $data = array())
    {
        return $this->user->words()->save(factory(Word::class)->make($data));
    }

    /** @test */
    public function updated_at_column_changes_when_definition_is_added()
    {
        $word = $this->createWordForUser(['word' => 'initial_word']);
        $initialUpdatedAt = $word->updated_at;
        $initialCreatedAt = $word->created_at;
        sleep(1);
        $word->save(['word' => 'initial_word']);
        $word->definitions()->save(factory(App\Definition::class)->make());
        $wordChanged = Word::find($word->id);
        $this->assertTrue($initialUpdatedAt->timestamp <
            $wordChanged->updated_at->timestamp);
        $this->assertEquals($initialCreatedAt->timestamp, $wordChanged->created_at->timestamp);
    }
}
