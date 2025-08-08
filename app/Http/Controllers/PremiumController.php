<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PremiumController extends Controller
{
    /**
     * Show premium upgrade page for place managers
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('placeManager')) {
            abort(403, 'Only place managers can access premium features.');
        }
        
        $place = Place::where('user_id', $user->id)->first();
        
        if (!$place) {
            return redirect()->route('placemanager.place.create')
                ->with('info', 'Please create your place first before upgrading to premium.');
        }
        
        return Inertia::render('premium/Index', [
            'place' => [
                'id' => $place->id,
                'name' => $place->name,
                'is_premium' => $place->isPremium(),
                'premium_expires_at' => $place->premium_expires_at,
                'premium_trial_used' => $place->premium_trial_used,
                'days_until_expiry' => $place->daysUntilPremiumExpires(),
                'is_eligible_for_trial' => $place->isEligibleForFreeTrial(),
                'is_expired' => $place->isPremiumExpired(),
            ],
            'pricing' => [
                [
                    'id' => '1month',
                    'name' => '1 Month Premium',
                    'price' => 99,
                    'duration' => 1,
                    'features' => [
                        'Priority listing in search results',
                        'Featured placement on homepage',
                        'Advanced analytics dashboard',
                        'Custom branding options',
                        'Priority customer support'
                    ]
                ],
                [
                    'id' => '6months',
                    'name' => '6 Months Premium',
                    'price' => 499,
                    'duration' => 6,
                    'discount' => 17,
                    'features' => [
                        'All 1-month features',
                        '17% discount',
                        'Extended analytics history',
                        'Bulk event management tools'
                    ]
                ],
                [
                    'id' => '12months',
                    'name' => '12 Months Premium',
                    'price' => 899,
                    'duration' => 12,
                    'discount' => 25,
                    'features' => [
                        'All previous features',
                        '25% discount',
                        'Annual performance reports',
                        'Dedicated account manager',
                        'API access for integrations'
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * Start free trial for eligible places
     */
    public function startFreeTrial(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('placeManager')) {
            abort(403, 'Only place managers can start premium trials.');
        }
        
        $place = Place::where('user_id', $user->id)->first();
        
        if (!$place) {
            return back()->withErrors(['place' => 'Place not found.']);
        }
        
        if (!$place->isEligibleForFreeTrial()) {
            return back()->withErrors(['trial' => 'You have already used your free trial.']);
        }
        
        $place->startFreeTrial();
        
        return redirect()->route('premium.index')
            ->with('success', 'Congratulations! Your 2-month free premium trial has started.');
    }
    
    /**
     * Process premium purchase
     */
    public function purchase(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('placeManager')) {
            abort(403, 'Only place managers can purchase premium.');
        }
        
        $validated = $request->validate([
            'plan' => 'required|in:1month,6months,12months',
        ]);
        
        $place = Place::where('user_id', $user->id)->first();
        
        if (!$place) {
            return back()->withErrors(['place' => 'Place not found.']);
        }
        
        // Map plan to months
        $months = match($validated['plan']) {
            '1month' => 1,
            '6months' => 6,
            '12months' => 12,
        };
        
        // In a real app, you would process payment here
        // For now, we'll just extend premium
        $place->extendPremium($months);
        
        return redirect()->route('premium.index')
            ->with('success', "Premium subscription extended for {$months} month(s)!");
    }
}
