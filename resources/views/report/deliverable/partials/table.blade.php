@php
$columns = [
    'client_name' => 'Client Name',
    'status_of_link' => 'Status of Link',
    'location_id' => 'Location ID',
    'area' => 'Area',
    'address' => 'Address',
    'circuit_id' => 'Circuit ID',
    'date_of_activation' => 'Date of Activation',
    'mode_of_delivery' => 'Mode of Delivery',
    'static_ip' => 'Static IP Address',
    'static_ip_subnet' => 'Static IP Subnet',
    'static_vlan_tag' => 'Static VLAN Tag',
    'network_ip' => 'Network IP',
    'gateway' => 'Gateway',
    'subnet_mask' => 'Subnet Mask',
    'usable_ips' => 'Usable IPs',
];
@endphp

<div class="container-fluid py-4">
    <div class="card shadow border-0">

<h5 class="mb-0 mt-2 bg-primary text-white py-2 px-3">
                <i class="bi bi-table me-2"></i>

                @if($type === 'open')
                    Open Deliverables
                @elseif($type === 'inprogress')
                    In Progress Deliverables
                @elseif($type === 'delivery')
                    Delivered Deliverables
                @else
                    Deliverables
                @endif
            </h5>
            
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
                <label for="entriesSelect" class="mb-0 fw-semibold">Show</label>
                <form id="filterForm" method="GET" class="d-flex align-items-center gap-2">

    {{-- Per Page --}}
    <select name="per_page"
            class="form-select form-select-sm w-auto"
            onchange="this.form.submit()">
        <option value="10" {{ request('per_page',10) == 10 ? 'selected' : '' }}>10</option>
        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
    </select>

    {{-- Date Filter --}}
    <select name="date_filter"
            class="form-select form-select-sm w-auto"
            onchange="this.form.submit()">
        <option value="">All</option>
        <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>
            This Month
        </option>
        <option value="quarter" {{ request('date_filter') == 'quarter' ? 'selected' : '' }}>
            This Quarter
        </option>
        <option value="half" {{ request('date_filter') == 'half' ? 'selected' : '' }}>
            Half Yearly
        </option>
    </select>

</form>
            </div>
            <form id="searchForm" method="GET" class="ms-auto">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-lg rounded-pill px-4" style="min-width: 250px;" placeholder="Search..." id="searchBox">
            </form>
            <button id="downloadExcelBtn"
                    class="btn btn-success d-none"
                    onclick="downloadSelectedExcel()">
                <i class="bi bi-download me-1"></i> Download Excel
            </button>
        </div>

        {{-- BODY --}}
        <div class="card-body">
            @if($records->count() > 0)

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="deliverableTable">
                        <thead class="table-dark">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="select_all">
                                </th>
                                <th width="50">S.No</th>

                                @foreach($columns as $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($records as $index => $record)
                                <tr>
                                    <td>
                                        <input type="checkbox"
                                               class="row-checkbox"
                                               value="{{ $record->id }}">
                                    </td>

                                    <td>
                                        {{ ($records->firstItem() ?? 1) + $index }}
                                    </td>

                                    <td>{{ $record->feasibility->client->client_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($record->deliverablePlans->count())
                                            {{ $record->deliverablePlans->pluck('status_of_link')->implode(', ') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $record->feasibility->location_id ?? 'N/A' }}</td>
                                    <td>{{ $record->feasibility->area ?? 'N/A' }}</td>
                                    <td>{{ $record->feasibility->address ?? 'N/A' }}</td>
                                    <td>
                                        @if($record->deliverablePlans->count())
                                            {{ $record->deliverablePlans->pluck('circuit_id')->implode(', ') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->deliverablePlans->count())
                                            {{ $record->deliverablePlans->pluck('date_of_activation')->map(fn($d) => $d ? \Carbon\Carbon::parse($d)->format('Y-m-d') : 'N/A')->implode(', ') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->deliverablePlans->count())
                                            {{ $record->deliverablePlans->pluck('mode_of_delivery')->implode(', ') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->deliverablePlans->count())
                                            @php $val = $record->deliverablePlans->pluck('static_ip_address')->filter()->implode(', '); @endphp
                                            {{ $val !== '' ? $val : '-' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->deliverablePlans->count())
                                            @php $val = $record->deliverablePlans->pluck('static_subnet_mask')->filter()->implode(', '); @endphp
                                            {{ $val !== '' ? $val : '-' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->deliverablePlans->count())
                                            @php $val = $record->deliverablePlans->pluck('static_vlan')->filter()->implode(', '); @endphp
                                            {{ $val !== '' ? $val : '-' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->deliverablePlans->count())
                                            @php $val = $record->deliverablePlans->pluck('network_ip')->filter()->implode(', '); @endphp
                                            {{ $val !== '' ? $val : '-' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->deliverablePlans->count())
                                            @php $val = $record->deliverablePlans->pluck('static_gateway')->filter()->implode(', '); @endphp
                                            {{ $val !== '' ? $val : '-' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->deliverablePlans->count())
                                            @php $val = $record->deliverablePlans->pluck('static_subnet_mask')->filter()->implode(', '); @endphp
                                            {{ $val !== '' ? $val : '-' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->deliverablePlans->count())
                                            @php $val = $record->deliverablePlans->pluck('usable_ips')->filter()->implode(', '); @endphp
                                            {{ $val !== '' ? $val : '-' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No Deliverables Found</h5>
                </div>
            @endif
        </div>

        {{-- FOOTER --}}
        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap px-3 pb-3">
            <div class="text-muted small">
                @if(method_exists($records, 'firstItem'))
                    Showing {{ $records->firstItem() ?? 0 }}
                    to {{ $records->lastItem() ?? 0 }}
                    of {{ number_format($records->total()) }} entries
                @else
                    Showing {{ $records->count() ? 1 : 0 }}
                    to {{ $records->count() }}
                    of {{ number_format($records->count()) }} entries
                @endif
            </div>

            <div class="ms-auto">
                @if(method_exists($records, 'links'))
                    {{ $records->links() }}
                @endif
            </div>
        </div>

    </div>
</div>

{{-- SCRIPTS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const selectAll = document.getElementById('select_all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const downloadBtn = document.getElementById('downloadExcelBtn');

    function updateDownloadBtn() {
        const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
        downloadBtn.classList.toggle('d-none', !anyChecked);
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateDownloadBtn();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const allChecked = Array.from(rowCheckboxes).every(x => x.checked);
            const noneChecked = Array.from(rowCheckboxes).every(x => !x.checked);

            if (selectAll) {
                selectAll.checked = allChecked;
                selectAll.indeterminate = !allChecked && !noneChecked;
            }

            updateDownloadBtn();
        });
    });

});

function downloadSelectedExcel() {

    const checked = Array.from(
        document.querySelectorAll('.row-checkbox:checked')
    ).map(cb => cb.value);

    if (checked.length === 0) return;

    const url = `{{ route('report.deliverable.downloadExcel') }}`;
    const form = document.createElement('form');

    form.method = 'POST';
    form.action = url;
    form.target = '_blank';

    form.innerHTML = `
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    `;

    checked.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>