<?php

use App\Definition;
use App\Word;

class DeleteWordTest extends WordTest
{
    /** @test */
    public function can_delete_word()
    {
        Storage::put($this->image->getFullFileName('test.jpg'), 'data');
        $word = $this->createWordForUser(['image_filename' => 'test.jpg']);
        $definitions = factory(Definition::class, 3)->make()->all();
        $word->addDefinitionsWithoutTouch($definitions);

        $this->call('DELETE', route('delete_word', [$word->slug]));
        $this->assertEquals(0, Word::count());
        $this->assertEquals(0, Definition::count());
        $this->assertFalse(Storage::exists($this->image->getFullFileName($word->image_filename)));
    }
}
