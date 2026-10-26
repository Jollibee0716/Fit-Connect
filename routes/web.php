<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuditController;

use App\Http\Controllers\AdminPeopleController;
use App\Http\Controllers\AdminDetailController;
use App\Http\Controllers\AdminStatusController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\AdminGymController;
use App\Http\Controllers\Admin\GymCatalogController;

use App\Http\Controllers\Owner\ClassBookingController;
use App\Http\Controllers\Owner\OwnerProfileController;
use App\Http\Controllers\OwnerProductController;
use App\Http\Controllers\OwnerOrderController;
use App\Http\Controllers\MemberProductController;
use App\Http\Controllers\OwnerAuditController;
use App\Http\Controllers\OwnerPlanController;

use App\Http\Controllers\ChatController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\AvatarMapController;

use App\Http\Controllers\Member\PortalController;
use App\Http\Controllers\Member\ProfileController as MemberProfileController;
use App\Http\Controllers\Member\XenditMembershipController;

use App\Http\Controllers\Owner\{
    TrainerController,
    ClassController,
    AttendanceCodeController
};

use App\Http\Controllers\Owner\AttendanceController as OwnerAttendanceController;
use App\Http\Controllers\Member\AttendanceController as MemberAttendanceController;

use App\Http\Middleware\EnsureUserIsActive;

use App\Http\Controllers\Owner\MemberController as OwnerMemberController;
use App\Http\Controllers\Owner\PaymentController as OwnerPaymentController;


