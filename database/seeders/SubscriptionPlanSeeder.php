<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Uruchom seeder planów subskrypcyjnych.
     */
    public function run(): void
    {
        // Usuń istniejące plany (bezpieczne usunięcie z uwzględnieniem kluczy obcych)
        SubscriptionPlan::query()->delete();

        // Plan darmowy - miesięczny
        SubscriptionPlan::create([
            'name' => 'Darmowy',
            'slug' => 'free-monthly',
            'description' => 'Podstawowy plan dla początkujących',
            'price' => 0.00,
            'billing_period' => 'monthly',
            'max_listings' => 1,
            'features' => [
                'basic_profile' => 'Podstawowy profil',
                'basic_search' => 'Podstawowe wyszukiwanie',
                'contact_info' => 'Informacje kontaktowe'
            ],
            'is_popular' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Plan darmowy - roczny
        SubscriptionPlan::create([
            'name' => 'Darmowy',
            'slug' => 'free-yearly',
            'description' => 'Podstawowy plan dla początkujących',
            'price' => 0.00,
            'billing_period' => 'yearly',
            'max_listings' => 1,
            'features' => [
                'basic_profile' => 'Podstawowy profil',
                'basic_search' => 'Podstawowe wyszukiwanie',
                'contact_info' => 'Informacje kontaktowe'
            ],
            'is_popular' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Plan Starter - miesięczny
        SubscriptionPlan::create([
            'name' => 'Starter',
            'slug' => 'starter-monthly',
            'description' => 'Idealny dla osób okazjonalnie szukających opieki',
            'price' => 29.00,
            'billing_period' => 'monthly',
            'max_listings' => 3,
            'features' => [
                'extended_profile' => 'Rozszerzony profil',
                'priority_search' => 'Priorytet w wyszukiwaniu',
                'photo_gallery' => 'Galeria zdjęć (5 zdjęć)',
                'basic_support' => 'Wsparcie email'
            ],
            'is_popular' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Plan Starter - roczny (20% rabatu)
        SubscriptionPlan::create([
            'name' => 'Starter',
            'slug' => 'starter-yearly',
            'description' => 'Idealny dla osób okazjonalnie szukających opieki - OSZCZĘDŹ 20%!',
            'price' => 278.40, // 29 * 12 * 0.8 = 278.40 (20% rabatu)
            'billing_period' => 'yearly',
            'max_listings' => 3,
            'features' => [
                'extended_profile' => 'Rozszerzony profil',
                'priority_search' => 'Priorytet w wyszukiwaniu',
                'photo_gallery' => 'Galeria zdjęć (5 zdjęć)',
                'basic_support' => 'Wsparcie email',
                'yearly_discount' => '20% rabatu przy płatności rocznej'
            ],
            'is_popular' => false,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Plan Pro - miesięczny
        SubscriptionPlan::create([
            'name' => 'Pro',
            'slug' => 'pro-monthly',
            'description' => 'Najlepszy wybór dla aktywnych użytkowników',
            'price' => 49.00,
            'billing_period' => 'monthly',
            'max_listings' => 10,
            'features' => [
                'premium_profile' => 'Profil premium z wyróżnieniem',
                'unlimited_search' => 'Nielimitowane wyszukiwanie',
                'photo_gallery' => 'Galeria zdjęć (15 zdjęć)',
                'priority_support' => 'Priorytetowe wsparcie',
                'advanced_filters' => 'Zaawansowane filtry',
                'booking_calendar' => 'Kalendarz rezerwacji'
            ],
            'is_popular' => true,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // Plan Pro - roczny (25% rabatu)
        SubscriptionPlan::create([
            'name' => 'Pro',
            'slug' => 'pro-yearly',
            'description' => 'Najlepszy wybór dla aktywnych użytkowników - OSZCZĘDŹ 25%!',
            'price' => 441.00, // 49 * 12 * 0.75 = 441.00 (25% rabatu)
            'billing_period' => 'yearly',
            'max_listings' => 10,
            'features' => [
                'premium_profile' => 'Profil premium z wyróżnieniem',
                'unlimited_search' => 'Nielimitowane wyszukiwanie',
                'photo_gallery' => 'Galeria zdjęć (15 zdjęć)',
                'priority_support' => 'Priorytetowe wsparcie',
                'advanced_filters' => 'Zaawansowane filtry',
                'booking_calendar' => 'Kalendarz rezerwacji',
                'yearly_discount' => '25% rabatu przy płatności rocznej'
            ],
            'is_popular' => true,
            'is_active' => true,
            'sort_order' => 5,
        ]);

        // Plan Business - miesięczny
        SubscriptionPlan::create([
            'name' => 'Business',
            'slug' => 'business-monthly',
            'description' => 'Dla profesjonalnych opiekunów i firm',
            'price' => 99.00,
            'billing_period' => 'monthly',
            'max_listings' => null, // Unlimited
            'features' => [
                'business_profile' => 'Profil biznesowy z logo',
                'unlimited_everything' => 'Nielimitowane wszystko',
                'photo_gallery' => 'Nielimitowana galeria zdjęć',
                'premium_support' => '24/7 Premium wsparcie',
                'advanced_analytics' => 'Zaawansowane statystyki',
                'api_access' => 'Dostęp do API',
                'custom_branding' => 'Własny branding'
            ],
            'is_popular' => false,
            'is_active' => true,
            'sort_order' => 6,
        ]);

        // Plan Business - roczny (30% rabatu)
        SubscriptionPlan::create([
            'name' => 'Business',
            'slug' => 'business-yearly',
            'description' => 'Dla profesjonalnych opiekunów i firm - OSZCZĘDŹ 30%!',
            'price' => 831.60, // 99 * 12 * 0.7 = 831.60 (30% rabatu)
            'billing_period' => 'yearly',
            'max_listings' => null, // Unlimited
            'features' => [
                'business_profile' => 'Profil biznesowy z logo',
                'unlimited_everything' => 'Nielimitowane wszystko',
                'photo_gallery' => 'Nielimitowana galeria zdjęć',
                'premium_support' => '24/7 Premium wsparcie',
                'advanced_analytics' => 'Zaawansowane statystyki',
                'api_access' => 'Dostęp do API',
                'custom_branding' => 'Własny branding',
                'yearly_discount' => '30% rabatu przy płatności rocznej'
            ],
            'is_popular' => false,
            'is_active' => true,
            'sort_order' => 7,
        ]);
    }
}
