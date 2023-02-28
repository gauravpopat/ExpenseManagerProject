<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>
    <h2>
        <pre>
        Hello {{ $user['first_name'] }},
        Thank you for joining us...
        Here your accounts details:
            <i>Account Name : </i>{{ $user['account_name'] }}
            <i>Account Number : </i>{{ $user['account_number'] }}
        Regards,
        {{ env('MAIL_FROM_NAME') }}
    </pre>
    </h2>
</body>

</html>
