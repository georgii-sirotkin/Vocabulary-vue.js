<?php

use App\Definition;
use App\User;
use App\Word;

class DeleteWordTest extends WordTest
{
    /** @test */
    public function can_delete_word()
    {
        $word = $this->createWordForUser(['image_filename' => 'test.jpg']);
        Storage::disk('public')->put($word->getImagePath(), 'data');
        $definitions = factory(Definition::class, 3)->make()->all();
        $word->addDefinitionsWithoutTouch($definitions);

        $this->call('DELETE', route('delete_word', [$word->slug]));

        $this->assertEquals(0, Word::count());
        $this->assertEquals(0, Definition::count());
        $this->assertFalse(Storage::disk('public')->exists($word->getImagePath()));
    }

    /** @test */
    public function cant_delete_other_users_word()
    {
        $word = $this->createWordForUser();
        $anotherUser = factory(User::class)->create();
        $this->actingAs($anotherUser);

        $this->call('DELETE', route('delete_word', [$word->slug]));

        $this->assertResponseStatus(404);
    }
}
