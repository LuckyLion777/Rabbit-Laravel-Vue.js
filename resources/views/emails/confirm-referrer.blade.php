<html>
<body>
    <h1>Hi, {{ $user->name }}!</h1>

    <p>Were you referred to our site by {{ $referrer->name }} <a href="mailto:{{ $referrer->email }}">{{ $referrer->email }}</a>? </p>
    <p>If so, please follow this link to confirm the referral and give credit to this user: <br>
		<a href="{{ $route = route('referrer.set', [
                                    'me' => $user->email,
                                    'email' => $referrer->email,
                                    'confirm' => $confirm
                                ]) }}">{{ $route }}</a>

    </p>
    <hr>
    <p>Login to view and manage Hutch: <a href="https://htch.us">https://htch.us</a></p>
    <br>
    <p>Thanks for choosing <a href="http://barntrax.com">Hutch</a></p>
</body>
</html>
