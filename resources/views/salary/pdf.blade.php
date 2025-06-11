<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $salary->user->name }}</title>
    <style>
        body { font-family: Arial,sans-serif; font-size:11px; margin:20px; color:#000 }
        .header { text-align:center; margin-bottom:15px }
        .header h2 { margin:0; font-size:16px }
        .header p { margin:1px 0; font-size:10px }
        .title { text-align:center; font-weight:bold; margin:15px 0 }
        table { width:100%; border-collapse:collapse; margin-bottom:12px }
        td { padding:4px; vertical-align:top }
        .label { width:100px; font-weight:bold }
        .section-title { font-weight:bold; margin-top:10px; border-bottom:1px solid #000 }
        .amount { text-align:right }
        .total { font-weight:bold; border-top:1px solid #000; padding-top:3px }
        .net-salary { text-align:center; font-size:13px; font-weight:bold; margin:15px 0; padding:8px; border:1px solid #000 }
        .terbilang { font-style:italic; font-size:10px; margin-bottom:15px }
        .signature { width:100%; margin-top:30px; }
        .signature td { text-align:center; padding-top:5px }
        .signature-line { height:60px }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ config('app.company','NAMA PERUSAHAAN') }}</h2>
        <p>{{ config('app.alamat','Karawang, Jawa Barat') }}</p>
        <p>{{ config('app.contact','Telp: (021) 123456 | Email: cvpurwaputera@gmail.com') }}</p>
    </div>

    <div class="title">Slip Gaji Pegawai</div>

    <table>
        <tr>
            <td class="label">Nama</td><td>: {{ $salary->user->name }}</td>
            <td class="label">Periode</td><td>: {{ \Carbon\Carbon::parse($salary->period)->format('F Y') }}</td>
        </tr>
        <tr>
            <td class="label">NIP/ID</td><td>: {{ $salary->user->users_id }}</td>
            <td class="label">Tanggal Cetak</td><td>: {{ \Carbon\Carbon::now()->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td><td>: {{ $salary->user->job_title ?? '-' }}</td>
            <td class="label">Hadir</td><td>: {{ $salary->total_attendance ?? 0 }} hari</td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="width:48%; vertical-align:top">
                <div class="section-title">Pendapatan</div>
                <table width="100%">
                    <tr><td>Gaji Pokok</td><td class="amount">Rp {{ number_format($salary->base_salary,0,',','.') }}</td></tr>
                    @if($salary->overtime > 0)
                    <tr><td>Lembur</td><td class="amount">Rp {{ number_format($salary->overtime,0,',','.') }}</td></tr>
                    @endif
                    @php $totalAllow = 0; @endphp
                    @foreach($salary->allowances ?? [] as $a)
                        @php
                            $amt = $a->type=='percentage' ? $salary->base_salary * $a->percentage/100 : $a->amount;
                            $totalAllow += $amt;
                        @endphp
                        <tr><td>{{ $a->name }}</td><td class="amount">Rp {{ number_format($amt,0,',','.') }}</td></tr>
                    @endforeach
                    <tr class="total"><td>Total Pendapatan</td>
                        <td class="amount">Rp {{ number_format($salary->base_salary + $salary->overtime + $totalAllow,0,',','.') }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:4%"></td>
            <td style="width:48%; vertical-align:top">
                <div class="section-title">Potongan</div>
                <table width="100%">
                    @php $totalDed = 0; @endphp
                    @foreach($salary->deductions ?? [] as $d)
                        @php
                            $amt = $d->type=='percentage' ? $salary->base_salary * $d->percentage/100 : $d->amount;
                            $totalDed += $amt;
                        @endphp
                        <tr><td>{{ $d->name }}</td><td class="amount">Rp {{ number_format($amt,0,',','.') }}</td></tr>
                    @endforeach
                    <tr class="total"><td>Total Potongan</td><td class="amount">Rp {{ number_format($totalDed,0,',','.') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    @php
    $net = ($salary->base_salary + $salary->overtime + $totalAllow) - $totalDed;
    function terbilang($n){
        $n = abs($n);
        $a = ["","satu","dua","tiga","empat","lima","enam","tujuh","delapan","sembilan","sepuluh","sebelas"];
        return $n < 12 ? " ".$a[$n]
            : ($n < 20 ? terbilang($n-10)." belas"
            : ($n < 100 ? terbilang(intval($n/10))." puluh".terbilang($n%10)
            : ($n < 200 ? " seratus".terbilang($n-100)
            : ($n < 1000 ? terbilang(intval($n/100))." ratus".terbilang($n%100)
            : ($n < 2000 ? " seribu".terbilang($n-1000)
            : ($n < 1000000 ? terbilang(intval($n/1000))." ribu".terbilang($n%1000)
            : ($n < 1000000000 ? terbilang(intval($n/1000000))." juta".terbilang($n%1000000)
            : "")))))));
    }
    @endphp

    <div class="net-salary">Gaji Bersih: Rp {{ number_format($net,0,',','.') }}</div>
    <div class="terbilang">Terbilang:{{ ucwords(trim(terbilang($net))) }} rupiah</div>

    <table class="signature">
        <tr><td>Karawang, {{ \Carbon\Carbon::now()->format('d F Y') }}</td><td></td></tr>
        <tr class="signature-line"><td></td><td></td></tr>
        <tr><td><strong>{{ $salary->user->name }}</strong></td><td><strong>HRD Manager</strong></td></tr>
    </table>
</body>
</html>
