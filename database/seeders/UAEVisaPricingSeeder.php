<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Emirates;
use App\Models\UAEVisaPackage;
use App\Models\UAEVisaPrice;

class UAEVisaPricingSeeder extends Seeder
{
    public function run(): void
    {
        // Get seeded Emirates
        $dubai = Emirates::where('emiratesName', 'Dubai')->first();
        $sharjah = Emirates::where('emiratesName', 'Sharjah')->first();

        // 1. Dubai Packages
        if ($dubai) {
            // Tourist Visa Package
            $tourist = UAEVisaPackage::create([
                'emirates_id' => $dubai->emiratesID,
                'name' => 'Standard Tourist Visa',
                'description' => 'Standard entry visa processed in 3-5 working days.',
                'isActive' => 1,
                'company_id' => 1,
            ]);

            // Tourist Single Entry 30 Days
            UAEVisaPrice::create([
                'visa_package_id' => $tourist->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Adult',
                'price' => 350.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $tourist->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Child',
                'price' => 300.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $tourist->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Infant',
                'price' => 100.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);

            // Tourist Single Entry 60 Days
            UAEVisaPrice::create([
                'visa_package_id' => $tourist->id,
                'entry_type' => 'Single Entry',
                'duration' => '60 Days',
                'traveller_type' => 'Adult',
                'price' => 650.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $tourist->id,
                'entry_type' => 'Single Entry',
                'duration' => '60 Days',
                'traveller_type' => 'Child',
                'price' => 600.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $tourist->id,
                'entry_type' => 'Single Entry',
                'duration' => '60 Days',
                'traveller_type' => 'Infant',
                'price' => 150.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);

            // Express Visa Package
            $express = UAEVisaPackage::create([
                'emirates_id' => $dubai->emiratesID,
                'name' => 'Express Tourist Visa',
                'description' => 'Fast-track entry visa processed within 24 hours.',
                'isActive' => 1,
                'company_id' => 1,
            ]);

            UAEVisaPrice::create([
                'visa_package_id' => $express->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Adult',
                'price' => 500.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $express->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Child',
                'price' => 450.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $express->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Infant',
                'price' => 200.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
        }

        // 2. Sharjah Packages
        if ($sharjah) {
            $sharjahTour = UAEVisaPackage::create([
                'emirates_id' => $sharjah->emiratesID,
                'name' => 'Sharjah Entry Visa',
                'description' => 'Standard tourist entry visa for Sharjah travel.',
                'isActive' => 1,
                'company_id' => 1,
            ]);

            UAEVisaPrice::create([
                'visa_package_id' => $sharjahTour->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Adult',
                'nationality' => null,
                'price' => 340.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $sharjahTour->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Child',
                'nationality' => null,
                'price' => 290.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $sharjahTour->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Infant',
                'nationality' => null,
                'price' => 90.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);

            // India specific pricing
            UAEVisaPrice::create([
                'visa_package_id' => $sharjahTour->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Adult',
                'nationality' => 'India',
                'price' => 350.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $sharjahTour->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Child',
                'nationality' => 'India',
                'price' => 300.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $sharjahTour->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Infant',
                'nationality' => 'India',
                'price' => 100.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);

            // Pakistan specific pricing
            UAEVisaPrice::create([
                'visa_package_id' => $sharjahTour->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Adult',
                'nationality' => 'Pakistan',
                'price' => 450.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $sharjahTour->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Child',
                'nationality' => 'Pakistan',
                'price' => 400.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $sharjahTour->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Infant',
                'nationality' => 'Pakistan',
                'price' => 150.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);

            // United Kingdom specific pricing
            UAEVisaPrice::create([
                'visa_package_id' => $sharjahTour->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Adult',
                'nationality' => 'United Kingdom',
                'price' => 320.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $sharjahTour->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Child',
                'nationality' => 'United Kingdom',
                'price' => 270.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
            UAEVisaPrice::create([
                'visa_package_id' => $sharjahTour->id,
                'entry_type' => 'Single Entry',
                'duration' => '30 Days',
                'traveller_type' => 'Infant',
                'nationality' => 'United Kingdom',
                'price' => 70.00,
                'isActive' => 1,
                'company_id' => 1,
            ]);
        }
    }
}
