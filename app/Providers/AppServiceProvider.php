<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\SupportStrategy\InstanceOfSupportStrategy;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Registry::class, function () {
            $registry = new Registry();

            foreach (config('workflow') as $name => $workflowConfig) {
                $transitions = [];
                foreach ($workflowConfig['transitions'] as $transitionName => $transition) {
                    $transitions[] = new Transition($transitionName, $transition['from'], $transition['to']);
                }
                $definition = new Definition(
                    $workflowConfig['places'],
                    $transitions,
                    $workflowConfig['initial_marking']
                );
                $type = $workflowConfig['type'];
                $markingStore = $workflowConfig['marking_store'];
                $workflow = new Workflow(
                    $definition,
                    new MethodMarkingStore($type === 'state_machine', $markingStore['property']),
                    null,
                    $name
                );
                foreach ($workflowConfig['supports'] as $support) {
                    $registry->addWorkflow($workflow, new InstanceOfSupportStrategy($support));
                }
            }

            return $registry;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
