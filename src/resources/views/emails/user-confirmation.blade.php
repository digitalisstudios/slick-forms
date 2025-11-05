<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Submission</title>
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
            background-color: #2196F3;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px 20px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .summary-box {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #e0e0e0;
        }
        .field-row {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .field-row:last-child {
            border-bottom: none;
        }
        .field-label {
            font-weight: 600;
            color: #555;
        }
        .field-value {
            color: #333;
            margin-left: 10px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Thank You!</h1>
        <p>Your submission has been received</p>
    </div>

    <div class="content">
        <p>Dear user,</p>

        <p>Thank you for submitting <strong>{{ $form->name }}</strong>. We have successfully received your information and will review it shortly.</p>

        <div class="summary-box">
            <h3 style="margin-top: 0;">Submission Summary</h3>
            <div class="field-row">
                <span class="field-label">Submission ID:</span>
                <span class="field-value">{{ $submission->id }}</span>
            </div>
            <div class="field-row">
                <span class="field-label">Submitted:</span>
                <span class="field-value">{{ $submitted_at->format('F j, Y g:i A') }}</span>
            </div>
        </div>

        <h3>Your Submitted Information</h3>

        @foreach($field_values as $fieldName => $fieldValue)
            @if($fieldValue->field && !in_array($fieldValue->field->type, ['password', 'hidden']))
                <div class="field-row">
                    <span class="field-label">{{ $fieldValue->field->label ?? ucfirst($fieldName) }}:</span>
                    <span class="field-value">{{ $fieldValue->value ?? 'N/A' }}</span>
                </div>
            @endif
        @endforeach

        <p style="margin-top: 20px;">If you have any questions, please don't hesitate to contact us.</p>
    </div>

    <div class="footer">
        <p>This is an automated confirmation from {{ config('app.name') }}</p>
        <p>Please do not reply to this email</p>
        <p>Powered by Slick Forms</p>
    </div>
</body>
</html>
