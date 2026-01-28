@component('emails.layout', ['subject' => 'Tagihan Baru'])
    <p class="greeting">Yth. Bapak/Ibu {{ $bill->student->parent->name ?? 'Orang Tua' }},</p>
    
    <div class="message">
        <p>Dengan hormat, kami informasikan bahwa terdapat tagihan baru untuk anak Anda:</p>
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
        <div class="info-row">
            <span class="info-label">Periode</span>
            <span class="info-value">{{ $bill->period_month }}/{{ $bill->period_year }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jatuh Tempo</span>
            <span class="info-value status-pending">{{ $bill->due_date->format('d F Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Nominal</span>
            <span class="amount">Rp {{ number_format($bill->amount, 0, ',', '.') }}</span>
        </div>
    </div>
    
    @if($bill->description)
    <p><strong>Keterangan:</strong> {{ $bill->description }}</p>
    @endif
    
    <p>Mohon segera melakukan pembayaran sebelum tanggal jatuh tempo.</p>
    
    <div style="text-align: center;">
        <a href="{{ url('/orangtua/pembayaran') }}" class="btn">Bayar Sekarang</a>
    </div>
    
    <p>Terima kasih atas perhatian dan kerjasamanya.</p>
    
    <p>Salam hormat,<br><strong>Tim Keuangan SMKS Bunda Kasih Sudiang</strong></p>
@endcomponent
