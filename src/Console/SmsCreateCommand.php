<?php

namespace Daijulong\LaravelSms\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class SmsCreateCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sms:create';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new SMS';

    /**
     * Build the class with the given name.
     *
     * @param  string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceAgentsMethods($stub)->replaceClass($stub, $name);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/sms.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Sms';
    }

    /**
     * Replace the agent methods for the given stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceAgentsMethods(&$stub)
    {
        $content = '';
        $agents = array_keys(config('sms.agents', []));
        if (!empty($agents)) {
            foreach ($agents as $agent) {
                $agent_name = ucfirst($agent);
                $content .= "\n" . <<<EOF
    /**
     * Content for agent : $agent_name
     *
     * return string
     */
    protected function agent$agent_name ()
    {
        return '';
    }
    
EOF;
            }
        }

        $stub = str_replace('DummyMethods', $content, $stub);

        return $this;
    }
}
