<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nepaliDistricts = [
            'Kathmandu', 'Lalitpur', 'Bhaktapur', 'Kaski', 'Chitwan',
            'Jhapa', 'Morang', 'Sunsari', 'Rupandehi', 'Banke',
            'Kailali', 'Dang', 'Makwanpur', 'Parsa', 'Bara',
        ];

        $organizations = [
            'Tribhuvan University', 'Kathmandu University', 'Pokhara University',
            "St. Xavier's College", 'Pulchowk Campus', 'KIST College',
            'Nepal Bank Limited', 'NTC', 'Ncell', 'Daraz Nepal',
        ];

        return [
            // Personal
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional(0.3)->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'date_of_birth' => fake()->dateTimeBetween('-30 years', '-17 years'),
            'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'marital_status' => fake()->randomElement(['single', 'married']),

            // Contact
            'phone' => fake()->numerify('98########'),
            'secondary_phone' => fake()->optional(0.4)->numerify('98########'),
            'email' => fake()->unique()->safeEmail(),
            'permanent_address' => fake()->address(),
            'current_address' => fake()->optional(0.6)->address(),

            // Guardian
            'father_name' => fake()->name('male'),
            'father_phone' => fake()->numerify('98########'),
            'mother_name' => fake()->name('female'),
            'mother_phone' => fake()->optional(0.7)->numerify('98########'),
            'local_guardian_name' => fake()->name(),
            'local_guardian_phone' => fake()->numerify('98########'),
            'local_guardian_relationship' => fake()->randomElement(['Uncle', 'Aunt', 'Brother', 'Sister', 'Cousin', 'Family Friend']),

            // Identification
            'citizenship_number' => fake()->numerify('##-##-##-#####'),
            'citizenship_issued_district' => fake()->randomElement($nepaliDistricts),
            'citizenship_issued_date' => fake()->dateTimeBetween('-10 years', '-1 year')->format('Y-m-d'),

            // Education / Profession
            'occupation_status' => fake()->randomElement(['student', 'job_holder']),
            'organization_name' => fake()->randomElement($organizations),
            'level_designation' => fake()->randomElement(['Bachelors 1st Year', 'Bachelors 2nd Year', 'Bachelors 3rd Year', 'Masters', 'Software Engineer', 'Accountant']),

            // Health
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->numerify('98########'),

            // Financial
            'joined_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'security_deposit' => fake()->randomElement([5000, 8000, 10000, 15000]),
            'monthly_rent_agreed' => fake()->randomElement([3500, 4000, 5000, 6000, 8000]),
            'meal_preference' => fake()->randomElement(['veg', 'non_veg']),
            'referral_source' => fake()->randomElement(['Friend', 'Website', 'Social Media', 'Walk-in', 'College Notice Board']),

            // System
            'status' => 'active',
        ];
    }
}
