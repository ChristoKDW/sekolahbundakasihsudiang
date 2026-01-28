<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminderMail;
use App\Models\Bill;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'payments:send-reminders 
                            {--days=7 : Days before due date to send reminder}
                            {--overdue : Also send reminders for overdue bills}';

    /**
     * The console command description.
     */
    protected $description = 'Send payment reminder emails to parents for upcoming due dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $includeOverdue = $this->option('overdue');

        $this->info("Sending payment reminders...");
        $this->newLine();

        // Get bills that are due within X days
        $upcomingBills = Bill::with(['student.parent', 'billType'])
            ->whereIn('status', ['pending', 'partial'])
            ->whereHas('student.parent', function ($query) {
                $query->whereNotNull('email');
            })
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays($days))
            ->get();

        $this->info("Found {$upcomingBills->count()} upcoming bills (due within {$days} days)");

        $sentCount = 0;
        $errorCount = 0;

        foreach ($upcomingBills as $bill) {
            try {
                $parent = $bill->student->parent;
                
                if (!$parent || !$parent->email) {
                    $this->warn("Skipping bill #{$bill->invoice_number}: No parent email");
                    continue;
                }

                $daysUntilDue = now()->diffInDays($bill->due_date, false);

                // Send email
                Mail::to($parent->email)->send(new PaymentReminderMail($bill, $daysUntilDue));

                // Create in-app notification
                Notification::create([
                    'user_id' => $parent->id,
                    'type' => 'payment_reminder',
                    'title' => 'Pengingat Pembayaran',
                    'message' => "Tagihan {$bill->billType->name} untuk {$bill->student->name} akan jatuh tempo dalam {$daysUntilDue} hari.",
                    'data' => json_encode([
                        'bill_id' => $bill->id,
                        'invoice_number' => $bill->invoice_number,
                        'due_date' => $bill->due_date->format('Y-m-d'),
                    ]),
                ]);

                $this->line("✓ Sent to {$parent->email} for bill #{$bill->invoice_number}");
                $sentCount++;

            } catch (\Exception $e) {
                $this->error("✗ Failed for bill #{$bill->invoice_number}: {$e->getMessage()}");
                $errorCount++;
            }
        }

        // Send reminders for overdue bills if flag is set
        if ($includeOverdue) {
            $this->newLine();
            $this->info("Processing overdue bills...");

            $overdueBills = Bill::with(['student.parent', 'billType'])
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->whereHas('student.parent', function ($query) {
                    $query->whereNotNull('email');
                })
                ->where('due_date', '<', now())
                ->get();

            $this->info("Found {$overdueBills->count()} overdue bills");

            foreach ($overdueBills as $bill) {
                try {
                    $parent = $bill->student->parent;
                    
                    if (!$parent || !$parent->email) {
                        continue;
                    }

                    // Send email
                    Mail::to($parent->email)->send(new PaymentReminderMail($bill, 0));

                    // Create in-app notification
                    Notification::create([
                        'user_id' => $parent->id,
                        'type' => 'payment_overdue',
                        'title' => 'Tagihan Terlambat',
                        'message' => "Tagihan {$bill->billType->name} untuk {$bill->student->name} sudah melewati jatuh tempo.",
                        'data' => json_encode([
                            'bill_id' => $bill->id,
                            'invoice_number' => $bill->invoice_number,
                            'due_date' => $bill->due_date->format('Y-m-d'),
                        ]),
                    ]);

                    $this->line("✓ Sent overdue reminder to {$parent->email}");
                    $sentCount++;

                } catch (\Exception $e) {
                    $this->error("✗ Failed: {$e->getMessage()}");
                    $errorCount++;
                }
            }
        }

        $this->newLine();
        $this->info("Done! Sent: {$sentCount}, Failed: {$errorCount}");

        return Command::SUCCESS;
    }
}
