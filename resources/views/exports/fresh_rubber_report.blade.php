<table>
    <!-- Main Header -->
    <tr>
        <th colspan="6" style="font-weight: bold; font-size: 16px; text-align: center;">
            รายงานการขายน้ำยางสด
        </th>
    </tr>
    <tr></tr>

    <!-- Meta Information Header -->
    <tr>
        <td style="font-weight: bold;">{{ $periodLabel }}</td>
        <td></td>
        <td></td>
        <td style="text-align: right; font-weight: bold;">ราคาเฉลี่ย</td>
        <td style="text-align: right; font-weight: bold;">{{ number_format($pricePerKg, 2) }}</td>
        <td style="font-weight: bold;">บาท</td>
    </tr>

    <!-- Table Columns Header -->
    <thead>
        <tr style="background-color: #f3f4f6; font-weight: bold;">
            <th style="border: 1px solid #000; text-align: center;">ชื่อ สกุล</th>
            <th style="border: 1px solid #000; text-align: center;">น้ำหนักสุทธิ</th>
            <th style="border: 1px solid #000; text-align: center;">DRC (%)</th>
            <th style="border: 1px solid #000; text-align: center;">น้ำหนักยางแห้ง</th>
            <th style="border: 1px solid #000; text-align: center;">จำนวนเงิน</th>
            <th style="border: 1px solid #000; text-align: center;">แรงงาน</th>
        </tr>
    </thead>

    <!-- Table Body -->
    <tbody>
        @php
            $totalVolume = 0;
            $totalDryRubber = 0;
            $totalAmount = 0;
            $totalLabor = 0;
        @endphp

        @forelse($transactions as $t)
            @php
                $totalVolume += $t->volume_kg;
                $totalDryRubber += $t->dry_rubber_weight_kg;
                $totalAmount += $t->total_amount;
                $totalLabor += 1;
            @endphp
            <tr>
                <td style="border: 1px solid #000;">{{ $t->plot->farmer->name ?? $t->farmer_name ?? 'N/A' }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($t->volume_kg, 2) }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($t->dry_rubber_content, 2) }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($t->dry_rubber_weight_kg, 2) }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($t->total_amount, 2) }}</td>
                <td style="border: 1px solid #000; text-align: center;">1</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="border: 1px solid #000; text-align: center;">No records found for this period.</td>
            </tr>
        @endforelse
    </tbody>

    <!-- Table Footer (Totals Row) -->
    <tfoot>
        <tr style="font-weight: bold; background-color: #e5e7eb;">
            <td style="border: 1px solid #000; text-align: center;">รวมทั้งหมด</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($totalVolume, 2) }}</td>
            <td style="border: 1px solid #000; text-align: right;">
                {{ $totalVolume > 0 ? number_format(($totalDryRubber / $totalVolume) * 100, 2) : '0.00' }}
            </td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($totalDryRubber, 2) }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format($totalAmount, 2) }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $totalLabor }}</td>
        </tr>
    </tfoot>
</table>