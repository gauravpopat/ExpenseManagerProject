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
        Forgot Password Code:
            {{$user['token']}}
        Regards,
        {{ env('MAIL_FROM_NAME') }}
    </pre>
    </h2>
</body>

</html>
