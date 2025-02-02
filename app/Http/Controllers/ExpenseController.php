<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ExpenseRequest;
use App\Http\Resources\ExpenseResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpenseController extends Controller
{
    /**
     * Show list of Expenses (related to authenticated user)
     *
     * Filter by date is available
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get the expenses for the authenticated user
        $query = Auth::user()->expenses();

        // Handle range filter (last-week, last-month, last-3-months)
        $range = $request->query('range', null);

        if ($range && in_array($range, ['last-week', 'last-month', 'last-3-months'])) {
            $query->where(function ($q) use ($range) {
                switch ($range) {
                    case 'last-week':
                        $q->where('created_at', '>=', now()->subWeek());
                        break;
                    case 'last-month':
                        $q->where('created_at', '>=', now()->subMonth());
                        break;
                    case 'last-3-months':
                        $q->where('created_at', '>=', now()->subMonths(3));
                        break;
                }
            });
        }

        // Handle custom date range filter (from and to)
        $from = $request->query('from', null);
        $to = $request->query('to', null);

        // Validate the 'from' and 'to' date format
        if ($from && !$this->isValidDateFormat($from)) {
            return $this->failureResponse('Invalid from date format. Use yyyy-mm-dd h:i:s.');
        }
        if ($to && !$this->isValidDateFormat($to)) {
            return $this->failureResponse('Invalid to date format. Use yyyy-mm-dd h:i:s.');
        }

        // Apply custom date range logic
        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        } elseif ($from) {
            $query->where('created_at', '>=', $from);
        } elseif ($to) {
            $query->where('created_at', '<=', $to);
        }

        // Fetch the expenses and return them as a resource collection
        return $this->successResponse(
            'Expenses retrieved successfully.',
            ExpenseResource::collection($query->get())
        );
    }

    /**
     * Create an Expense
     *
     * POST /api/v1/expenses
     *
     * @param \App\Http\Requests\ExpenseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ExpenseRequest $request)
    {
        // Create expense
        $expense = Auth::user()->expenses()
            ->create($request->validated());

        return $this->successResponse(
            'Expense created successfully.',
            new ExpenseResource($expense),
            Response::HTTP_CREATED
        );
    }

    /**
     * Update an Expense (related to authenticated user)
     *
     * PUT|PATCH /api/v1/expenses/{id}
     *
     * @param \App\Http\Requests\ExpenseRequest $request
     * @param \App\Models\Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ExpenseRequest $request, Expense $expense)
    {
        Gate::authorize('isOwner', $expense);

        $expense->update($request->validated());

        return $this->successResponse(
            'Expense updated successfully.',
            new ExpenseResource($expense),
        );
    }

    /**
     * Delete an Expense (related to authenticated user)
     *
     * DELETE /api/v1/expenses/{id}
     *
     * @param \App\Models\Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Expense $expense)
    {
        Gate::authorize('isOwner', $expense);

        // Delete expense
        $expense->delete();

        return $this->successResponse(
            statusCode: Response::HTTP_NO_CONTENT
        );
    }

    // Helper method to validate date format
    private function isValidDateFormat($date)
    {
        return preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $date);
    }
}
