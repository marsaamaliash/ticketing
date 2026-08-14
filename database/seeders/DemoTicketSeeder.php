<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketDevice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoTicketSeeder extends Seeder
{
    public function run(): void
    {
        $cs = User::role('cs')->first();
        $manager = User::role('manager')->first();
        $teknisiList = User::role('teknisi')->get();

        if (! $cs || ! $manager || $teknisiList->isEmpty()) {
            $this->command->warn('Users belum ada — jalankan RolePermissionSeeder & UserSeeder dulu.');

            return;
        }

        $customerData = [
            ['name' => 'Budi Santoso', 'phone' => '6281211111111', 'address' => 'Jl. Merdeka No. 10, Jakarta Pusat', 'city' => 'Jakarta'],
            ['name' => 'Siti Aminah', 'phone' => '6281222222222', 'address' => 'Jl. Sudirman Kav. 25, Bandung', 'city' => 'Bandung'],
            ['name' => 'Andi Wijaya', 'phone' => '6281233333333', 'address' => 'Jl. Diponegoro No. 5, Surabaya', 'city' => 'Surabaya'],
            ['name' => 'Dewi Lestari', 'phone' => '6281244444444', 'address' => 'Jl. Veteran No. 17, Yogyakarta', 'city' => 'Yogyakarta'],
            ['name' => 'Rini Handayani', 'phone' => '6281255555555', 'address' => 'Jl. Asia Afrika No. 100, Medan', 'city' => 'Medan'],
            ['name' => 'Fajar Nugroho', 'phone' => '6281266666666', 'address' => 'Jl. Cendrawasih No. 3, Semarang', 'city' => 'Semarang'],
            ['name' => 'Maya Sari', 'phone' => '6281277777777', 'address' => 'Jl. Gajah Mada No. 88, Denpasar', 'city' => 'Denpasar'],
            ['name' => 'Eko Susanto', 'phone' => '6281288888888', 'address' => 'Jl. Pahlawan No. 22, Makassar', 'city' => 'Makassar'],
            ['name' => 'Lina Marlina', 'phone' => '6281299999999', 'address' => 'Jl. Kemerdekaan No. 99, Palembang', 'city' => 'Palembang'],
        ];

        $customers = [];
        foreach ($customerData as $data) {
            $customers[] = Customer::firstOrCreate(
                ['phone' => $data['phone']],
                $data + ['email' => strtolower(str_replace(' ', '.', $data['name'])).'@example.com'],
            );
        }

        $categories = Category::all();
        $priorities = ['low', 'medium', 'high', 'urgent'];

        $titleSamples = [
            'Instalasi baru',
            'Tidak bisa akses internet',
            'Koneksi lambat / putus-putus',
            'Modem sering restart sendiri',
            'Kabel fiber optik putus',
            'Pelanggan ingin upgrade paket',
            'Perangkat ONT rusak',
        ];

        $targetStatuses = ['open', 'forwarded', 'assigned', 'in_progress', 'finished', 'verified', 'closed'];

        for ($i = 0; $i < count($customers); $i++) {
            $customer = $customers[$i];
            $category = $categories->random();
            $priority = $priorities[array_rand($priorities)];
            $targetStatus = $targetStatuses[$i % count($targetStatuses)];
            $title = $titleSamples[$i % count($titleSamples)];

            $ticket = Ticket::create([
                'customer_id' => $customer->id,
                'category_id' => $category->id,
                'created_by' => $cs->id,
                'title' => $title,
                'description' => "Pelanggan {$customer->name} menghubungi call center untuk menyampaikan masalah: {$title}. Mohon untuk ditindaklanjuti sesuai prosedur.",
                'priority' => $priority,
                'status' => 'open',
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $cs->id,
                'type' => 'created',
                'description' => "Tiket {$ticket->ticket_number} dibuat oleh {$cs->name} untuk pelanggan {$customer->name}.",
            ]);

            if ($targetStatus === 'open') {
                continue;
            }

            $ticket->update(['status' => 'forwarded']);
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $cs->id,
                'type' => 'forwarded',
                'description' => 'Tiket diteruskan ke manager.',
            ]);

            if ($targetStatus === 'forwarded') {
                continue;
            }

            $tech = $teknisiList->random();
            $scheduled = Carbon::now()->subDays(rand(1, 10))->addHours(rand(1, 8));
            $ticket->update([
                'assigned_technician_id' => $tech->id,
                'status' => 'assigned',
                'scheduled_at' => $scheduled,
            ]);

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $manager->id,
                'type' => 'assigned',
                'description' => "Tiket di-assign ke {$tech->name}, jadwal {$scheduled->format('d M Y H:i')}.",
            ]);

            TicketDevice::create([
                'ticket_id' => $ticket->id,
                'device_type' => 'ONT',
                'brand' => ['ZTE', 'Huawei', 'Fiberhome'][$i % 3],
                'model' => ['F609', 'HG8245H', 'HG6145F'][$i % 3],
                'serial_number' => 'SN'.str_pad((string) (10000 + $i * 13), 8, '0', STR_PAD_LEFT),
                'location' => ['Ruang tamu', 'Ruang kerja', 'Luar rumah'][$i % 3],
                'installed_at' => Carbon::now()->subMonths(rand(1, 12)),
            ]);

            if ($targetStatus === 'assigned') {
                continue;
            }

            $ticket->update(['status' => 'in_progress', 'started_at' => $scheduled->copy()->addHour()]);
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $tech->id,
                'type' => 'status_change',
                'description' => "Teknisi {$tech->name} mulai pengerjaan.",
            ]);

            if ($targetStatus === 'in_progress') {
                continue;
            }

            $ticket->update(['status' => 'finished', 'finished_at' => $scheduled->copy()->addHours(3)]);
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $tech->id,
                'type' => 'status_change',
                'description' => 'Pekerjaan selesai. Kabel diganti dan ONT dikonfigurasi ulang.',
            ]);

            if ($targetStatus === 'finished') {
                continue;
            }

            $ticket->update([
                'status' => 'verified',
                'verified_at' => Carbon::now()->subDays(rand(0, 3)),
                'verified_by' => $cs->id,
            ]);
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $cs->id,
                'type' => 'verified',
                'description' => 'CS menghubungi pelanggan dan memverifikasi koneksi sudah normal.',
            ]);

            if ($targetStatus === 'verified') {
                continue;
            }

            $ticket->update([
                'rating' => [3, 4, 5][$i % 3],
                'rating_comment' => ['Cukup membantu.', 'Pelayanan ramah.', 'Sangat puas!'][$i % 3],
                'status' => 'closed',
            ]);
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $cs->id,
                'type' => 'rated',
                'description' => "Tiket ditutup. Rating: {$ticket->rating}/5.",
            ]);
        }

        // Customer 8: Reopened flow (was verified, opened kembali)
        $reopenedCustomer = $customers[7];
        $reopenedCategory = $categories->random();
        $reopenedTicket = Ticket::create([
            'customer_id' => $reopenedCustomer->id,
            'category_id' => $reopenedCategory->id,
            'created_by' => $cs->id,
            'assigned_technician_id' => $teknisiList->first()->id,
            'title' => 'Koneksi putus-putus',
            'description' => 'Kadang internet putus tiba-tiba, restart ONT workaround tapi besok putus lagi.',
            'priority' => 'high',
            'status' => 'in_progress',
            'scheduled_at' => Carbon::now()->subHours(2),
            'started_at' => Carbon::now()->subHour(),
        ]);
        TicketActivity::create([
            'ticket_id' => $reopenedTicket->id,
            'user_id' => $cs->id,
            'type' => 'created',
            'description' => "Tiket {$reopenedTicket->ticket_number} dibuat.",
        ]);
        TicketActivity::create([
            'ticket_id' => $reopenedTicket->id,
            'user_id' => $cs->id,
            'type' => 'reopened',
            'description' => 'Tiket dibuka kembali. Alasan: Koneksi masih putus-putus, perlu investigasi lanjutan.',
        ]);

        // Customer 9: Cancelled by CS
        $cancelledCustomer = $customers[8];
        $cancelledCategory = $categories->random();
        $cancelledTicket = Ticket::create([
            'customer_id' => $cancelledCustomer->id,
            'category_id' => $cancelledCategory->id,
            'created_by' => $cs->id,
            'title' => 'Konsultasi paket',
            'description' => 'Pelanggan ingin tanya paket hemat.',
            'priority' => 'low',
            'status' => 'cancelled',
            'cancellation_reason' => 'Pelanggan mengurungkan niat setelah diinformasikan via telepon.',
        ]);
        TicketActivity::create([
            'ticket_id' => $cancelledTicket->id,
            'user_id' => $cs->id,
            'type' => 'created',
            'description' => "Tiket {$cancelledTicket->ticket_number} dibuat.",
        ]);
        TicketActivity::create([
            'ticket_id' => $cancelledTicket->id,
            'user_id' => $cs->id,
            'type' => 'cancelled',
            'description' => "Tiket dibatalkan. Alasan: {$cancelledTicket->cancellation_reason}",
        ]);
    }
}
