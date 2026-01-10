<?php

namespace Modules\Support\Concerns\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * @mixin Component
 */
trait HandlesRecords
{
    #[Locked()]
    public string $recordName;

    protected function prepRecordHandler(array $data): void
    {
        $this->recordName = $data['recordName'] ?? 'record';
    }

    public function add(): void
    {
        $eventName = "add-{$this->recordName}-action";
        $this->dispatch($eventName, recordName: $this->recordName);
    }

    public function edit(mixed $id): void
    {
        $eventName = "edit-{$this->recordName}-action";
        $this->dispatch($eventName, recordName: $this->recordName, recordId: $id);
    }

    public function remove(mixed $id): void
    {
        $eventName = "remove-{$this->recordName}-action";
        $this->dispatch($eventName, recordName: $this->recordName, recordId: $id);
    }
}
