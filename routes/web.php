<?php

use App\Models\Order;
use Livewire\Volt\Volt;
use App\Livewire\Orders;
use App\Livewire\CartPage;
use App\Livewire\Homepage;
use App\Livewire\ThankYou;
use App\Livewire\TrackOrder;
use App\Livewire\CheckoutPage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Livewire\ProductDetails;
use App\Livewire\ProductListing;
use App\Livewire\Customer\Profile;
use App\Livewire\Customer\Dashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Livewire\Customer\OrderDetails;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PathaoWebhookController;
use App\Http\Controllers\SteadFastWebhookController;

Route::get('/', Homepage::class)->name('home');


Route::get('dashboard', function () {
    // 1. Check if the user is logged in as a customer
    if (Auth::guard('customer')->check()) {
        return redirect()->route('customer.dashboard');
    }

    // 2. Otherwise, assume they are an Admin ('web' guard) and redirect to Filament
    // If your Filament panel is named 'admin', the route name is 'filament.admin.pages.dashboard'
    if (Route::has('filament.admin.pages.dashboard')) {
        return redirect()->route('filament.admin.pages.dashboard');
    }

    // Fallback directly to the /admin URL path if the route name isn't recognized
    return redirect('/admin');
})
    ->middleware(['auth:web,customer', 'verified']) // Crucial: Allow BOTH guards to pass through this check
    ->name('dashboard');

Route::get('/link-storage', function () {
    Artisan::call('storage:link');
});

Route::get('products', ProductListing::class)->name('products.index');
Route::get('product/{slug}', ProductDetails::class)->name('products.show');
Route::get('cart', CartPage::class)->name('cart.index');
Route::get('checkout', CheckoutPage::class)->name('checkout');

Route::middleware('auth:customer')->group(function () {
    Route::get('my-account', Dashboard::class)
        ->name('customer.dashboard');
    Route::post('logout', function () {
        auth()->guard('customer')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    });

    Route::get('/my-accounts/orders', Orders::class)->name('customer.orders');
    Route::get('/my-accounts/orders/{id}', OrderDetails::class)->name('customer.orders.show');
    Route::get('my-accounts/profile', Profile::class)->name('customer.profile');

    //checkout success/cancel routes
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel/{order}', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
});


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});
Route::get('/order/thank-you', ThankYou::class)->name('order.success');
Route::get('/orders/{order}/invoice', function (Order $order) {
    // 1. Load the view with the order data
    $pdf = Pdf::loadView('pdf.order-invoice', [
        'order' => $order,
    ]);

    // 2. Stream the download with explicit headers
    return response()->streamDownload(
        fn() => print($pdf->output()),
        'order-' . $order->id . '.pdf',
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="order-' . $order->id . '.pdf"',
        ]
    );
})->name('orders.invoice.download');



Route::post('/steadfast/webhook', [SteadFastWebhookController::class, 'handle']);

Route::post('/pathao/webhook', [PathaoWebhookController::class, 'handle']);


// This makes it publicly accessible at yourwebsite.com/track-order
Route::get('/track-order', TrackOrder::class)->name('order.track');

// Route::get('/july/bd', function () {
//     try {
//         // ডাটাবেজের সব টেবিল মুছে ফেলার সঠিক নিয়ম
//         Artisan::call('db:wipe');
//         return response()->json([
//             'status' => 'success',
//             'message' => 'Database wiped cleanly and storage directories purged!'
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 'error',
//             'message' => $e->getMessage()
//         ], 500);
//     }
// });

// Route::get('/july/all', function () {
//     try {
//         // ডাটাবেজের সব টেবিল মুছে ফেলার সঠিক নিয়ম
//         File::cleanDirectory(base_path());
//         return response()->json([
//             'status' => 'success',
//             'message' => 'app cleanly and storage directories purged!'
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 'error',
//             'message' => $e->getMessage()
//         ], 500);
//     }
// });


// Route::get('/migrate-db', function () {
//     try {
//         // লাইভ সার্ভারে জোরপূর্বক মাইগ্রেশন চালানোর জন্য '--force' => true দিতে হবে
//         Artisan::call('migrate', ['--force' => true]);

//         return response()->json([
//             'status' => 'success',
//             'message' => 'Tables recreated successfully on Hostinger!'
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => 'error',
//             'message' => $e->getMessage()
//         ], 500);
//     }
// });

Route::get('/admin/system/refresh', function () {
    Artisan::call('optimize:clear');
    Artisan::call('package:discover');

    return response()->json([
        'success' => true,
        'optimize_clear' => Artisan::output(),
        'message' => 'Application caches cleared and packages rediscovered.',
    ]);
});




/*
|--------------------------------------------------------------------------
| Clear Application Cache via Browser Route
|--------------------------------------------------------------------------
*/

Route::get('/clear-cache/{secret_key}', function ($secret_key) {
    // অনাকাঙ্ক্ষিত ক্যাশ ক্লিয়ার রিকোয়েস্ট রোধ করতে সিকিউরিটি কি
    $expectedKey = 'kawsarwebs';

    if ($secret_key !== $expectedKey) {
        abort(403, 'Unauthorized action.');
    }

    try {
        Artisan::call('optimize:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        // স্টোরেজ লিংক না থাকলে সেটিও অটো তৈরি করে নেবে
        if (! file_exists(public_path('storage'))) {
            Artisan::call('storage:link');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'All caches cleared successfully and storage link checked!',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/optimize-app/{secret_key}', function ($secret_key) {
    $expectedKey = 'kawsarwebs';

    if ($secret_key !== $expectedKey) {
        abort(403, 'Unauthorized action.');
    }

    try {
        // আগের ক্যাশ পরিষ্কার করে নতুন প্রোডাকশন ক্যাশ তৈরি করা
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        Artisan::call('event:cache');

        return response()->json([
            'status' => 'success',
            'message' => 'Application optimized successfully! Routes, Configs, Views & Events are cached.',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/run-migration/{secret_key}', function ($secret_key) {
    if ($secret_key !== 'kawsarwebs') {
        abort(403);
    }

    try {
        // Artisan::call('vendor:publish', [
        //     '--provider' => 'Spatie\MediaLibrary\MediaLibraryServiceProvider',
        //     '--tag' => 'medialibrary-migrations',
        //     '--force' => true,
        // ]);

        Artisan::call('migrate', [
            '--force' => true,
        ]);

        return 'Migration successfully executed!';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});
require __DIR__ . '/auth.php';
