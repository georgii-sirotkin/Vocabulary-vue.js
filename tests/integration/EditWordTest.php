<?php

use App\Definition;
use App\User;
use App\Word;

class EditWordTest extends WordTest
{
    /** @test */
    public function can_update_word_with_definitions_without_changing_anything()
    {
        $word = $this->createWordForUser();
        $definitions = factory(Definition::class, 3)->make()->all();
        $word->addDefinitionsWithoutTouch($definitions);

        $this->visit(route('edit_word', [$word->slug]))
            ->press('Save')
            ->seePageIs(route('words'));

        $this->assertEquals(1, Word::count());
        $this->seeInDatabase('words', ['id' => $word->id, 'word' => $word->word]);

        $this->assertEquals(3, Definition::count());
        foreach ($definitions as $definition) {
            $this->seeInDatabase('definitions', ['definition' => $definition->definition]);
            $this->dontSeeInDatabase('definitions', ['id' => $definition->id]);
        }
    }

    /** @test */
    public function can_remove_image_by_uploading_a_new_one()
    {
        $word = $this->createWordForUser(['image_filename' => 'test.jpg']);
        Storage::disk('public')->put($word->getImagePath(), 'data');

        $this->visit(route('edit_word', [$word->slug]))
            ->attach($this->getPathToTestFile('image.png'), 'image')
            ->press('Save')
            ->seePageIs(route('words'));

        $updatedWord = Word::first();
        $this->assertNotEquals($word->image_filename, $updatedWord->image_filename);
        $this->assertNotNull($updatedWord->image_filename);
        $this->assertFalse(Storage::disk('public')->exists($word->getImagePath()));
        $this->assertTrue(Storage::disk('public')->exists($updatedWord->getImagePath()));
        Storage::disk('public')->delete($updatedWord->getImagePath());
    }

    /** @test */
    public function image_is_kept()
    {
        $word = $this->createWordForUser(['image_filename' => 'test.jpg']);
        Storage::disk('public')->put($word->getImagePath(), 'data');

        $this->visit(route('edit_word', [$word->slug]))
            ->press('Save');

        $this->seePageIs(route('words'));
        $updatedWord = Word::first();
        $this->assertEquals($word->image_filename, $updatedWord->image_filename);
        $this->assertTrue(Storage::disk('public')->exists($updatedWord->getImagePath()));
        Storage::disk('public')->delete($updatedWord->getImagePath());
    }

    /** @test */
    public function slug_is_not_incremented_when_two_users_have_identical_words()
    {
        Word::unguard();
        $word = $this->createWordForUser(['word' => 'test', 'image_filename' => 'test.jpg']);
        $anotherUser = factory(User::class)->create();
        $this->actingAs($anotherUser);
        $theSameWord = factory(Word::class)->make(['word' => 'test', 'image_filename' => 'test2.jpg']);
        $anotherUser->addWord($theSameWord);
        $this->assertNotNull($word->slug);
        $this->assertEquals($word->slug, $theSameWord->slug);

        $this->call('PUT', route('update_word', [$word->slug]), ['word' => 'test', 'keepImage' => 'keepImage']);

        $this->assertRedirectedToRoute('words');
        $updatedWord = Word::first();
        $this->assertEquals($theSameWord->slug, $updatedWord->slug);
    }

    /** @test */
    public function cant_edit_other_users_word()
    {
        $word = $this->createWordForUser();
        $anotherUser = factory(User::class)->create();
        $this->actingAs($anotherUser);

        $this->call('PUT', route('update_word', [$word->slug]), ['word' => 'test', 'definitions' => ['definition']]);

        $this->assertResponseStatus(404);
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
