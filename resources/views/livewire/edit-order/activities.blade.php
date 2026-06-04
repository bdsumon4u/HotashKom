<?php
function editOrderActivityData($data)
{
    if (isset($data['data'])) {
        $data = array_merge($data, $data['data']);
        unset($data['data']);
    }

    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
?>
<div class="shadow-sm card rounded-0" wire:init="load">
    <div class="p-3 card-header">
        <h5 class="mb-0 card-title">Activities</h5>
    </div>
    <div class="p-3 card-body">
        <div wire:loading wire:target="load" class="py-4 text-center text-muted">Loading activities...</div>
        @if ($loaded)
            <div id="accordion-{{ $this->getId() }}">
                @foreach ($activities as $activity)
                    <div class="mb-1 shadow-sm card rounded-0">
                        <div class="px-3 py-2 card-header" id="heading{{ $activity->id }}">
                            <a class="text-dark" data-toggle="collapse"
                                href="#collapse-{{ $this->getId() }}-{{ $activity->id }}">
                                <div class="pb-1 mb-1 border-bottom text-primary">{{ $activity->description }}
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div><i
                                            class="mr-1 fa fa-user"></i>{{ $activity->causer->name ?? 'System' }}
                                    </div>
                                    <div><i
                                            class="mr-1 fa fa-clock-o"></i>{{ $activity->created_at->format('d-M-Y h:i A') }}
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div id="collapse-{{ $this->getId() }}-{{ $activity->id }}" class="collapse" data-parent="#accordion-{{ $this->getId() }}">
                            <div class="p-3 card-body">
                                <table class="table table-responsive">
                                    <tbody>
                                        @if ($activity->changes['old'] ?? false)
                                            <tr>
                                                <th class="text-center">OLD</th>
                                                <th class="text-center">NEW</th>
                                            </tr>
                                        @endif
                                        <tr>
                                            @if ($activity->changes['old'] ?? false)
                                                <td>
                                                    <pre><div class="language-php">{{ editOrderActivityData($activity->changes['old'] ?? []) }}</div></pre>
                                                </td>
                                            @endif
                                            <td>
                                                <pre><div class="language-php">{{ editOrderActivityData($activity->changes['attributes'] ?? []) }}</div></pre>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-4 text-center text-muted" wire:loading.remove wire:target="load">Loading activities...</div>
        @endif
    </div>
</div>
