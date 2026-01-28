@component('emails.layout', ['subject' => 'Pembayaran Berhasil'])
    <p class="greeting">Yth. Bapak/Ibu {{ $payment->bill->student->parent->name ?? 'Orang Tua' }},</p>
    
    <div class="message">
        <p>Pembayaran Anda telah berhasil diproses. Berikut adalah detail pembayaran:</p>
    </div>
    
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Order ID</span>
            <span class="info-value">{{ $payment->order_id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nama Siswa</span>
            <span class="info-value">{{ $payment->bill->student->name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jenis Tagihan</span>
            <span class="info-value">{{ $payment->bill->billType->name ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Periode</span>
            <span class="info-value">{{ $payment->bill->period_month }}/{{ $payment->bill->period_year }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Metode Pembayaran</span>
            <span class="info-value">{{ strtoupper($payment->payment_type ?? 'ONLINE') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Pembayaran</span>
            <span class="info-value">{{ $payment->paid_at ? $payment->paid_at->format('d F Y H:i') : now()->format('d F Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status</span>
            <span class="info-value status-success">✓ BERHASIL</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jumlah Dibayar</span>
            <span class="amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
        </div>
    </div>
    
    <p>Terima kasih atas pembayaran Anda. Bukti pembayaran ini dapat Anda simpan sebagai referensi.</p>
    
    <div style="text-align: center;">
        <a href="{{ url('/orangtua/pembayaran/riwayat') }}" class="btn">Lihat Riwayat Pembayaran</a>
    </div>
    
    <p>Salam hormat,<br><strong>Tim Keuangan SMKS Bunda Kasih Sudiang</strong></p>
@endcomponent
