<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JobCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['icon' => '💻', 'name' => 'Technology & IT', 'subs' => [
                'Software Development', 'Web Development', 'Mobile App Development',
                'Data Science & AI', 'Cybersecurity', 'IT Support & Networking',
                'Cloud Computing', 'UI/UX Design', 'QA & Testing', 'DevOps & SysAdmin',
            ]],
            ['icon' => '🏥', 'name' => 'Healthcare & Medical', 'subs' => [
                'Doctors & Physicians', 'Nursing', 'Pharmacy', 'Dentistry',
                'Physiotherapy', 'Mental Health', 'Medical Lab & Imaging',
                'Healthcare Administration', 'Home Care & PSW', 'Veterinary',
            ]],
            ['icon' => '🏗️', 'name' => 'Trades & Construction', 'subs' => [
                'Electricians', 'Plumbers', 'Carpenters & Woodworkers',
                'HVAC Technicians', 'Welders & Fabricators', 'General Labourers',
                'Painters & Decorators', 'Roofers', 'Concrete & Masonry', 'Heavy Equipment Operators',
            ]],
            ['icon' => '📦', 'name' => 'Warehouse & Logistics', 'subs' => [
                'Warehouse Associates', 'Forklift Operators', 'Truck Drivers',
                'Delivery Drivers', 'Supply Chain & Procurement',
                'Inventory Management', 'Shipping & Receiving',
            ]],
            ['icon' => '🍽️', 'name' => 'Hospitality & Food Service', 'subs' => [
                'Chefs & Cooks', 'Servers & Bartenders', 'Dishwashers & Kitchen Helpers',
                'Hotel & Accommodation', 'Event Staff', 'Catering',
                'Bakery & Pastry', 'Fast Food & Café',
            ]],
            ['icon' => '🛒', 'name' => 'Retail & Sales', 'subs' => [
                'Retail Associates', 'Cashiers', 'Sales Representatives',
                'Account Managers', 'Business Development', 'Visual Merchandising',
                'Store Management',
            ]],
            ['icon' => '📊', 'name' => 'Finance & Accounting', 'subs' => [
                'Accountants & Auditors', 'Bookkeeping', 'Financial Analysts',
                'Banking & Investment', 'Tax & Payroll', 'Insurance',
                'Financial Planning',
            ]],
            ['icon' => '⚖️', 'name' => 'Legal & Compliance', 'subs' => [
                'Lawyers & Paralegals', 'Legal Assistants', 'Compliance Officers',
                'Contract Management', 'Court Reporters',
            ]],
            ['icon' => '🎓', 'name' => 'Education & Training', 'subs' => [
                'Teachers & Instructors', 'Early Childhood Educators', 'Tutors',
                'University & College', 'Corporate Training', 'Special Education',
                'Library & Research',
            ]],
            ['icon' => '🏠', 'name' => 'Real Estate & Property', 'subs' => [
                'Real Estate Agents', 'Property Management', 'Leasing Consultants',
                'Appraisers', 'Mortgage Brokers',
            ]],
            ['icon' => '🎨', 'name' => 'Creative & Media', 'subs' => [
                'Graphic Designers', 'Video Production', 'Photography',
                'Copywriters & Editors', 'Marketing & Advertising',
                'Social Media Management', 'Journalism & Broadcasting',
            ]],
            ['icon' => '🔧', 'name' => 'Engineering', 'subs' => [
                'Mechanical Engineers', 'Civil & Structural Engineers',
                'Electrical Engineers', 'Chemical Engineers',
                'Industrial Engineers', 'Environmental Engineers',
                'Project Engineers',
            ]],
            ['icon' => '🚗', 'name' => 'Automotive', 'subs' => [
                'Auto Technicians & Mechanics', 'Auto Body & Collision',
                'Auto Sales', 'Auto Parts & Service Advisors', 'Detailing',
            ]],
            ['icon' => '👔', 'name' => 'Management & Executive', 'subs' => [
                'General Managers', 'Operations Managers', 'Project Managers',
                'HR Managers', 'C-Suite & Directors', 'Business Analysts',
            ]],
            ['icon' => '🧹', 'name' => 'Cleaning & Maintenance', 'subs' => [
                'Janitorial & Cleaning', 'Building Maintenance', 'Landscaping & Grounds',
                'Pest Control', 'Carpet & Floor Care',
            ]],
            ['icon' => '👶', 'name' => 'Childcare & Eldercare', 'subs' => [
                'Nannies & Babysitters', 'Daycare Workers', 'Elder Care & Companions',
                'Support Workers', 'Live-in Caregivers',
            ]],
            ['icon' => '💼', 'name' => 'Admin & Office', 'subs' => [
                'Receptionists', 'Administrative Assistants', 'Data Entry',
                'Customer Service', 'Office Managers', 'Executive Assistants',
            ]],
            ['icon' => '🌐', 'name' => 'Remote & Work From Home', 'subs' => [
                'Remote Software Jobs', 'Remote Customer Support', 'Remote Marketing',
                'Freelance & Contract', 'Virtual Assistants',
            ]],
            ['icon' => '🌱', 'name' => 'Agriculture & Environment', 'subs' => [
                'Farm Workers', 'Greenhouse & Nursery', 'Environmental Science',
                'Forestry & Conservation', 'Fisheries',
            ]],
            ['icon' => '🎮', 'name' => 'Entertainment & Sports', 'subs' => [
                'Fitness & Personal Training', 'Sports Coaching', 'Gaming & Esports',
                'Performing Arts', 'Recreation & Leisure',
            ]],
            ['icon' => '🛡️', 'name' => 'Security & Public Safety', 'subs' => [
                'Security Guards', 'Loss Prevention', 'Police & Law Enforcement',
                'Firefighters & EMS', 'Military & Defence',
            ]],
            ['icon' => '✈️', 'name' => 'Aviation & Transportation', 'subs' => [
                'Pilots & Crew', 'Airport Ground Staff', 'Transit & Rail',
                'Marine & Shipping',
            ]],
        ];

        foreach ($categories as $cat) {
            $slug = 'job-' . Str::slug($cat['name']);
            $i = 1;
            $baseSlug = $slug;
            while (DB::table('categories')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }

            $parentId = DB::table('categories')->insertGetId([
                'name'       => $cat['name'],
                'slug'       => $slug,
                'icon'       => $cat['icon'],
                'type'       => 'jobs',
                'parent_id'  => null,
                'is_active'  => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($cat['subs'] as $subName) {
                $subSlug = 'job-' . Str::slug($subName);
                $j = 1;
                $baseSubSlug = $subSlug;
                while (DB::table('categories')->where('slug', $subSlug)->exists()) {
                    $subSlug = $baseSubSlug . '-' . $j++;
                }

                DB::table('categories')->insert([
                    'name'       => $subName,
                    'slug'       => $subSlug,
                    'icon'       => null,
                    'type'       => 'jobs',
                    'parent_id'  => $parentId,
                    'is_active'  => true,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Job categories seeded: ' . count($categories) . ' parents.');
    }
}
