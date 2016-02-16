<?php

use App\Word;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WordTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function updated_at_column_changes_when_definition_is_added()
    {
        $user = factory(App\User::class)->create();
        $word = factory(App\Word::class)->make(['word' => 'initial_word']);
        $user->words()->save($word);
        $initialUpdatedAt = $word->updated_at;
        $initialCreatedAt = $word->created_at;
        sleep(1);
        $word->save(['word' => 'initial_word']);
        $word->definitions()->save(factory(App\Definition::class)->make());
        $wordChanged = Word::find($word->id);
        $this->assertTrue($initialUpdatedAt->timestamp < $wordChanged->updated_at->timestamp);
        $this->assertEquals($initialCreatedAt->timestamp, $wordChanged->created_at->timestamp);
    }
}
