<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    public function __construct(public AccountingService $accountingService) {}

    public function index()
    {
        abort_unless(request()->user()->is('admin'), 403);

        // Ensure default accounts exist
        if (Account::count() === 0) {
            $this->accountingService->seedDefaultAccounts();
        }

        $accounts = Account::orderBy('type')->orderBy('name')->get();

        $groupedAccounts = $accounts->groupBy('type');

        return view('admin.accounting.accounts.index', compact('accounts', 'groupedAccounts'));
    }

    public function create()
    {
        abort_unless(request()->user()->is('admin'), 403);

        return view('admin.accounting.accounts.create');
    }

    public function store(Request $request)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'in:asset,liability,equity,income,expense'],
            'opening_balance' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['opening_balance'] = (float) ($validated['opening_balance'] ?? 0);
        $validated['current_balance'] = $validated['opening_balance'];
        $validated['is_active'] = $request->boolean('is_active', true);

        Account::create($validated);

        return to_route('admin.accounting.accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(Account $account)
    {
        abort_unless(request()->user()->is('admin'), 403);

        return view('admin.accounting.accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        abort_unless(request()->user()->is('admin'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'in:asset,liability,equity,income,expense'],
            'opening_balance' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['opening_balance'] = (float) ($validated['opening_balance'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        $account->update($validated);
        $account->recalculateBalance();

        return to_route('admin.accounting.accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        abort_unless(request()->user()->is('admin'), 403);

        try {
            $account->delete();

            return back()->with('success', 'Account deleted successfully.');
        } catch (ValidationException $e) {
            return back()->with('danger', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('danger', 'Cannot delete account because it is referenced in transactions.');
        }
    }
}
