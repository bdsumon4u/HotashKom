<?php

declare(strict_types=1);

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Investor;
use Illuminate\View\View;

class InvestmentController extends Controller
{
    /**
     * Display a listing of the investor's investments.
     */
    public function index(): View
    {
        /** @var Investor $investor */
        $investor = auth('investor')->user();

        $investments = $investor->investments()
            ->with(['installments'])
            ->latest()
            ->paginate(15);

        return view('investor.investments.index', compact('investments'));
    }

    /**
     * Display the specified investment with full installment schedule.
     */
    public function show(Investment $investment): View
    {
        /** @var Investor $investor */
        $investor = auth('investor')->user();

        if ($investment->investor_id !== $investor->id) {
            abort(403);
        }

        $installments = $investment->installments()
            ->orderBy('installment_number')
            ->get();

        return view('investor.investments.show', compact('investment', 'installments'));
    }
}
