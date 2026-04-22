@extends('layouts.app')



@section('content')

<div class="container py-4">

    <h3 class="fw-bold text-primary mb-3">Create Email Template</h3>



    <div class="card shadow border-0 p-4">

        <form action="{{ route('emails.store') }}" method="POST">

            @csrf



            {{-- Company Select --}}

            <div class="mb-3">

                <label class="form-label">Company</label>

                <select name="company_id" class="form-control" required>

                    <option value="">-- Select Company --</option>

                    @foreach($companies as $company)

                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>

                            {{ $company->company_name }}

                        </option>

                    @endforeach

                </select>

                @error('company_id') <span class="text-danger small">{{ $message }}</span> @enderror

            </div>



            {{-- Subject --}}

            <div class="mb-3">

                <label class="form-label">Subject</label>

                <input type="text" name="subject" class="form-control" 

                       value="{{ old('subject') }}" required>

                @error('subject') <span class="text-danger small">{{ $message }}</span> @enderror

            </div>



            {{-- Body --}}


            <div class="mb-3">

                <label class="form-label">Body</label>

                <textarea name="body" id="email_body" class="form-control" rows="8" required>{{ old('body') }}</textarea>

                <small class="text-muted">

    You can use placeholders like 

    <code>@{{name}}</code>, 

    <code>@{{company_name}}</code>,

    <code>@{{email}}</code>, 

    <code>@{{joining_date}}</code>.

</small>

                @error('body') <span class="text-danger small">{{ $message }}</span> @enderror

            </div>



            {{-- Event Key --}}
            <div class="mb-3">
                <label class="form-label">Event Key</label>
                <input type="text" name="event_key" class="form-control" value="{{ old('event_key') }}" required>
                <small class="text-muted">Unique key for this event (e.g. user_registered, po_approved)</small>
                @error('event_key') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            {{-- Buttons --}}

            <button type="submit" class="btn btn-success">Save Template</button>

            <a href="{{ route('emails.index') }}" class="btn btn-secondary">Cancel</a>

        </form>

    </div>

</div>
@endsection

@section('scripts')
<script>
$(function () {
    $('#email_body').summernote({
        height: 250,
        toolbar: [
            ['font', ['fontname', 'fontsize']],
            ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['fontstyle', ['superscript', 'subscript']],
            ['color', ['color', 'backcolor']],
            ['para', ['ul', 'ol', 'paragraph', 'lineheight']],
            ['insert', ['picture', 'link', 'table', 'hr']],
            ['view', ['fullscreen', 'codeview', 'undo', 'redo']]
        ],
        fontNames: ['Source Sans Pro', 'Arial', 'Times New Roman'],
        placeholder: 'Draft your email body here…'
    });
});
</script>
@endsection