@extends('client_portal.layout')

@section('content')
<div class="container mt-3">
    <h4 class="fw-bold">Notification Settings</h4>

    <form method="post" action="{{ route('client.notifications.settings.update') }}">
        @csrf

        <div class="row mt-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Email Alerts</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="notify_sla_breach" {{ $settings->notify_sla_breach ? 'checked' : '' }}>
                    <label class="form-check-label">SLA Breach Alerts</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="notify_link_down" {{ $settings->notify_link_down ? 'checked' : '' }}>
                    <label class="form-check-label">Link Down Alerts</label>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Thresholds</label>
                <input class="form-control mb-2" type="number" name="latency_threshold" value="{{ $settings->latency_threshold }}" placeholder="Latency Threshold (ms)">
                <input class="form-control mb-2" type="number" name="packet_loss_threshold" value="{{ $settings->packet_loss_threshold }}" placeholder="Packet Loss (%)">
            </div>

            <div class="col-md-12 mt-3">
                <label class="form-label fw-bold">Additional Recipients</label>
                <input class="form-control" type="text" name="extra_recipients" value="{{ $settings->extra_recipients }}" placeholder="Email1, Email2, Email3">
            </div>
        </div>

        <button class="btn btn-primary mt-3">Save</button>
    </form>
</div>
@endsection
