<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteRenderingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(UserSeeder::class);
    }

    public function test_dashboard_renders_for_every_role(): void
    {
        foreach (['admin', 'cs', 'manager', 'teknisi'] as $role) {
            $user = User::whereHas('roles', fn ($q) => $q->where('name', $role))->first();
            $this->actingAs($user)->get(route('dashboard'))->assertOk();
        }
    }

    public function test_tickets_pages_render(): void
    {
        $cs = User::where('email', 'cs@ticketing.test')->first();
        $manager = User::where('email', 'manager@ticketing.test')->first();

        $customer = Customer::create(['name' => 'Render Test', 'phone' => '628888888888']);
        $category = Category::first();
        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'category_id' => $category->id,
            'created_by' => $cs->id,
            'title' => 'Render Test',
            'description' => 'Test',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $this->actingAs($cs)->get(route('tickets.index'))->assertOk();
        $this->actingAs($cs)->get(route('tickets.create'))->assertOk();
        $this->actingAs($cs)->get(route('tickets.show', $ticket))->assertOk();
        $this->actingAs($cs)->get(route('tickets.edit', $ticket))->assertOk();

        $this->actingAs($manager)->get(route('tickets.show', $ticket))->assertOk();
    }

    public function test_customers_pages_render(): void
    {
        $cs = User::where('email', 'cs@ticketing.test')->first();
        $customer = Customer::create(['name' => 'Cust Render', 'phone' => '628777777777']);

        $this->actingAs($cs)->get(route('customers.index'))->assertOk();
        $this->actingAs($cs)->get(route('customers.create'))->assertOk();
        $this->actingAs($cs)->get(route('customers.show', $customer))->assertOk();
        $this->actingAs($cs)->get(route('customers.edit', $customer))->assertOk();
    }

    public function test_categories_pages_render(): void
    {
        $admin = User::where('email', 'admin@ticketing.test')->first();
        $cat = Category::first();

        $this->actingAs($admin)->get(route('categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('categories.create'))->assertOk();
        $this->actingAs($admin)->get(route('categories.edit', $cat))->assertOk();
    }

    public function test_daily_reports_pages_render(): void
    {
        $teknisi = User::where('email', 'teknisi@ticketing.test')->first();

        $this->actingAs($teknisi)->get(route('daily-reports.index'))->assertOk();
        $this->actingAs($teknisi)->get(route('daily-reports.create'))->assertOk();
    }

    public function test_reports_page_renders(): void
    {
        $manager = User::where('email', 'manager@ticketing.test')->first();
        $this->actingAs($manager)->get(route('reports.index'))->assertOk();
    }
}
