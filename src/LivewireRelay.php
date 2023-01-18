<?php declare(strict_types=1);

namespace MAS\Toaster;

use Livewire\Component;
use Livewire\LivewireManager;
use Livewire\Response;

/** @internal */
final class LivewireRelay
{
    public const EVENT = 'toaster:received';

    public function __construct(
        private readonly LivewireManager $livewire,
        private readonly Collector $toasts,
    ) {}

    public function __invoke(Component $component, Response $response): Response
    {
        if (! $this->livewire->isLivewireRequest()) {
            return $response;
        }

        if ($component->redirectTo !== null) {
            return $response;
        }

        if ($toasts = $this->toasts->flush()) {
            $response->effects['dispatches'] ??= [];

            foreach ($toasts as $toast) {
                $response->effects['dispatches'][] = ['event' => self::EVENT, 'data' => $toast->toArray()];
            }
        }

        return $response;
    }
}
