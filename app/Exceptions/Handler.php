use Illuminate\Session\TokenMismatchException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

public function register(): void
{
    $this->renderable(function (TokenMismatchException $e, Request $request) {
        return redirect()->route('login')
            ->with('error', 'Votre session a expiré. Veuillez vous reconnecter.');
    });

    $this->renderable(function (DecryptException $e, Request $request) {
        // When cookie decryption fails (usually APP_KEY mismatch or corrupted cookie),
        // clear the main session cookie and the XSRF token so the user can re-authenticate.
        $cookieNames = [
            config('session.cookie'),
            'XSRF-TOKEN',
        ];

        $response = redirect()->route('login')
            ->with('error', 'Problème d\'authentification lié aux cookies. Vos cookies ont été réinitialisés. Veuillez vous reconnecter.');

        foreach ($cookieNames as $name) {
            $response = $response->withCookie(Cookie::forget($name));
        }

        return $response;
    });
}