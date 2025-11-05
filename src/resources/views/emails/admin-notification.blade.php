<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .field-group {
            margin-bottom: 15px;
            padding: 10px;
            background-color: white;
            border-left: 3px solid #4CAF50;
        }
        .field-label {
            font-weight: bold;
            color: #555;
            font-size: 12px;
            text-transform: uppercase;
        }
        .field-value {
            margin-top: 5px;
            font-size: 14px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Form Submission</h1>
        <p>{{ $form->name }}</p>
    </div>

    <div class="content">
        <p><strong>Submission ID:</strong> {{ $submission->id }}</p>
        <p><strong>Submitted:</strong> {{ $submitted_at->format('F j, Y g:i A') }}</p>
        <p><strong>IP Address:</strong> {{ $ip_address }}</p>

        <hr style="margin: 20px 0;">

        <h2 style="margin-bottom: 15px;">Form Data</h2>

        @foreach($field_values as $fieldName => $fieldValue)
            <div class="field-group">
                <div class="field-label">{{ $fieldValue->field->label ?? ucfirst($fieldName) }}</div>
                <div class="field-value">{{ $fieldValue->value ?? 'N/A' }}</div>
            </div>
        @endforeach
    </div>

    <div class="footer">
        <p>This is an automated notification from {{ config('app.name') }}</p>
        <p>Powered by Slick Forms</p>
    </div>
</body>
</html>
