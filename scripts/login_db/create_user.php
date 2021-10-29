$user = App\Models\User::where('email', env("APP_LOGIN_EMAIL"))->first();
if ($user == null) {
    $user = new App\Models\User();
    $user->password = Hash::make(env("APP_LOGIN_PASSWORD"));
    $user->email = env("APP_LOGIN_EMAIL");
    $user->name = 'Admin';
    $user->save();
}
