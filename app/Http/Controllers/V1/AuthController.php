<?php namespace App\Http\Controllers\V1;

use App\Mail\EmailConfirmation;
use App\Mail\EmailConfirmationEu;
use App\Mail\ResetPasswordMail;
use App\Mail\ResetPasswordMailEu;
use App\Models\MoneyWithdrawal;
use App\Models\RoundType;
use App\Models\User;
use App\Services\Response\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'codeSend', 'registration', 'emailVerified', 'sendResetLinkEmail', 'resetPassword']]);
    }

    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'lang' => 'nullable|in:ru,eu'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ResponseService::sendJsonResponse(
                false,
                404,
                ['We can\'t find a user with that e-mail address.'],
                []
            );
        }

        $rec = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();

        if($rec) {
            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->delete();
        }

        // Создаем и сохраняем токен сброса пароля в базе данных
        $token = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        $frontendUrl = env('FRONTEND_URL') . '/reset-password';
        $verifyUrl = URL::to("$frontendUrl?resetToken=$token");

        if ($request->get('lang') == 'eu') {
            Mail::to($user->email)->send(new ResetPasswordMailEu($verifyUrl));
        }
        else {
            Mail::to($user->email)->send(new ResetPasswordMail($verifyUrl));
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            ['We have emailed your password reset link!']
        );
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'resetToken' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $passwordReset = DB::table('password_reset_tokens')
            ->where('token', $request->get('resetToken'))
            ->first();

        if (!$passwordReset) {
            return ResponseService::sendJsonResponse(
                false,
                404,
                ['This password reset token is invalid.'],
                []
            );
        }

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            return ResponseService::sendJsonResponse(
                false,
                404,
                ['We can\'t find a user with that e-mail address.'],
                []
            );
        }

        $user->password = Hash::make($request->get('password'));
        $user->save();

        DB::table('password_reset_tokens')
            ->where('email', $passwordReset->email)
            ->delete();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            ['Password reset successfully!']
        );
    }

    public function codeSend(Request $request)
    {
        $request->validate([
            'phone' => ['required_without_all:email', 'integer', 'min:10000000000', 'max:99999999999999'],
            'email' => ['required_without_all:phone', 'email'],
            'lang' => 'nullable|in:ru,eu'
        ]);

        if($request->get('phone')) {
            $user = User::where([
                'phone' => $request->get('phone'),
            ])->first();

            if(!$user) {
                return ResponseService::sendJsonResponse(
                    false,
                    403,
                    ['Неверный телефон'],
                    []
                );
            }

            $user->code = rand(100000, 999999);
            $user->code_generated_at = Carbon::now();
            $user->save();
        }
        elseif($request->get('email')) {
            $user = User::where([
                'email' => $request->get('email'),
            ])->first();

            if(!$user) {
                return ResponseService::sendJsonResponse(
                    false,
                    403,
                    ['Неверный email'],
                    []
                );
            }
            else {
                $user->code = rand(100000, 999999);
                $user->code_generated_at = Carbon::now();
                $user->save();

                if ($request->get('lang') == 'eu') {
                    Mail::to($user->email)->send(new EmailConfirmationEu($user->code));
                }
                else {
                    Mail::to($user->email)->send(new EmailConfirmation($user->code));
                }
            }
        }

        return ResponseService::Success('Отправлено');
    }

    public function emailVerified(Request $request) {
        $request->validate([
            'code' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $user = User::where([
            'email' => $request->get('email'),
            'code' => $request->get('code')
        ])
            ->first();

        if(!$user) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Неверный код'],
                []
            );
        }
        if(Carbon::createFromTimeString($user->code_generated_at)->addMinutes(5) < Carbon::now()) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Код устарел'],
                []
            );
        }

        $user->email_verified_at = Carbon::now();
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            ['Email подтвержден']
        );
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => ['required_without_all:email,password', 'integer', 'min:10000000000', 'max:99999999999999'],
            'code' => ['required_without_all:email,password', 'string'],
            'email' => ['required_without_all:phone,code', 'email'],
            'password' => ['required_without_all:phone,code', 'string'],
        ]);

        if($request->get('phone')) {
            if(env('APP_ENV') == 'prod') {
                $user = User::where([
                    'phone' => $request->get('phone'),
                    'code' => $request->get('code')
                ])
                ->first();
            }
            else {
                $user = User::where([
                    'phone' => $request->get('phone'),
                ])
                ->first();
            }

            if(!$user) {
                return ResponseService::sendJsonResponse(
                    false,
                    403,
                    ['Неверный код'],
                    []
                );
            }
            if(Carbon::createFromTimeString($user->code_generated_at)->addMinutes(5) < Carbon::now()) {
                return ResponseService::sendJsonResponse(
                    false,
                    403,
                    ['Код устарел'],
                    []
                );
            }
        }
        elseif($request->get('email')) {
            $user = User::where([
                'email' => $request->get('email'),
            ])
                ->first();

            if(!$user || !Hash::check($request->get('password'), $user->password)) {
                return ResponseService::sendJsonResponse(
                    false,
                    403,
                    ['Неверный логин или пароль'],
                    []
                );
            }

            if($user->email_verified_at == null) {
                return ResponseService::sendJsonResponse(
                    false,
                    402,
                    ['Email не подтвержден'],
                    []
                );
            }
        }

        Auth::login($user);

        $user['referal_login'] = User::where('id', $user->referal_id)->first()->login ?? null;

        $user['can_money_request'] = MoneyWithdrawal::where('user_id', Auth::user()->id)->where('created_at', '>', Carbon::now()->subDays(7))->count() == 0;

        $user['menu'] = [
            'tickets' => $user->count_avatars,
            'balance_vbt' => $user->token_vesting + $user->token_stacking + $user->token_private,
            'tokens' => [
                'vesting' => $user->token_vesting,
                'stacking' => $user->token_stacking,
                'private' => $user->token_private,
            ],
        ];
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'user' => $user,
                'token' => $this->respondWithToken(Auth()->refresh())
            ]
        );
    }

    public function registration(Request $request)
    {
        $request->validate([
            'phone' => ['required_without:email', 'integer', 'unique:users', 'min:10000000000', 'max:99999999999999'],
            'email' => ['required_without:phone', 'email', 'unique:users'],
            'password' => ['required_with:email', 'string', 'confirmed', 'min:8', 'max:12'],
            'login' => ['required', 'string', 'unique:users'],
            'referal_invited' => ['string'],
            'lang' => 'nullable|in:ru,eu'
        ]);

        if(env('APP_ENV') == 'prod' && !$request->get('referal_invited') || !User::where('referal_invited', $request->get('referal_invited'))->first()) {
            return response()->json(['message' => 'Для регистрации необходимо приглашение'], 422);
        }

        $user = new User();

        if($request->get('referal_invited')){
            $user->referal_id = User::where('referal_invited', $request->get('referal_invited'))->first()->id;
        } else {
            $user->referal_id = 1;
        }


        $user->code = rand(100000, 999999);
        $user->code_generated_at = Carbon::now();
        if($request->get('email')) {
            $user->email = $request->get('email');
            $user->password = Hash::make($request->get('password'));
            $user->referal_invited = base64_encode($request->get('email'));

            if ($request->get('lang') == 'eu') {
                Mail::to($user->email)->send(new EmailConfirmationEu($user->code));
            }
            else {
                Mail::to($user->email)->send(new EmailConfirmation($user->code));
            }
        }
        elseif ($request->get('phone')) {
            $user->phone = $request->get('phone');
            $user->referal_invited = base64_encode($request->get('phone'));
        }
        $user->login = $request->get('login');
        $user->role_id = 1;
        $user->status_id = 1;
        $user->active_queue = 1;
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'user' => $user,
            ]
        );
    }

    public function me()
    {
        $user = auth()->user();

        $user['referal_login'] = User::where('id', $user->referal_id)->first()->login ?? null;
        $user['can_money_request'] = MoneyWithdrawal::where('user_id', Auth::user()->id)->where('created_at', '>', Carbon::now()->subDays(7))->count() == 0;

        $user['menu'] = [
            'tickets' => $user->count_avatars,
            'balance_vbt' => $user->token_vesting + $user->token_stacking + $user->token_private,
            'tokens' => [
                'vesting' => $user->token_vesting,
                'stacking' => $user->token_stacking,
                'private' => $user->token_private,
            ],
        ];

        return response()->json($user);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        $user = auth()->user();
        $round_types = RoundType::where('queue', $user->active_queue)->get();
        $ids = [];
        foreach ($round_types as $round_type) {
            $ids[] = $round_type->id;
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'token' => $this->respondWithToken(auth()->refresh()),
                'user' => $user,
            ]
        );
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 720
        ]);
    }
}
