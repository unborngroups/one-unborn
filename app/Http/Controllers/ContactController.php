<?php

namespace App\Http\Controllers;

use App\Helpers\TemplateHelper;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function vendorIndex(Request $request): View
    {
        return $this->indexByType($request, 'vendor');
    }

    public function supportIndex(Request $request): View
    {
        return $this->indexByType($request, 'support');
    }

    public function create(string $type): View
    {
        $type = $this->normalizeType($type);

        return view('contact.create', [
            'type' => $type,
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $type = $this->normalizeType($type);
        $data = $this->validateData($request);
        $data['contact_type'] = $type;
        $data['status'] = strtolower((string) ($data['status'] ?? 'active'));
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        Contact::create($data);

        return redirect()->route('contacts.' . $type . '.index')
            ->with('success', ucfirst($type) . ' contact created successfully.');
    }

    public function show(string $type, Contact $contact): View
    {
        $type = $this->normalizeType($type);
        $this->guardType($type, $contact);

        return view('contact.show', [
            'type' => $type,
            'contact' => $contact,
        ]);
    }

    public function edit(string $type, Contact $contact): View
    {
        $type = $this->normalizeType($type);
        $this->guardType($type, $contact);

        return view('contact.edit', [
            'type' => $type,
            'contact' => $contact,
        ]);
    }

    public function update(Request $request, string $type, Contact $contact): RedirectResponse
    {
        $type = $this->normalizeType($type);
        $this->guardType($type, $contact);

        $data = $this->validateData($request);
        $data['status'] = strtolower((string) ($data['status'] ?? $contact->status));
        $data['updated_by'] = Auth::id();

        $contact->update($data);

        return redirect()->route('contacts.' . $type . '.index')
            ->with('success', ucfirst($type) . ' contact updated successfully.');
    }

    public function destroy(string $type, Contact $contact): RedirectResponse
    {
        $type = $this->normalizeType($type);
        $this->guardType($type, $contact);

        $contact->deleted_by = Auth::id();
        $contact->save();
        $contact->delete();

        return redirect()->route('contacts.' . $type . '.index')
            ->with('success', ucfirst($type) . ' contact deleted successfully.');
    }

    public function toggleStatus(string $type, Contact $contact): RedirectResponse
    {
        $type = $this->normalizeType($type);
        $this->guardType($type, $contact);

        $contact->status = strtolower((string) $contact->status) === 'active' ? 'inactive' : 'active';
        $contact->updated_by = Auth::id();
        $contact->save();

        return redirect()->route('contacts.' . $type . '.index')
            ->with('success', ucfirst($type) . ' contact status updated to ' . ucfirst($contact->status) . '.');
    }

    private function indexByType(Request $request, string $type): View
    {
        $type = $this->normalizeType($type);
        $permissions = TemplateHelper::getUserMenuPermissions('Contact') ?? (object) [
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $search = trim((string) $request->get('search', ''));

        $query = Contact::where('contact_type', $type);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('area', 'like', '%' . $search . '%')
                    ->orWhere('state', 'like', '%' . $search . '%')
                    ->orWhere('contact1', 'like', '%' . $search . '%')
                    ->orWhere('contact2', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%');
            });
        }

        $contacts = $query
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('contact.index', [
            'type' => $type,
            'contacts' => $contacts,
            'permissions' => $permissions,
        ]);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'area' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'contact1' => 'required|string|max:20',
            'contact2' => 'nullable|string|max:20',
            'remarks' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);
    }

    private function normalizeType(string $type): string
    {
        $type = strtolower(trim($type));
        abort_unless(in_array($type, ['vendor', 'support'], true), 404);

        return $type;
    }

    private function guardType(string $type, Contact $contact): void
    {
        abort_unless($contact->contact_type === $type, 404);
    }
}
