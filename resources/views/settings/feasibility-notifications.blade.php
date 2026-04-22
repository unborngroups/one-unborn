@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Feasibility Notification Settings</h3>
    <form method="POST" action="{{ route('settings.feasibility-notifications.update') }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="open_user_type" class="form-label">User Type for Open Status</label>
            <select name="open_user_type" id="open_user_type" class="form-select">
                @foreach($userTypes as $type)
                    <option value="{{ $type->name }}" {{ (old('open_user_type', $config['Open'] ?? '') == $type->name) ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="closed_user_type" class="form-label">User Type for Closed Status</label>
            <select name="closed_user_type" id="closed_user_type" class="form-select">
                @foreach($userTypes as $type)
                    <option value="{{ $type->name }}" {{ (old('closed_user_type', $config['Closed'] ?? '') == $type->name) ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>
@endsection
