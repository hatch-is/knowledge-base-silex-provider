<?php

namespace KnowledgeBaseMcs;

use Silex\Application;
use Silex\ServiceProviderInreface;

/**
 * Class KnowledgeBaseSilexProvider
 *
 * @package KnowledgeBaseMcs
 */
class KnowledgeBaseSilexProvider implements ServiceProviderInreface
{
    public function register(Application $app)
    {
        $app['hatch-is.knowledge-base-mcs.processor'] = $app->share(
            function () use ($app) {
                return new Processor(
                    $app['hatch-is.knowledge-base-mcs.endpoint']
                );
            }
        );
    }

    public function boot(Application $app)
    {

    }
}
