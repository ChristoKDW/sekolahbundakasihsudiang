@component('emails.layout', ['subject' => 'Pengingat Pembayaran'])
    <p class="greeting">Yth. Bapak/Ibu {{ $bill->student->parent->name ?? 'Orang Tua' }},</p>
    
    <div class="message">
        @if(isset($daysUntilDue) && $daysUntilDue > 0)
        <p>Ini adalah pengingat bahwa terdapat tagihan yang akan <strong>jatuh tempo dalam {{ $daysUntilDue }} hari</strong>:</p>
        @elseif(isset($daysUntilDue) && $daysUntilDue == 0)
        <p>Ini adalah pengingat bahwa terdapat tagihan yang <strong style="color: #dc3545;">sudah jatuh tempo</strong>:</p>
        @else
        <p>Ini adalah pengingat bahwa terdapat tagihan yang belum dibayar:</p>
        @endif
    </div>
    
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Nama Siswa</span>
            <span class="info-value">{{ $bill->student->name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">NIS</span>
            <span class="info-value">{{ $bill->student->nis ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Kelas</span>
            <span class="info-value">{{ $bill->student->class ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jenis Tagihan</span>
            <span class="info-value">{{ $bill->billType->name ?? '-' }}</span>
        </div>
        @if($bill->month)
        <div class="info-row">
            <span class="info-label">Periode</span>
            <span class="info-value">{{ $bill->month }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Jatuh Tempo</span>
            <span class="info-value {{ $bill->due_date->isPast() ? 'status-danger' : '' }}">
                {{ $bill->due_date->format('d F Y') }}
                @if($bill->due_date->isPast())
                (TERLAMBAT)
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Nominal Tagihan</span>
            <span class="amount">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</span>
        </div>
        @if($bill->paid_amount > 0)
        <div class="info-row">
            <span class="info-label">Sudah Dibayar</span>
            <span class="info-value status-success">Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Sisa Tagihan</span>
            <span class="amount status-danger">Rp {{ number_format($bill->total_amount - $bill->paid_amount, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>
    
    @if($bill->billType->is_flexible ?? false)
    <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; color: #155724;">
            <strong>💰 Pembayaran Fleksibel</strong><br>
            <span style="font-size: 14px;">Untuk tagihan ini, Anda dapat membayar sesuai dengan kemampuan. Tidak harus langsung lunas, bayar berapa saja yang Anda mampu.</span>
        </p>
    </div>
    @else
    <p>Mohon segera melakukan pembayaran sebelum jatuh tempo untuk menghindari denda keterlambatan.</p>
    @endif
    
    <div style="text-align: center;">
        <a href="{{ route('parent.payments.index') }}" class="btn">Bayar Sekarang</a>
    </div>
    
    <p style="color: #6c757d; font-size: 13px;">Jika Anda sudah melakukan pembayaran, mohon abaikan email ini.</p>
    
    <p>Salam hormat,<br><strong>Tim Keuangan Sekolah</strong></p>
@endcomponent
