<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://vocabulary-vue.local';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Get path to test files.
     *
     * @param  string $filename
     * @return string
     */
    protected function getPathToTestFile($filename)
    {
        return base_path() . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'integration' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $filename;
    }
}
