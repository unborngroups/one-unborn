<?php

namespace App\Services;

use App\Models\ChatGroup;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LiveChatService
{
    /**
     * Ensure the authenticated user has chat groups for each accessible company
     * and that the pivot membership exists. Returns the synced groups.
     */
    public function syncUserGroups(User $user): Collection
    {
        $companyIds = $this->resolveCompanyIdsFor($user);

        if (empty($companyIds)) {
            return collect();
        }

        return DB::transaction(function () use ($companyIds, $user) {
            $companies = Company::query()
                ->whereIn('id', $companyIds)
                ->get(['id', 'company_name']);

            $groups = ChatGroup::query()
                ->whereIn('company_id', $companyIds)
                ->get()
                ->keyBy('company_id');

            foreach ($companies as $company) {
                $group = $groups->get($company->id);

                if (! $group) {
                    $group = ChatGroup::create([
                        'name' => trim(($company->company_name ?: 'Company') . ' • Team Chat'),
                        'created_by' => $user->id,
                        'company_id' => $company->id,
                    ]);
                    $groups->put($company->id, $group);
                }

                $group->users()->syncWithoutDetaching([$user->id]);
            }

            return ChatGroup::query()
                ->with(['company:id,company_name'])
                ->whereIn('company_id', $companyIds)
                ->orderBy('company_id')
                ->get();
        });
    }

    /**
     * Determine whether the given user can access the chat group.
     */
    public function userCanAccessGroup(User $user, ChatGroup $group): bool
    {
        $companyIds = $this->resolveCompanyIdsFor($user);

        if (empty($companyIds)) {
            return false;
        }

        return in_array($group->company_id, $companyIds, true);
    }

    /**
     * Resolve company IDs the user can access based on RBAC rules.
     */
    protected function resolveCompanyIdsFor(User $user): array
    {
        $userTypeId = (int) ($user->user_type_id ?? 0);

        if (in_array($userTypeId, [1, 2], true)) {
            return Company::query()->pluck('id')->all();
        }

        return $user->companies()->pluck('companies.id')->all();
    }
}
