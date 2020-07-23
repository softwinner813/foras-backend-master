<html>
    <head>
        <title>Verify Email</title>
    </head>
    <body>
        <h2>Welcome to Foras, {{$user['name']}}</h2>
        <br>
        Your registered email is {{$user['email']}} , Please click on the below link to verify your email account
        <br>
        <!-- <a href="{{ url('auth/verify/email', $user['email_verify_token'], $user['id'], strtotime('+10 minutes') ) }}">Verify Email</a> -->
        <a href="{{url('api/auth/verify/email')}}/{{$user['email_verify_token']}}/{{$user['id']}}/{{strtotime('+10 minutes')}}">Verify Email</a>
    </body>
</html>