@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
	<h4 class="text-primary fw-bold mb-3">Add Termination Request</h4>
	<div class="card shadow border-0 p-4">
		<form action="{{ route('operations.termination.store') }}" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="row mb-3">
				<div class="col-md-4">
					<label class="form-label fw-semibold">Circuit ID <span class="text-danger">*</span></label>
					<select name="deliverable_id" id="deliverable_id" class="form-select select2-tags" required>
					<option value="">Select Circuit ID</option>
					@foreach($deliverables_plans as $d)
						<option value="{{ $d['deliverable_id'] }}"
							data-company_name="{{ $d['company_name'] ?? '' }}"
							data-address="{{ $d['address'] ?? '' }}"
							data-bandwidth="{{ $d['bandwidth'] ?? '' }}"
							data-asset_id="{{ $d['asset_id'] ?? '' }}"
							data-asset_mac="{{ $d['asset_mac'] ?? '' }}"
							data-asset_serial="{{ $d['asset_serial'] ?? '' }}"
							data-date_of_activation="{{ $d['date_of_activation'] ?? '' }}"
							data-date_of_delivered="{{ $d['date_of_delivered'] ?? '' }}"
							data-date_of_last_renewal="{{ $d['date_of_last_renewal'] ?? '' }}"
							data-date_of_expiry="{{ $d['date_of_expiry'] ?? '' }}"
						>
							{{ $d['circuit_id'] }}
						</option>
					@endforeach
				
                </select>
				</div>
			</div>
			<!-- This autofill path based in circuit ID selection in deliverable_id -->
			<div id="autofill_fields">
				<div class="row g-3">
					<div class="col-md-4">
						<label>Company Name</label>
						<input type="text" name="company_name" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Address</label>
						<input type="text" name="address" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Bandwidth</label>
						<input type="text" name="bandwidth" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Asset ID</label>
						<input type="text" name="asset_id" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Asset MAC</label>
						<input type="text" name="asset_mac" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Asset Serial</label>
						<input type="text" name="asset_serial" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Date of Activation</label>
						<input type="text" name="date_of_activation" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Date of Delivered</label>
						<input type="text" name="date_of_delivered" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Date of Last Renewal</label>
						<input type="text" name="date_of_last_renewal" class="form-control" readonly>
					</div>
					<div class="col-md-4">
						<label>Date of Expiry</label>
						<input type="text" name="date_of_expiry" class="form-control" readonly>
					</div>
				</div>
			</div>
			<hr>
			<!-- This all manual input fields -->
			<div class="row g-3">
				<div class="col-md-4">
					<label>Termination Request Date</label>
					<input type="date" name="termination_request_date" class="form-control" required>
				</div>
				<div class="col-md-4">
					<label>Termination Requested By</label>
					<input type="text" name="termination_requested_by" class="form-control" required>
				</div>
				<div class="col-md-4">
					<label>Termination Request Document</label>
					
					<input type="file" name="termination_request_document" class="form-control" accept=".pdf,.png,.jpg,.jpeg">
				</div>
				<div class="col-md-4">
					<label>Termination Date</label>
					<input type="date" name="termination_date" class="form-control">
				</div>
			</div>
			<div class="mt-4 text-end">
				<button type="submit" class="btn btn-primary">Submit</button>
			</div>
		</form>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
	$('#deliverable_id').on('change', function () {
		let selected = $(this).find(':selected');
		$('[name="company_name"]').val(selected.data('company_name') || '');
		$('[name="address"]').val(selected.data('address') || '');
		$('[name="bandwidth"]').val(selected.data('bandwidth') || '');
		$('[name="asset_id"]').val(selected.data('asset_id') || '');
		$('[name="asset_mac"]').val(selected.data('asset_mac') || '');
		$('[name="asset_serial"]').val(selected.data('asset_serial') || '');
		$('[name="date_of_activation"]').val(selected.data('date_of_activation') || '');
		$('[name="date_of_expiry"]').val(selected.data('date_of_expiry') || '');
		$('[name="date_of_last_renewal"]').val(selected.data('date_of_last_renewal') || '');
		$('[name="date_of_expiry"]').val(selected.data('date_of_expiry') || '');
	});
});
</script>





@endsection