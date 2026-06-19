<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Maintenance Records Export</title>
    <style>
        @page {
            margin: 110px 30px 50px 30px;
        }

        body, h1, h2, h3, h4, h5, h6, table, th, td, p, span {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
        }

        body {
            font-size: 9px;
            color: #374151;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 60px;
            border-bottom: 2px solid #111827;
            padding-bottom: 10px;
        }

        header table {
            width: 100%;
            border-collapse: collapse;
        }

        header td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }

        .logo {
            height: 40px;
            /* Adjust based on your logo's aspect ratio */
        }

        .report-info {
            text-align: right;
        }

        .report-info h1 {
            margin: 0;
            font-size: 18px;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-info p {
            margin: 4px 0 0 0;
            font-size: 10px;
            color: #6B7280;
        }

        footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 20px;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
            font-size: 8px;
            color: #9CA3AF;
        }

        footer table {
            width: 100%;
            border-collapse: collapse;
        }

        footer td {
            border: none;
            padding: 0;
        }

        .page-number:before {
            content: "Page " counter(page) " of " counter(pages);
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            word-wrap: break-word;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #d1d5db;
            padding: 6px 4px;
            vertical-align: top;
        }

        table.data-table th {
            background-color: #111827;
            color: #ffffff;
            font-weight: 600;
            font-size: 8px;
            text-transform: uppercase;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        /* Column Widths Configuration to ensure everything fits perfectly */
        .col-date {
            width: 5%;
        }

        .col-shift {
            width: 4%;
        }

        .col-group {
            width: 6%;
        }

        .col-line {
            width: 6%;
        }

        .col-machine-no {
            width: 6%;
        }

        .col-machine-name {
            width: 7%;
        }

        .col-type {
            width: 7%;
        }

        .col-part-name {
            width: 8%;
        }

        .col-part-qty {
            width: 5%;
        }

        .col-time {
            width: 5%;
        }

        .col-duration {
            width: 5%;
        }

        .col-problem {
            width: 8%;
        }

        .col-cause {
            width: 8%;
        }

        .col-action {
            width: 8%;
        }

        .col-status {
            width: 5%;
        }

        .col-pic {
            width: 6%;
        }
    </style>
</head>

<body>
    @php
        $logoPath = public_path('images/logo.png');
        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
        $logoSrc = $logoData ? 'data:image/png;base64,' . $logoData : '';
    @endphp

    <header>
        <table>
            <tr>
                <td style="width: 50%;">
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" class="logo" alt="Company Logo">
                    @else
                        <h2 style="margin:0; color:#111827; font-style: italic;">COMPANY LOGO</h2>
                    @endif
                </td>
                <td style="width: 50%;" class="report-info">
                    <h1>Maintenance Cardty Report</h1>
                    <p>Generated on: {{ now()->format('d M Y, H:i') }}</p>
                    @if($startDate || $endDate)
                        <p>Period: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Beginning' }} -
                            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'End' }}</p>
                    @else
                        <p>Period: All Time Records</p>
                    @endif
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table>
            <tr>
                <td style="text-align: left;">Computerized Maintenance Management System (CMMS)</td>
                <td style="text-align: right;" class="page-number"></td>
            </tr>
        </table>
    </footer>

    <main>
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center col-date">Date</th>
                    <th class="text-center col-shift">Shift</th>
                    <th class="text-center col-group">Group<br>Line</th>
                    <th class="col-line">Line Name</th>
                    <th class="text-center col-machine-no">Machine No</th>
                    <th class="col-machine-name">Machine Name</th>
                    <th class="col-type">Type Of Problem</th>
                    <th class="col-part-name">Sparepart Name</th>
                    <th class="text-center col-part-qty">Part Qty</th>
                    <th class="text-center col-time">Start Time</th>
                    <th class="text-center col-time">Finish Time</th>
                    <th class="text-center col-duration">Down<br>Time</th>
                    <th class="text-center col-duration">Work<br>Time</th>
                    <th class="col-problem">Problem</th>
                    <th class="col-cause">Cause</th>
                    <th class="col-action">Action</th>
                    <th class="text-center col-status">Status</th>
                    <th class="col-pic">PIC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                    @php
                        $pics = is_array($record->pics) && count($record->pics) > 0
                            ? implode(', ', $record->pics)
                            : ($record->PIC ?? '-');

                        $sparePartsNameStr = '-';
                        $sparePartsQtyStr = '-';
                        if ($record->spareParts->isNotEmpty()) {
                            $names = $record->spareParts->pluck('part_name')->toArray();
                            $qtys = $record->spareParts->map(function ($part) {
                                return $part->pivot->qty;
                            })->toArray();
                            $sparePartsNameStr = implode(', ', $names);
                            $sparePartsQtyStr = implode(', ', $qtys);
                        } elseif ($record->sparepartName) {
                            $sparePartsNameStr = $record->sparepartName;
                            $sparePartsQtyStr = $record->sparepartQty ?? 0;
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $record->Date ? $record->Date->format('Y-m-d') : '-' }}</td>
                        <td class="text-center">{{ $record->Shift }}</td>
                        <td class="text-center">{{ $record->groupline }}</td>
                        <td>{{ $record->LineName }}</td>
                        <td class="text-center">{{ $record->MachineNo }}</td>
                        <td>{{ $record->MachineName }}</td>
                        <td>{{ $record->typeofproblem }}</td>
                        <td>{{ $sparePartsNameStr }}</td>
                        <td class="text-center">{{ $sparePartsQtyStr }}</td>
                        <td class="text-center">{{ $record->start_time }}</td>
                        <td class="text-center">{{ $record->finish_time }}</td>
                        <td class="text-center">{{ $record->DownTime }}</td>
                        <td class="text-center">{{ $record->worktime }}</td>
                        <td>{{ $record->Problem }}</td>
                        <td>{{ $record->Cause }}</td>
                        <td>{{ $record->Action }}</td>
                        <td class="text-center">{{ $record->Status }}</td>
                        <td>{{ $pics }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>

</html>