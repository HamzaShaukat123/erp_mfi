<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <p>Your OTP is: <strong>{{ $details['otp'] }}</strong></p>
    <p>User: <strong>{{ $details['username'] }}</p>
    <p>Browser: <strong>{{ $details['browser'] }} (Version: {{ $details['version'] }})</strong></p>
    <p>IP Address: <strong>{{ $details['ip'] }} </strong></p>
    <p>City: <strong>{{ $details['city'] }} </strong></p>
    <p>Region: <strong>{{ $details['region'] }} </strong></p>
    <p>Country: <strong>{{ $details['country'] }} </strong></p>
    <p>Location: <strong>{{ $details['location'] }} </strong></p>
    <p>This OTP will expire after 10 minutes</p>
</body>
</html>
