<?php

namespace Daijulong\LaravelSms\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class SmsAgentCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sms:agent';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:agent {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new agent';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/agent.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return trim(config('sms.agent_ext_namespace', $rootNamespace . '\Sms\Agents'), '\\');
    }

}