/*
|--------------------------------------------------------------------------
| PUBLIC PAGES AND AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('home'))->name('home');

Route::get('/log_in', [
    AuthController::class,
    'showLogin'
])->name('login');

Route::post('/log_in', [
    AuthController::class,
    'login'
])->middleware('throttle:5,1')->name('login.submit');

Route::get('/two-factor', [
    TwoFactorController::class,
    'show'
])->name('two-factor.show');

Route::post('/two-factor', [
    TwoFactorController::class,
    'verify'
])->middleware('throttle:10,1')->name('two-factor.verify');

Route::post('/two-factor/resend', [
    TwoFactorController::class,
    'resend'
])->middleware('throttle:3,1')->name('two-factor.resend');

Route::post('/register', [
    AuthController::class,
    'register'
])->middleware('throttle:10,1')->name('register');

Route::post('/logout', [
    AuthController::class,
    'logout'
])->name('logout');

Route::get('/forgot-password', [
    AuthController::class,
    'showForgotPassword'
])->name('password.request');

Route::post('/forgot-password', [
    AuthController::class,
    'sendResetLink'
])->middleware('throttle:5,1')->name('password.email');

Route::get('/reset-password/{token}', fn ($token) =>
    view('reset-password', [
        'token' => $token,
        'email' => request('email'),
    ])
)->name('password.reset');

Route::post('/reset-password', [
    AuthController::class,
    'resetPassword'
])->middleware('throttle:5,1')->name('password.update');


/*
|--------------------------------------------------------------------------
| XENDIT WEBHOOK — PUBLIC ENDPOINT (TOKEN VERIFIED IN CONTROLLER)
|--------------------------------------------------------------------------
|
| Xendit cannot authenticate using a member session or browser CSRF token.
| Exclude only this URI from CSRF validation in bootstrap/app.php.
| Never trust the request without verifying x-callback-token in the controller.
|
| The combined controller routes membership sessions to the membership
| webhook and everything else to the existing product webhook.
|
*/
Route::post('/webhooks/xendit', [
    \App\Http\Controllers\XenditCombinedWebhookController::class,
    'handle',
])->name('xendit.webhook');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED AND ACTIVE USERS
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    EnsureUserIsActive::class
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | MAIN DASHBOARDS
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->name('dashboard');

    Route::get('/admin/dashboard', [
        DashboardController::class,
        'admin'
    ])
        ->middleware('role:admin')
        ->name('admin.dashboard');

    Route::get('/coach/dashboard', [
        DashboardController::class,
        'coach'
    ])
        ->middleware('role:gym_owner,admin')
        ->name('coach.dashboard');

    Route::get('/member/dashboard', [
        DashboardController::class,
        'member'
    ])
        ->middleware('role:member')
        ->name('member.dashboard');


    /*
    |--------------------------------------------------------------------------
    | ADMIN PROFILE AND AUDIT
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {

        Route::patch('/profile', [
            ProfileController::class,
            'update'
        ])->name('profile.update');

        Route::put('/profile/password', [
            ProfileController::class,
            'updatePassword'
        ])
            ->middleware('throttle:5,1')
            ->name('profile.password');

        Route::get('/api/audit', [
            AuditController::class,
            'index'
        ])->name('audit.index');

    });


    /*
    |--------------------------------------------------------------------------
    | ADMIN MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')
        ->prefix('api/admin')
        ->group(function () {

            Route::get('people', [
                AdminPeopleController::class,
                'index'
            ]);

            Route::get('avatars', [
                AvatarMapController::class,
                'admin'
            ]);

            Route::get('gyms', [
                AdminGymController::class,
                'index'
            ]);

            Route::get('gyms/{gym}/catalog', [
                GymCatalogController::class,
                'index'
            ])->whereNumber('gym');

            Route::patch('coaches/{user}/approval', [
                AdminPeopleController::class,
                'approval'
            ]);

            Route::patch('users/{user}/status', [
                AdminStatusController::class,
                'update'
            ]);

            Route::delete('users/{user}', [
                AdminPeopleController::class,
                'destroy'
            ]);

            Route::get('users/{id}/details', [
                AdminDetailController::class,
                'show'
            ]);

        });


    /*
    |--------------------------------------------------------------------------
    | GYM OWNER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:gym_owner')
        ->prefix('api/owner')
        ->group(function () {

            // Audit
            Route::get('audit', [
                OwnerAuditController::class,
                'index'
            ]);

            // Gym Owner Profile
            Route::get('profile', [
                OwnerProfileController::class,
                'show'
            ]);

            Route::put('profile', [
                OwnerProfileController::class,
                'updateProfile'
            ]);

            Route::put('gym', [
                OwnerProfileController::class,
                'updateGym'
            ]);

            Route::put('password', [
                OwnerProfileController::class,
                'updatePassword'
            ])->middleware('throttle:5,1');

            Route::get('avatars', [
                AvatarMapController::class,
                'owner'
            ]);

            // Products
            Route::get('products', [
                OwnerProductController::class,
                'index'
            ]);

            Route::post('products', [
                OwnerProductController::class,
                'store'
            ]);

            Route::put('products/{product}', [
                OwnerProductController::class,
                'update'
            ]);

            Route::delete('products/{product}', [
                OwnerProductController::class,
                'destroy'
            ]);

            Route::post('products/{product}/sell', [
                OwnerProductController::class,
                'sell'
            ]);

            // Pickup and Delivery Orders
            Route::get('orders', [
                OwnerOrderController::class,
                'index'
            ]);

            Route::patch('orders/{order}', [
                OwnerOrderController::class,
                'update'
            ]);

            // Membership Payments
            Route::get('payments', [
                OwnerPaymentController::class,
                'index'
            ]);

            Route::patch('payments/{id}', [
                OwnerPaymentController::class,
                'update'
            ])->whereNumber('id');

            // Membership Plans
            Route::get('plans', [
                OwnerPlanController::class,
                'index'
            ]);

            Route::post('plans', [
                OwnerPlanController::class,
                'store'
            ]);

            Route::put('plans/{id}', [
                OwnerPlanController::class,
                'update'
            ]);

            Route::delete('plans/{id}', [
                OwnerPlanController::class,
                'destroy'
            ]);

            // Class booking management (Gym Owner)
            Route::get('classes/{class}/bookings', [
                ClassBookingController::class,
                'index'
            ])->whereNumber('class');

            Route::patch('class-bookings/{booking}', [
                ClassBookingController::class,
                'update'
            ])->whereNumber('booking');

            // Trainers and Classes
            Route::get('classes/today', [
                AttendanceCodeController::class,
                'today'
            ]);

            Route::apiResource(
                'trainers',
                TrainerController::class
            )->except('show');

            Route::apiResource(
                'classes',
                ClassController::class
            )->except('show');

            Route::post('classes/{class}/code', [
                AttendanceCodeController::class,
                'generate'
            ]);

            Route::delete('codes/{code}', [
                AttendanceCodeController::class,
                'revoke'
            ]);

            // Attendance
            Route::get('attendance', [
                OwnerAttendanceController::class,
                'index'
            ]);

            Route::post('attendance', [
                OwnerAttendanceController::class,
                'store'
            ]);

            Route::delete('attendance/{attendance}', [
                OwnerAttendanceController::class,
                'destroy'
            ]);

            // Members
            Route::get('members', [
                OwnerMemberController::class,
                'index'
            ]);

            Route::delete('members/{id}', [
                OwnerMemberController::class,
                'destroy'
            ])->whereNumber('id');

        });


    /*
    |--------------------------------------------------------------------------
    | MEMBER SHOP, ATTENDANCE AND PROFILE
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:member')
        ->prefix('api/member')
        ->group(function () {

            // Existing product endpoints
            Route::get('products', [
                MemberProductController::class,
                'index'
            ]);

            Route::post('orders', [
                MemberProductController::class,
                'order'
            ]);

            Route::get('orders', [
                MemberProductController::class,
                'mine'
            ]);

            // Xendit: authenticated member creates checkout for their own order.
            Route::post('xendit/checkout', [
                \App\Http\Controllers\Member\XenditCheckoutController::class,
                'store'
            ])->middleware('throttle:10,1');

            // Xendit: membership checkout and server-side verification.
            Route::post('xendit/membership/checkout', [
                XenditMembershipController::class,
                'checkout'
            ])->middleware('throttle:10,1');

            Route::post('xendit/membership/verify', [
                XenditMembershipController::class,
                'verify'
            ])->middleware('throttle:10,1');

            // Membership Payments (now always go through Xendit checkout;
            // the old manual MemberPaymentController@payMembership is retired).
            Route::post('membership-payments', [
                XenditMembershipController::class,
                'checkout'
            ])->middleware('throttle:10,1');

            // Member Classes and Attendance
            Route::get('classes', [
                MemberAttendanceController::class,
                'classes'
            ]);

            // Member class reservations
            Route::get('class-bookings', [
                MemberAttendanceController::class,
                'bookings'
            ]);

            Route::post('class-bookings', [
                MemberAttendanceController::class,
                'book'
            ])->middleware('throttle:15,1');

            Route::patch('class-bookings/{booking}/cancel', [
                MemberAttendanceController::class,
                'cancelBooking'
            ])->whereNumber('booking')->middleware('throttle:15,1');

            Route::get('attendance/history', [
                MemberAttendanceController::class,
                'history'
            ]);

            Route::post('attendance/check-in', [
                MemberAttendanceController::class,
                'checkIn'
            ])->middleware('throttle:8,1');


            /*
            |--------------------------------------------------------------------------
            | MEMBER PORTAL
            |--------------------------------------------------------------------------
            */

            Route::prefix('portal')->group(function () {

                // Explore registered gyms
                Route::get('gyms', [
                    PortalController::class,
                    'gyms'
                ]);

                // Select a gym
                Route::post('select-gym', [
                    PortalController::class,
                    'selectGym'
                ]);

                // Gym and membership overview
                Route::get('overview', [
                    PortalController::class,
                    'overview'
                ]);

                // Gym catalog
                Route::get('catalog', [
                    PortalController::class,
                    'catalog'
                ]);

                // Member product orders
                Route::get('orders', [
                    PortalController::class,
                    'orders'
                ]);

                Route::post('orders', [
                    PortalController::class,
                    'placeOrder'
                ]);

                // Membership payment history
                Route::get('payments', [
                    PortalController::class,
                    'payments'
                ]);


                /*
                |--------------------------------------------------------------------------
                | MEMBER PROFILE
                |--------------------------------------------------------------------------
                */

                Route::get('profile', [
                    MemberProfileController::class,
                    'show'
                ]);

                Route::put('profile', [
                    MemberProfileController::class,
                    'update'
                ]);

                Route::put('profile/password', [
                    MemberProfileController::class,
                    'updatePassword'
                ])->middleware('throttle:5,1');

            });

        });


    /*
    |--------------------------------------------------------------------------
    | PROFILE AVATARS
    |--------------------------------------------------------------------------
    */

    Route::post('api/profile/avatar', [
        AvatarController::class,
        'store'
    ])
        ->middleware('throttle:10,1')
        ->name('avatar.store');

    Route::delete('api/profile/avatar', [
        AvatarController::class,
        'destroy'
    ])->name('avatar.destroy');


    /*
    |--------------------------------------------------------------------------
    | SHARED MESSAGING
    |--------------------------------------------------------------------------
    */

    Route::prefix('api/chat')->group(function () {

        // Available contacts
        Route::get('contacts', [
            ChatController::class,
            'contacts'
        ]);

        // Conversation history
        Route::get('{user}', [
            ChatController::class,
            'thread'
        ])->whereNumber('user');

        // Send message
        Route::post('{user}', [
            ChatController::class,
            'send'
        ])
            ->whereNumber('user')
            ->middleware('throttle:30,1');

    });

});