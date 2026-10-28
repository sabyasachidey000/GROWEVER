<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} - Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #2c3e50; color: white; padding: 10px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <h1>{{ config('app.name') }} - Report</h1>
    <p>Generated: {{ now()->format('d M Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                @if($data->isNotEmpty())
                    @foreach(array_keys((array) $data->first()) as $column)
                        <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    @foreach((array) $row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td>No data found</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>{{ config('app.name') }} - Manufacturing Execution System</p>
        <p>Report generated automatically by the system</p>
    </div>
</body>
</html>
