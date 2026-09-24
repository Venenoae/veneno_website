<?php

use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\TechnicianPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdihexController;
use App\Http\Controllers\HammerChallengeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Clean Primary Storefront Routes (In-Place Multilingual - Zero 404s)
Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/services/{slug}', [StorefrontController::class, 'serviceDetail'])->name('service.detail');

// ADIHEX 2026 Campaign Portal & Digital Signage Kiosk
Route::get('/adihex', [AdihexController::class, 'index'])->name('adihex.index');
Route::get('/adihex/terms', [AdihexController::class, 'terms'])->name('adihex.terms');
Route::get('/adihex/display', [AdihexController::class, 'display'])->name('adihex.display');
Route::get('/adihex/screen', [AdihexController::class, 'display'])->name('adihex.screen');
Route::get('/{locale}/adihex', [AdihexController::class, 'index'])
    ->where('locale', 'en|ar');
Route::get('/{locale}/adihex/terms', [AdihexController::class, 'terms'])
    ->where('locale', 'en|ar');
Route::get('/{locale}/adihex/display', [AdihexController::class, 'display'])
    ->where('locale', 'en|ar');

// Hammer Challenge 2026 local event flow - Contestants
Route::get('/hammer-challenge', [HammerChallengeController::class, 'index'])->name('hammer-challenge.index');
Route::get('/hammer-challenge/register', [HammerChallengeController::class, 'index'])->name('hammer-challenge.register');
Route::get('/hammer-challenge/terms', [HammerChallengeController::class, 'terms'])->name('hammer-challenge.terms');
Route::get('/hammer-challenge/confirmation', [HammerChallengeController::class, 'confirmation'])->name('hammer-challenge.confirmation');

// Hammer Challenge Big Screen Display / QR Kiosk
Route::get('/hammer-challenge/display', [HammerChallengeController::class, 'display'])->name('hammer-challenge.display');
Route::get('/hammer-challenge/screen', [HammerChallengeController::class, 'display'])->name('hammer-challenge.screen');

// Hammer Challenge Audience & Visitor Registration Flow
Route::get('/hammer-challenge/audience', [HammerChallengeController::class, 'audience'])->name('hammer-challenge.audience');
Route::get('/hammer-challenge/visitor', [HammerChallengeController::class, 'audience'])->name('hammer-challenge.visitor');
Route::get('/hammer-challenge/audience/confirmation', [HammerChallengeController::class, 'audienceConfirmation'])->name('hammer-challenge.audience.confirmation');

// Multilingual URL prefix fallbacks (en | ar) for Hammer Challenge
Route::get('/{locale}/hammer-challenge', [HammerChallengeController::class, 'index'])
    ->where('locale', 'en|ar');
Route::get('/{locale}/hammer-challenge/register', [HammerChallengeController::class, 'index'])
    ->where('locale', 'en|ar');
Route::get('/{locale}/hammer-challenge/display', [HammerChallengeController::class, 'display'])
    ->where('locale', 'en|ar');
Route::get('/{locale}/hammer-challenge/screen', [HammerChallengeController::class, 'display'])
    ->where('locale', 'en|ar');
Route::get('/{locale}/hammer-challenge/audience', [HammerChallengeController::class, 'audience'])
    ->where('locale', 'en|ar');
Route::get('/{locale}/hammer-challenge/visitor', [HammerChallengeController::class, 'audience'])
    ->where('locale', 'en|ar');
Route::get('/{locale}/hammer-challenge/audience/confirmation', [HammerChallengeController::class, 'audienceConfirmation'])
    ->where('locale', 'en|ar');

// Multilingual URL prefix fallbacks (en | ar)
Route::get('/{locale}', [StorefrontController::class, 'home'])
    ->where('locale', 'en|ar');
Route::get('/{locale}/services/{slug}', [StorefrontController::class, 'serviceDetail'])
    ->where('locale', 'en|ar');

// API Routes
Route::post('/api/quote', [StorefrontController::class, 'submitQuote'])->name('api.quote.submit');
Route::post('/api/inquiries', [StorefrontController::class, 'storeInquiry'])->name('api.inquiries.store');

// ADIHEX 2026 Activation APIs (Public)
Route::post('/api/adihex/spin', [AdihexController::class, 'spin'])->name('api.adihex.spin');
Route::post('/api/adihex/reserve', [AdihexController::class, 'reserve'])->name('api.adihex.reserve');
Route::post('/api/adihex/payment-intent', [AdihexController::class, 'createPaymentIntent'])->name('api.adihex.payment-intent');

