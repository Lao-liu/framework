<?php
namespace Clarity\Kernel;

use Dotenv\Dotenv;
use Phalcon\Config;
use Phalcon\Di\FactoryDefault;
use Clarity\Services\Service\ServiceContainer;

trait KernelTrait
{
    protected function loadFactory()
    {
        $this->di = new FactoryDefault;

        return $this;
    }

    protected function loadConfig()
    {
        # let's create an empty config with just an empty
        # array, this is just for us to prepare the config

        $this->di->set('config', function() {
            return new Config([]);
        }, true);


        # get the paths and merge the array values to the
        # empty config as we instantiated above

        config()->merge(
            new Config([
                'path' => $this->path
            ])
        );


        # now merge the assigned environment

        config()->merge(
            new Config([
                'environment' => $this->getEnvironment()
            ])
        );


        # iterate all the base config files and require
        # the files to return an array values

        $base_config_files = iterate_require(
            folder_files($this->path['config'])
        );


        # iterate all the environment config files and
        # process the same thing as the base config files

        $env_config_files = iterate_require(
            folder_files(
                url_trimmer(
                    $this->path['config'].'/'.$this->getEnvironment()
                )
            )
        );


        # merge the base config files and the environment
        # config files as one in the our DI 'config'

        config()->merge( new Config($base_config_files) );
        config()->merge( new Config($env_config_files) );
    }

    protected function loadTimeZone()
    {
        date_default_timezone_set(config()->app->timezone);
    }

    protected function loadServices($after_module = false)
    {
        # load all the service providers, providing our
        # native phalcon classes

        $container = new ServiceContainer;

        foreach (config()->app->services as $provider) {

            $instance = new $provider;

            if ( $instance->getAfterModule() == $after_module ) {
                $container->addServiceProvider($instance);
            }
        }

        $container->boot();
    }

}
