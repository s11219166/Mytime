<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\FinancialTransaction;
use App\Models\FinancialCategory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialTransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private FinancialCategory $category;
    private FinancialTransaction $transaction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = FinancialCategory::factory()->create([
            'type' => 'income'
        ]);
        $this->transaction = FinancialTransaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 1000.00,
            'type' => 'income',
            'status' => 'completed'
        ]);
    }

    /**
     * Test transaction belongs to user relationship.
     */
    public function test_transaction_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->transaction->user);
        $this->assertEquals($this->user->id, $this->transaction->user->id);
    }

    /**
     * Test transaction belongs to category relationship.
     */
    public function test_transaction_belongs_to_category(): void
    {
        $this->assertInstanceOf(FinancialCategory::class, $this->transaction->category);
        $this->assertEquals($this->category->id, $this->transaction->category->id);
    }

    /**
     * Test formatted amount accessor.
     */
    public function test_formatted_amount_accessor(): void
    {
        $this->assertEquals('1,000.00', $this->transaction->formatted_amount);
    }

    /**
     * Test scope for filtering by type.
     */
    public function test_scope_of_type(): void
    {
        FinancialTransaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'expense'
        ]);

        $incomeTransactions = FinancialTransaction::ofType('income')->get();
        $expenseTransactions = FinancialTransaction::ofType('expense')->get();

        $this->assertEquals(1, $incomeTransactions->count());
        $this->assertEquals(1, $expenseTransactions->count());
    }

    /**
     * Test scope for filtering by date range.
     */
    public function test_scope_between_dates(): void
    {
        $pastTransaction = FinancialTransaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'transaction_date' => now()->subDays(5)
        ]);

        $futureTransaction = FinancialTransaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'transaction_date' => now()->addDays(5)
        ]);

        $transactions = FinancialTransaction::betweenDates(
            now()->subDays(6),
            now()->subDays(4)
        )->get();

        $this->assertEquals(1, $transactions->count());
        $this->assertEquals($pastTransaction->id, $transactions->first()->id);
    }

    /**
     * Test scope for user's transactions.
     */
    public function test_scope_for_user(): void
    {
        $otherUser = User::factory()->create();
        FinancialTransaction::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id
        ]);

        $userTransactions = FinancialTransaction::forUser($this->user->id)->get();
        $this->assertEquals(1, $userTransactions->count());
        $this->assertEquals($this->user->id, $userTransactions->first()->user_id);
    }

    /**
     * Test getting transactions summary by type.
     */
    public function test_get_summary_by_type(): void
    {
        FinancialTransaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 500.00,
            'status' => 'completed'
        ]);

        $summary = FinancialTransaction::getSummaryByType($this->user->id);

        $this->assertEquals(1000.00, $summary['income']);
        $this->assertEquals(500.00, $summary['expense']);
    }

    /**
     * Test getting monthly trend data.
     */
    public function test_get_monthly_trend(): void
    {
        // Create transactions for the last 3 months
        for ($i = 0; $i < 3; $i++) {
            FinancialTransaction::factory()->create([
                'user_id' => $this->user->id,
                'category_id' => $this->category->id,
                'transaction_date' => now()->subMonths($i),
                'type' => 'income',
                'amount' => 1000.00,
                'status' => 'completed'
            ]);
        }

        $trends = FinancialTransaction::getMonthlyTrend($this->user->id, 3);

        $this->assertEquals(3, $trends->count());
        $this->assertEquals(2000.00, $trends->first()->total_amount); // Including the transaction from setUp
    }

    /**
     * Test soft deletes.
     */
    public function test_soft_deletes(): void
    {
        $this->transaction->delete();

        $this->assertSoftDeleted('financial_transactions', [
            'id' => $this->transaction->id
        ]);

        $this->assertEquals(0, FinancialTransaction::count());
        $this->assertEquals(1, FinancialTransaction::withTrashed()->count());
    }
}
