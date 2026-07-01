<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryField;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryFieldSeeder extends Seeder
{
    /**
     * Preset custom fields per directory category (matched by name).
     * Each field: [label, type, options?, required?, placeholder?]
     */
    private array $map = [
        'Restaurant' => [
            ['Cuisine Type', 'select', ['North Indian', 'South Indian', 'Gujarati', 'Punjabi', 'Chinese', 'Multi-Cuisine']],
            ['Food Type', 'select', ['Veg', 'Non-Veg', 'Both', 'Jain', 'Vegan']],
            ['Dine-in Available', 'checkbox'],
            ['Takeaway / Delivery', 'checkbox'],
            ['Avg. Cost for Two', 'text', null, false, 'e.g. $30'],
            ['Opening Hours', 'text', null, false, 'e.g. 11 AM – 10 PM'],
        ],
        'Immigration' => [
            ['Services Offered', 'select', ['Study Visa', 'Work Permit', 'PR', 'Citizenship', 'Visitor Visa', 'All Services']],
            ['Consultant Licensed (RCIC)', 'checkbox'],
            ['Free Consultation', 'checkbox'],
            ['Experience (Years)', 'number'],
        ],
        'Real Estate Agent' => [
            ['Specialization', 'select', ['Residential', 'Commercial', 'Both', 'Rentals', 'Pre-Construction']],
            ['Brokerage Name', 'text'],
            ['License Number', 'text'],
            ['Areas Served', 'text', null, false, 'e.g. Brampton, Mississauga'],
        ],
        'Travel Agency' => [
            ['Service Type', 'select', ['Flight Booking', 'Tour Packages', 'Visa Assistance', 'Hotel Booking', 'All Services']],
            ['IATA Certified', 'checkbox'],
            ['Destinations', 'text', null, false, 'e.g. India, Dubai, Europe'],
        ],
        'Grocery' => [
            ['Store Type', 'select', ['Indian Grocery', 'Sweets & Snacks', 'Halal', 'General Grocery', 'Organic']],
            ['Home Delivery', 'checkbox'],
            ['Fresh Vegetables', 'checkbox'],
            ['Opening Hours', 'text', null, false, 'e.g. 9 AM – 9 PM'],
        ],
        'Professional Services' => [
            ['Service Category', 'select', ['Accounting', 'Legal', 'Insurance', 'IT / Web', 'Marketing', 'Consulting', 'Other']],
            ['Experience (Years)', 'number'],
            ['Free Consultation', 'checkbox'],
        ],
        'Education' => [
            ['Type', 'select', ['Tutoring', 'Coaching Classes', 'Music', 'Dance', 'Language', 'Daycare', 'Other']],
            ['Mode', 'select', ['In-Person', 'Online', 'Both']],
            ['Grade / Level', 'text', null, false, 'e.g. Grade 1–12, College'],
        ],
        'Sports' => [
            ['Sport / Activity', 'text', null, false, 'e.g. Cricket, Badminton, Gym'],
            ['Type', 'select', ['Coaching', 'Club', 'Facility Rental', 'Equipment']],
            ['Age Group', 'text', null, false, 'e.g. Kids, Adults, All'],
        ],
        'Medical' => [
            ['Specialization', 'select', ['Family Doctor', 'Physiotherapy', 'Pediatrics', 'Cardiology', 'General', 'Other']],
            ['Accepts New Patients', 'checkbox'],
            ['Walk-in Available', 'checkbox'],
            ['Languages Spoken', 'text', null, false, 'e.g. English, Hindi, Punjabi'],
        ],
        'Dental' => [
            ['Services', 'select', ['General Dentistry', 'Orthodontics', 'Cosmetic', 'Implants', 'Emergency', 'All']],
            ['Accepts Insurance', 'checkbox'],
            ['Emergency Service', 'checkbox'],
        ],
        'Salon & Spa' => [
            ['Type', 'select', ['Hair Salon', 'Beauty Parlour', 'Spa', 'Nail Studio', 'Barber', 'Unisex']],
            ['For', 'select', ['Women', 'Men', 'Unisex']],
            ['Home Service', 'checkbox'],
            ['Appointment Needed', 'checkbox'],
        ],
        'Fashion' => [
            ['Category', 'select', ['Indian Wear', 'Western Wear', 'Bridal', 'Kids', 'Accessories', 'Footwear']],
            ['For', 'select', ['Women', 'Men', 'Kids', 'All']],
            ['Custom Stitching', 'checkbox'],
        ],
        'Jewelry' => [
            ['Type', 'select', ['Gold', 'Diamond', 'Silver', 'Imitation', 'Bridal Sets']],
            ['Custom Design', 'checkbox'],
            ['Certified (BIS/Hallmark)', 'checkbox'],
            ['Buy-back / Exchange', 'checkbox'],
        ],
    ];

    public function run(): void
    {
        foreach ($this->map as $categoryName => $fields) {
            $category = Category::where('type', 'directory')
                ->where('name', $categoryName)
                ->first();

            if (! $category) {
                continue;
            }

            foreach ($fields as $i => $f) {
                [$label, $type] = $f;
                $options     = $f[2] ?? null;
                $required    = $f[3] ?? false;
                $placeholder = $f[4] ?? null;

                CategoryField::updateOrCreate(
                    ['category_id' => $category->id, 'key' => Str::slug($label, '_')],
                    [
                        'label'       => $label,
                        'type'        => $type,
                        'options'     => $type === 'select' ? $options : null,
                        'placeholder' => $placeholder,
                        'is_required' => $required,
                        'sort_order'  => $i,
                    ]
                );
            }
        }
    }
}
