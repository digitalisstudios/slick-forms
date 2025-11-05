<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $form->name }} - Submissions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
        }
        .meta {
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $form->name }} - Submissions Export</h1>
        <div class="meta">
            Generated: {{ now()->format('F j, Y g:i A') }} |
            Total Submissions: {{ $submissions->count() }}
        </div>
    </div>

    @if($submissions->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 120px;">Submitted At</th>
                    @foreach($fields as $field)
                        <th>{{ $field['label'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $submission)
                    <tr>
                        <td>{{ $submission->id }}</td>
                        <td>{{ $submission->created_at->format('M j, Y g:i A') }}</td>
                        @php
                            $fieldValues = $submission->fieldValues->keyBy('field_id');
                        @endphp
                        @foreach($fields as $field)
                            @php
                                $fieldValue = $fieldValues->get($field['id']);
                                $value = $fieldValue ? $fieldValue->value : '';

                                // Handle array values
                                if (is_array($value)) {
                                    $value = implode(', ', $value);
                                }
                            @endphp
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #999; margin-top: 40px;">No submissions found.</p>
    @endif

    <div class="footer">
        Exported from Slick Forms | {{ config('app.name') }}
    </div>
</body>
</html>
