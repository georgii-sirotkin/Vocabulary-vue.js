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
        $this->seeInDatabase('word', ['id' => $word->id, 'word' => $word->word]);

        $this->assertEquals(3, Definition::count());
        foreach ($definitions as $definition) {
            $this->seeInDatabase('definition', ['id' => $definition->id, 'definition' => $definition->definition]);
        }

    }

    /** @test */
    public function can_update_word_and_definitions()
    {
        $word = $this->createWordForUser(['image_filename' => null]);
        $definitions = factory(Definition::class, 3)->make()->all();
        $word->addDefinitionsWithoutTouch($definitions);

        $data = [];
        $data['word'] = 'word changed';
        $definitions[0]->definition = 'definition updated';
        $data['definitions'][] = $definitions[0]->definition;
        $data['definitionIds'][] = $definitions[0]->id;
        $data['definitions'][] = $definitions[1]->definition;
        $data['definitionIds'][] = $definitions[1]->id;

        $data['definitions'][] = 'new definition';
        $data['definitionIds'][] = '';

        $this->call('PUT', route('update_word', [$word->slug]), $data);

        $this->assertRedirectedToRoute('words');
        $this->assertEquals(1, Word::count());
        $this->seeInDatabase('word', ['id' => $word->id, 'word' => 'word changed']);

        $this->assertEquals(3, Definition::count());
        $this->seeInDatabase('definition', ['id' => $definitions[0]->id, 'definition' => 'definition updated']);
        $this->seeInDatabase('definition', ['id' => $definitions[1]->id, 'definition' => $definitions[1]->definition]);
        $this->seeInDatabase('definition', ['definition' => 'new definition']);
        $this->dontSeeInDatabase('definition', ['id' => $definitions[2]->id]);
        $this->dontSeeInDatabase('definition', ['definition' => $definitions[2]->definition]);
    }

    /** @test */
    public function can_remove_image_by_uploading_a_new_one()
    {
        Storage::put($this->image->getFullFileName('test.jpg'), 'data');
        $word = $this->createWordForUser(['image_filename' => 'test.jpg']);

        $this->visit(route('edit_word', [$word->slug]))
            ->attach($this->getPathToTestFile('image.png'), 'image')
            ->press('Save')
            ->seePageIs(route('words'));

        $updatedWord = Word::first();
        $this->assertNotEquals($word->image_filename, $updatedWord->image_filename);
        $this->assertNotNull($updatedWord->image_filename);
        $this->assertFalse(Storage::exists($this->image->getFullFileName($word->image_filename)));
        $this->assertTrue(Storage::exists($this->image->getFullFileName($updatedWord->image_filename)));
        $this->image->delete($updatedWord->image_filename);
    }

    /** @test */
    public function image_is_kept()
    {
        Storage::put($this->image->getFullFileName('test.jpg'), 'data');
        $word = $this->createWordForUser(['image_filename' => 'test.jpg']);

        $this->visit(route('edit_word', [$word->slug]))
            ->press('Save');

        $this->seePageIs(route('words'));
        $updatedWord = Word::first();
        $this->assertEquals($word->image_filename, $updatedWord->image_filename);
        $this->assertTrue(Storage::exists($this->image->getFullFileName($updatedWord->image_filename)));
        $this->image->delete($word->image_filename);
    }

    /** @test */
    public function can_remove_image_without_uploading_a_new_one()
    {
        Storage::put($this->image->getFullFileName('test.jpg'), 'data');
        $word = $this->createWordForUser(['image_filename' => 'test.jpg']);
        $definitions = factory(Definition::class, 3)->make();
        $word->addDefinitionsWithoutTouch($definitions->all());

        $data = [];
        $data['word'] = $word->word;
        $data['definitionIds'] = $definitions->pluck('id')->all();
        $data['definitions'] = $definitions->pluck('definition')->all();
        $this->call('PUT', route('update_word', [$word->slug]), $data);

        $this->assertRedirectedToRoute('words');
        $updatedWord = Word::first();
        $this->assertNull($updatedWord->image_filename);
        $this->assertFalse(Storage::exists($this->image->getFullFileName($word->image_filename)));
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

        $this->call('PUT', route('update_word', [$word->slug]), []);

        $this->assertResponseStatus(404);
    }
}
