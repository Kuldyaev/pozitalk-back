<?php

namespace App\Console\Commands\SystemCommands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:action')]
class MakeActionCommand extends GeneratorCommand
{
    protected $name = 'make:action';

    protected static $defaultName = 'make:action';

    protected $description = 'Create a new action class';

    protected $type = 'Action';

    public function handle(): void
    {
        parent::handle();
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/action.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Actions';
    }
}
