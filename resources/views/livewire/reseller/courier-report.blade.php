<div id="courier-report" class="shadow-sm card rounded-0 no-print" wire:init="load">
    <div class="p-3 card-header">
        <h5 class="mb-0 card-title">Courier Report</h5>
    </div>
    <div class="p-0 card-body">
        <div wire:loading wire:target="load" class="py-4 text-center text-muted">Loading courier report...</div>
        @if ($loaded)
            @if (is_string($report))
                <div class="alert alert-danger">{{ $report }}</div>
                <div class="alert alert-danger">Please wait 5 minutes</div>
            @else
                <div class="flex-wrap d-flex" style="column-gap: 1rem;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Courier</th>
                                    <th>Total</th>
                                    <th class="bg-success">Delivered</th>
                                    <th class="bg-danger">Failed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (['Pathao', 'SteadFast', 'RedX', 'PaperFly'] as $provider)
                                    @php($providerReport = data_get($report, 'courierData.' . strtolower($provider)))
                                    <tr>
                                        <th>{{ $provider }}</th>
                                        <td class="font-weight-bold">{{ data_get($providerReport, 'total_parcel', 0) }}</td>
                                        <td class="font-weight-bold bg-success">
                                            {{ data_get($providerReport, 'success_parcel', 0) }}</td>
                                        <td class="font-weight-bold bg-danger">
                                            {{ data_get($providerReport, 'cancelled_parcel', 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="flex: 1;display: flex;flex-direction: column;justify-content: center;"
                        class="p-2 border font-weight-bold">
                        @php($summary = data_get($report, 'courierData.summary'))
                        @php($failure = data_get($summary, 'total_parcel', 0) > 0 ? number_format((data_get($summary, 'cancelled_parcel', 0) / data_get($summary, 'total_parcel', 1)) * 100, 2) : 0)
                        <div class="px-3 py-1 my-1 text-center border border-secondary">Summary:</div>
                        <div class="px-3 py-2 my-1 bg-success">Delivered: {{ data_get($summary, 'success_parcel', 0) }}
                            ({{ data_get($summary, 'success_ratio', 0) }}%)</div>
                        <div class="px-3 py-2 my-1 bg-danger">Failed: {{ data_get($summary, 'cancelled_parcel', 0) }}
                            ({{ $failure }}%)</div>
                        <div class="d-flex">
                            <div class="px-1 py-2 my-1 text-center bg-success text-nowrap w-100"
                                @if (round(data_get($summary, 'success_ratio', 0)) > 0) style="width: {{ data_get($summary, 'success_ratio', 0) }}% !important;" @endif
                                title="Success Rate: {{ data_get($summary, 'success_ratio', 0) }}%">
                                {{ data_get($summary, 'success_ratio', 0) }}%</div>
                            <div class="px-1 py-2 my-1 text-center bg-danger text-nowrap w-100"
                                @if (round($failure) > 0) style="width: {{ $failure }}% !important;" @endif
                                title="Failure Rate: {{ $failure }}%">{{ $failure }}%</div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="py-4 text-center text-muted" wire:loading.remove wire:target="load">Loading courier report...</div>
        @endif
    </div>
</div>