// Hammer Challenge Public Participant APIs
Route::post('/api/hammer-challenge/register', [HammerChallengeController::class, 'register'])->name('api.hammer-challenge.register');
Route::get('/api/hammer-challenge/confirmation/{token}', [HammerChallengeController::class, 'confirmationData'])->name('api.hammer-challenge.confirmation');
Route::post('/api/hammer-challenge/audience', [HammerChallengeController::class, 'registerAudience'])->name('api.hammer-challenge.audience');
Route::get('/api/hammer-challenge/audience/confirmation/{token}', [HammerChallengeController::class, 'audienceConfirmationData'])->name('api.hammer-challenge.audience.confirmation');

// Booking Engine
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/confirmation/{bookingCode}', [BookingController::class, 'confirmation'])->name('bookings.confirmation');

// Authentication Routes (Login Only - Public Registration Strictly Disabled)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Explicitly Block & Defend /register Against Unauthorized Account Creation
Route::any('/register', function () {
    if (request()->expectsJson()) {
        return response()->json(['message' => 'Public registration is disabled.'], 403);
    }
    return redirect()->route('login')->withErrors([
        'email' => 'Public registration is disabled. Staff and customer accounts are provisioned by administration.',
    ]);
})->name('register');

// Logout (Authenticated)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Portals & Dashboard CRM
Route::middleware(['auth'])->group(function () {
    // Customer VIP Portal (Restricted to Customers & Management)
    Route::middleware(['role:super_admin|manager|customer'])->group(function () {
        Route::get('/customer-portal', [CustomerPortalController::class, 'index'])->name('customer.portal');
    });

    // Technician Portal (Restricted to Bay Floor Staff & Management)
    Route::middleware(['role:super_admin|manager|technician'])->group(function () {
        Route::get('/technician-portal', [TechnicianPortalController::class, 'index'])->name('technician.portal');
        Route::post('/technician/bookings/{booking}/stage', [TechnicianPortalController::class, 'updateStage'])->name('technician.bookings.stage');
    });

    // Admin & Operations Management Dashboard CRM (Strictly Super Admin & Manager)
    Route::middleware(['role:super_admin|manager'])->prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/raffle', [HammerChallengeController::class, 'raffle'])->name('dashboard.raffle');
        Route::patch('/inquiries/{inquiry}', [DashboardController::class, 'updateInquiryStatus'])->name('dashboard.inquiries.update');
        Route::delete('/inquiries/{inquiry}', [DashboardController::class, 'destroyInquiry'])->name('dashboard.inquiries.destroy');
        Route::post('/campaigns', [DashboardController::class, 'storeCampaign'])->name('dashboard.campaigns.store');
        Route::get('/adihex/export', [AdihexController::class, 'exportLeads'])->name('dashboard.adihex.export');
        Route::patch('/hammer-challenge/{registration}/status', [DashboardController::class, 'updateHammerRegistrationStatus'])->name('dashboard.hammer-challenge.status');
        Route::get('/hammer-challenge/export', [DashboardController::class, 'exportHammerRegistrations'])->name('dashboard.hammer-challenge.export');
        Route::patch('/hammer-challenge/audience/{audience}/status', [DashboardController::class, 'updateHammerAudienceStatus'])->name('dashboard.hammer-challenge.audience.status');
        Route::post('/hammer-challenge/audience/{audience}/send-sms', [DashboardController::class, 'sendHammerWinnerSms'])->name('dashboard.hammer-challenge.audience.send-sms');
        Route::get('/hammer-challenge/audience/export', [DashboardController::class, 'exportHammerAudience'])->name('dashboard.hammer-challenge.audience.export');

        // Self Password Change (Requires current_password verification)
        Route::post('/change-password', [DashboardController::class, 'changePassword'])->name('dashboard.password.change');

        // Staff Password Reset (Strictly Super Admin ONLY - Cannot target Super Admins)
        Route::middleware(['role:super_admin'])->post('/users/{user}/reset-password', [DashboardController::class, 'resetUserPassword'])->name('dashboard.users.reset-password');
    });

    // Authenticated Management Actions for ADIHEX & Hammer Challenge
    Route::middleware(['role:super_admin|manager'])->group(function () {
        Route::post('/api/adihex/redeem', [AdihexController::class, 'redeemVoucher'])->name('api.adihex.redeem');
        Route::get('/api/hammer-challenge/raffle/participants', [HammerChallengeController::class, 'getRaffleParticipants'])->name('api.hammer-challenge.raffle.participants');
        Route::post('/api/hammer-challenge/raffle/draw', [HammerChallengeController::class, 'drawRaffleWinner'])->name('api.hammer-challenge.raffle.draw');
        Route::post('/api/hammer-challenge/raffle/reset', [HammerChallengeController::class, 'resetRaffleWinners'])->name('api.hammer-challenge.raffle.reset');
        Route::post('/api/hammer-challenge/raffle/{audience}/send-sms', [HammerChallengeController::class, 'sendRaffleWinnerSms'])->name('api.hammer-challenge.raffle.send-sms');
    });
});

