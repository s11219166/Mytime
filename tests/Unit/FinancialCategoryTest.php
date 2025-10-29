<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialCategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private FinancialCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = FinancialCategory::factory()->create([
            'name' => 'Salary',
            'type' => 'income',
            'user_id' => $this->user->id
        ]);
    }

    /**
     * Test category belongs to user relationship.
     */
    public function test_category_belongs_to_user(): void
    {
        $this->assertInstanceOf(User::class, $this->category->user);
        $this->assertEquals($this->user->id, $this->category->user->id);
    }

    /**
     * Test category has many transactions relationship.
     */
    public function test_category_has_many_transactions(): void
    {
        FinancialTransaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id
        ]);

        $this->assertEquals(3, $this->category->transactions()->count());
        $this->assertInstanceOf(FinancialTransaction::class, $this->category->transactions->first());
    }

    /**
     * Test scope for filtering by type.
     */
    public function test_scope_of_type(): void
    {
        FinancialCategory::factory()->create([
            'type' => 'expense'
        ]);

        $incomeCategories = FinancialCategory::ofType('income')->get();
        $expenseCategories = FinancialCategory::ofType('expense')->get();

        $this->assertEquals(1, $incomeCategories->count());
        $this->assertEquals(1, $expenseCategories->count());
    }

    /**
     * Test scope for filtering by user.
     */
    public function test_scope_for_user(): void
    {
        // Create a system category (user_id = null)
        FinancialCategory::factory()->create([
            'user_id' => null
        ]);

        // Create another user's category
        $otherUser = User::factory()->create();
        FinancialCategory::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $categories = FinancialCategory::forUser($this->user->id)->get();

        // Should include both user's categories and system categories
        $this->assertEquals(2, $categories->count());
    }

    /**
     * Test getting total amount for category.
     */
    public function test_get_total_amount(): void
    {
        // Create completed transactions
        FinancialTransaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 100.00,
            'status' => 'completed'
        ]);

        // Create a pending transaction (should not be counted)
        FinancialTransaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 100.00,
            'status' => 'pending'
        ]);

        $total = $this->category->getTotalAmount();
        $this->assertEquals(300.00, $total);
    }

    /**
     * Test getting total amount within date range.
     */
    public function test_get_total_amount_with_date_range(): void
    {
        // Create transactions with different dates
        FinancialTransaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 100.00,
            'status' => 'completed',
            'transaction_date' => now()->subDays(5)
        ]);

        FinancialTransaction::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 200.00,
            'status' => 'completed',
            'transaction_date' => now()
        ]);

        $total = $this->category->getTotalAmount(
            now()->subDays(3),
            now()
        );

        $this->assertEquals(200.00, $total);
    }

    /**
     * Test soft deletes.
     */
    public function test_soft_deletes(): void
    {
        $this->category->delete();

        $this->assertSoftDeleted('financial_categories', [
            'id' => $this->category->id
        ]);

        $this->assertEquals(0, FinancialCategory::count());
        $this->assertEquals(1, FinancialCategory::withTrashed()->count());
    }
}
