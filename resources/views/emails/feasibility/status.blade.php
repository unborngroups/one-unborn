{{-- Feasibility Completed Email --}}
@if(isset($templateContent) && $status === 'Closed' && $templateContent)
    {!! $templateContent !!}
@else
    <p>Feasibility Status: {{ $status }}</p>
    <p>Feasibility ID: {{ $feasibility->feasibility_request_id ?? '' }}</p>
    <p>Action By: {{ $actionBy->name ?? '' }}</p>
    <p>Previous Status: {{ $previousStatus ?? '-' }}</p>
@endif
