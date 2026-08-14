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

class TicketWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(UserSeeder::class);
    }

    public function test_cs_can_create_ticket(): void
    {
        $cs = User::where('email', 'cs@ticketing.test')->first();
        $customer = Customer::create(['name' => 'Test', 'phone' => '628999999999']);
        $category = Category::first();

        $response = $this->actingAs($cs)->post(route('tickets.store'), [
            'customer_id' => $customer->id,
            'category_id' => $category->id,
            'title' => 'Internet mati',
            'description' => 'Pelanggan tidak bisa akses internet sejak pagi',
            'priority' => 'high',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'customer_id' => $customer->id,
            'created_by' => $cs->id,
            'status' => 'open',
            'priority' => 'high',
        ]);
        $ticket = Ticket::first();
        $this->assertNotNull($ticket->ticket_number);
        $this->assertStringStartsWith('TKT-', $ticket->ticket_number);
    }

    public function test_cs_can_forward_ticket_to_manager(): void
    {
        $cs = User::where('email', 'cs@ticketing.test')->first();
        $manager = User::where('email', 'manager@ticketing.test')->first();
        $customer = Customer::create(['name' => 'Test', 'phone' => '628999999998']);
        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'category_id' => Category::first()->id,
            'created_by' => $cs->id,
            'title' => 'Test',
            'description' => 'Test',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $response = $this->actingAs($cs)->post(route('tickets.forward', $ticket), [
            'note' => 'Tolong segera',
        ]);

        $response->assertRedirect();
        $ticket->refresh();
        $this->assertEquals('forwarded', $ticket->status);

        $this->assertDatabaseHas('notifications', ['notifiable_id' => $manager->id]);
    }

    public function test_manager_can_assign_technician(): void
    {
        $cs = User::where('email', 'cs@ticketing.test')->first();
        $manager = User::where('email', 'manager@ticketing.test')->first();
        $teknisi = User::where('email', 'teknisi@ticketing.test')->first();

        $customer = Customer::create(['name' => 'Test', 'phone' => '628999999997']);
        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'category_id' => Category::first()->id,
            'created_by' => $cs->id,
            'title' => 'Test',
            'description' => 'Test',
            'priority' => 'medium',
            'status' => 'forwarded',
        ]);

        $this->actingAs($manager)->post(route('tickets.assign', $ticket), [
            'assigned_technician_id' => $teknisi->id,
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ])->assertRedirect();

        $ticket->refresh();
        $this->assertEquals('assigned', $ticket->status);
        $this->assertEquals($teknisi->id, $ticket->assigned_technician_id);
        $this->assertNotNull($ticket->scheduled_at);
    }

    public function test_teknisi_cannot_view_other_tickets(): void
    {
        $cs = User::where('email', 'cs@ticketing.test')->first();
        $teknisi1 = User::where('email', 'teknisi@ticketing.test')->first();
        $teknisi2 = User::where('email', 'teknisi2@ticketing.test')->first();

        $customer = Customer::create(['name' => 'Test', 'phone' => '628999999996']);
        $ticketForOther = Ticket::create([
            'customer_id' => $customer->id,
            'category_id' => Category::first()->id,
            'created_by' => $cs->id,
            'assigned_technician_id' => $teknisi2->id,
            'title' => 'Test',
            'description' => 'Test',
            'priority' => 'medium',
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($teknisi1)->get(route('tickets.show', $ticketForOther));
        $response->assertForbidden();

        $ticketForSelf = Ticket::create([
            'customer_id' => $customer->id,
            'category_id' => Category::first()->id,
            'created_by' => $cs->id,
            'assigned_technician_id' => $teknisi1->id,
            'title' => 'My Ticket',
            'description' => 'Test',
            'priority' => 'medium',
            'status' => 'assigned',
        ]);

        $response = $this->actingAs($teknisi1)->get(route('tickets.show', $ticketForSelf));
        $response->assertOk();
    }

    public function test_full_workflow_open_to_closed(): void
    {
        $cs = User::where('email', 'cs@ticketing.test')->first();
        $manager = User::where('email', 'manager@ticketing.test')->first();
        $teknisi = User::where('email', 'teknisi@ticketing.test')->first();

        $customer = Customer::create(['name' => 'Test', 'phone' => '628999999995']);
        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'category_id' => Category::first()->id,
            'created_by' => $cs->id,
            'title' => 'Full Flow',
            'description' => 'Test',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $this->actingAs($cs)->post(route('tickets.forward', $ticket))->assertRedirect();
        $this->actingAs($manager)->post(route('tickets.assign', $ticket), ['assigned_technician_id' => $teknisi->id])->assertRedirect();
        $this->actingAs($teknisi)->post(route('tickets.start', $ticket))->assertRedirect();
        $this->actingAs($teknisi)->post(route('tickets.finish', $ticket), ['action_taken' => 'Selesai'])->assertRedirect();
        $this->actingAs($cs)->post(route('tickets.verify', $ticket))->assertRedirect();
        $this->actingAs($cs)->post(route('tickets.rate', $ticket), ['rating' => 5])->assertRedirect();

        $ticket->refresh();
        $this->assertEquals('closed', $ticket->status);
        $this->assertEquals(5, $ticket->rating);
    }

    public function test_reopen_resets_status_for_assigned_ticket(): void
    {
        $cs = User::where('email', 'cs@ticketing.test')->first();
        $teknisi = User::where('email', 'teknisi@ticketing.test')->first();

        $customer = Customer::create(['name' => 'Test', 'phone' => '628999999994']);
        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'category_id' => Category::first()->id,
            'created_by' => $cs->id,
            'assigned_technician_id' => $teknisi->id,
            'title' => 'Reopen test',
            'description' => 'Test',
            'priority' => 'high',
            'status' => 'verified',
            'finished_at' => now(),
            'verified_at' => now(),
            'verified_by' => $cs->id,
            'rating' => 4,
        ]);

        $this->actingAs($cs)->post(route('tickets.reopen', $ticket), ['reason' => 'Masih bermasalah'])->assertRedirect();

        $ticket->refresh();
        $this->assertEquals('in_progress', $ticket->status);
        $this->assertNull($ticket->rating);
        $this->assertNull($ticket->verified_at);
        $this->assertNull($ticket->finished_at);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $teknisi->id,
            'notifiable_type' => User::class,
        ]);
    }

    public function test_reopen_unassigned_ticket_keeps_reopened_status(): void
    {
        $cs = User::where('email', 'cs@ticketing.test')->first();

        $customer = Customer::create(['name' => 'Test', 'phone' => '628999999993']);
        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'category_id' => Category::first()->id,
            'created_by' => $cs->id,
            'title' => 'Reopen unassigned',
            'description' => 'Test',
            'priority' => 'medium',
            'status' => 'finished',
        ]);

        $this->actingAs($cs)->post(route('tickets.reopen', $ticket), ['reason' => 'Butuh pengerjaan ulang'])->assertRedirect();

        $ticket->refresh();
        $this->assertEquals('reopened', $ticket->status);
        $this->assertNull($ticket->assigned_technician_id);
    }

    public function test_customer_policy_view_any(): void
    {
        $cs = User::where('email', 'cs@ticketing.test')->first();

        $this->actingAs($cs)->get(route('customers.index'))->assertOk();
    }

    public function test_pdf_surat_route_responds(): void
    {
        $cs = User::where('email', 'cs@ticketing.test')->first();
        $customer = Customer::create(['name' => 'PDF Test', 'phone' => '628999999992']);
        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'category_id' => Category::first()->id,
            'created_by' => $cs->id,
            'title' => 'PDF Test',
            'description' => 'PDF content testing.',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $response = $this->actingAs($cs)->get(route('tickets.pdf.surat', $ticket));
        $response->assertOk();
    }

    public function test_pdf_label_batch_route_responds(): void
    {
        $cs = User::where('email', 'cs@ticketing.test')->first();
        $customer = Customer::create(['name' => 'Label Test', 'phone' => '628999999991']);
        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'category_id' => Category::first()->id,
            'created_by' => $cs->id,
            'title' => 'Label Test',
            'description' => 'Test',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $response = $this->actingAs($cs)->post(route('tickets.labels.preview'), ['ticket_ids' => [$ticket->id]]);
        $response->assertOk();
    }
}
