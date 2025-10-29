<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FinancialTransaction;
use App\Models\FinancialCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FinancialControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test that unauthenticated users cannot access financial pages.
     */
    public function test_unauthenticated_users_cannot_access_financial_pages(): void
    {
        $response = $this->get('/financial');
        $response->assertRedirect('/login');
    }

    /**
     * Test that authenticated users can access financial dashboard.
     */
    public function test_authenticated_users_can_access_financial_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/financial');
        $response->assertStatus(200);
        $response->assertViewIs('financial.index');
    }

    /**
     * Test creating a new transaction.
     */
    public function test_can_create_transaction(): void
    {
        $user = User::factory()->create();
        $category = FinancialCategory::factory()->create(['type' => 'income']);

        $transactionData = [
            'transaction_date' => now()->format('Y-m-d'),
            'type' => 'income',
            'category_id' => $category->id,
            'amount' => 1000.00,
            'description' => 'Test transaction',
            'status' => 'completed',
            'reference_number' => 'REF123'
        ];

        $response = $this->actingAs($user)
            ->postJson('/financial/transaction', $transactionData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transaction created successfully']);

        $this->assertDatabaseHas('financial_transactions', [
            'user_id' => $user->id,
            'amount' => 1000.00,
            'description' => 'Test transaction'
        ]);
    }

    /**
     * Test updating an existing transaction.
     */
    public function test_can_update_transaction(): void
    {
        $user = User::factory()->create();
        $category = FinancialCategory::factory()->create(['type' => 'income']);
        $transaction = FinancialTransaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id
        ]);

        $updatedData = [
            'amount' => 2000.00,
            'description' => 'Updated transaction'
        ];

        $response = $this->actingAs($user)
            ->putJson("/financial/transaction/{$transaction->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transaction updated successfully']);

        $this->assertDatabaseHas('financial_transactions', [
            'id' => $transaction->id,
            'amount' => 2000.00,
            'description' => 'Updated transaction'
        ]);
    }

    /**
     * Test deleting a transaction.
     */
    public function test_can_delete_transaction(): void
    {
        $user = User::factory()->create();
        $category = FinancialCategory::factory()->create();
        $transaction = FinancialTransaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/financial/transaction/{$transaction->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transaction deleted successfully']);

        $this->assertSoftDeleted('financial_transactions', [
            'id' => $transaction->id
        ]);
    }

    /**
     * Test that users can only access their own transactions.
     */
    public function test_users_can_only_access_their_own_transactions(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $category = FinancialCategory::factory()->create();

        $transaction = FinancialTransaction::factory()->create([
            'user_id' => $user1->id,
            'category_id' => $category->id
        ]);

        $response = $this->actingAs($user2)
            ->putJson("/financial/transaction/{$transaction->id}", [
                'amount' => 3000.00
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test filtering transactions.
     */
    public function test_can_filter_transactions(): void
    {
        $user = User::factory()->create();
        $category = FinancialCategory::factory()->create(['type' => 'income']);

        // Create some test transactions
        FinancialTransaction::factory()->count(3)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'income',
            'transaction_date' => now()
        ]);

        FinancialTransaction::factory()->count(2)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'transaction_date' => now()->subDays(5)
        ]);

        $response = $this->actingAs($user)
            ->getJson('/financial/filter?type=income');

        $response->assertStatus(200);
        $this->assertEquals(3, count($response->json('data')));
    }

    /**
     * Test exporting transactions.
     */
    public function test_can_export_transactions(): void
    {
        $user = User::factory()->create();
        $category = FinancialCategory::factory()->create();

        FinancialTransaction::factory()->count(5)->create([
            'user_id' => $user->id,
            'category_id' => $category->id
        ]);

        $response = $this->actingAs($user)
            ->get('/financial/export');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertHeader('Content-Disposition', 'attachment; filename=transactions.csv');
    }

    /**
     * Test retrieving chart data.
     */
    public function test_can_get_chart_data(): void
    {
        $user = User::factory()->create();
        $category = FinancialCategory::factory()->create();

        FinancialTransaction::factory()->count(5)->create([
            'user_id' => $user->id,
            'category_id' => $category->id
        ]);

        $response = $this->actingAs($user)
            ->getJson('/financial/chart-data');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'expensesByCategory',
                'dailyTotals'
            ]);
    }
}
