<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Deliverables;
use App\Models\Feasibility;

// Run: php artisan tinker < update_circuit_ids.php

$deliverables = Deliverables::whereNull('circuit_id')->orWhere('circuit_id', '')->get();

foreach ($deliverables as $deliverable) {
    $feasibility = $deliverable->feasibility;
    $companyShort = strtoupper(substr(str_replace(' ', '', $feasibility->company->company_name ?? 'CMP'), 0, 3));
    $stateShort = strtoupper(substr(str_replace(' ', '', $feasibility->state ?? 'Unknown'), 0, 3));
    $countryShort = strtoupper(substr(str_replace(' ', '', $feasibility->country ?? 'IN'), 0, 2));
    $nextNumber = str_pad($deliverable->id, 4, '0', STR_PAD_LEFT);
    $circuitID = date('y') . $countryShort . $companyShort . $stateShort . $nextNumber;
    $deliverable->circuit_id = $circuitID;
    $deliverable->save();
    echo "Updated Deliverable ID {$deliverable->id} with Circuit ID: $circuitID\n";
}
echo "Done updating missing Circuit IDs.\n";
