<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClassifiedCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Each parent: name, icon, subs[], fields[] (custom fields for parent + inherited by subs)
        // Sub-specific fields can be added under subs as ['name'=>..., 'fields'=>[...]]
        $data = [
            [
                'name' => 'Buy & Sell', 'icon' => '🛍️',
                'fields' => [
                    ['label' => 'Condition',    'key' => 'condition',    'type' => 'select',   'options' => ['New','Like New','Good','Fair','For Parts'], 'required' => true],
                    ['label' => 'Brand',        'key' => 'brand',        'type' => 'text',     'placeholder' => 'e.g. Samsung, Apple'],
                    ['label' => 'Exchange',     'key' => 'exchange',     'type' => 'checkbox', 'placeholder' => ''],
                ],
                'subs' => [
                    ['name' => 'Electronics',        'fields' => [['label'=>'Model','key'=>'model','type'=>'text','placeholder'=>'e.g. iPhone 14 Pro'],['label'=>'Storage','key'=>'storage','type'=>'text','placeholder'=>'e.g. 256GB']]],
                    ['name' => 'Mobile Phones',      'fields' => [['label'=>'Model','key'=>'model','type'=>'text','placeholder'=>'e.g. Galaxy S23'],['label'=>'Storage','key'=>'storage','type'=>'text','placeholder'=>'e.g. 128GB'],['label'=>'Carrier','key'=>'carrier','type'=>'select','options'=>['Unlocked','Rogers','Bell','Telus','Fido','Freedom','Videotron']]]],
                    ['name' => 'Computers & Tablets','fields' => [['label'=>'Model','key'=>'model','type'=>'text','placeholder'=>'e.g. MacBook Pro 2022'],['label'=>'RAM','key'=>'ram','type'=>'text','placeholder'=>'e.g. 16GB'],['label'=>'Storage','key'=>'storage','type'=>'text','placeholder'=>'e.g. 512GB SSD']]],
                    ['name' => 'TV & Audio',         'fields' => [['label'=>'Screen Size','key'=>'screen_size','type'=>'text','placeholder'=>'e.g. 65 inch'],['label'=>'Resolution','key'=>'resolution','type'=>'select','options'=>['HD','Full HD','4K','8K']]]],
                    ['name' => 'Cameras',            'fields' => [['label'=>'Model','key'=>'model','type'=>'text','placeholder'=>'e.g. Canon EOS R6'],['label'=>'Includes','key'=>'includes','type'=>'text','placeholder'=>'e.g. 2 lenses, bag']]],
                    ['name' => 'Home Appliances',    'fields' => [['label'=>'Type','key'=>'appliance_type','type'=>'text','placeholder'=>'e.g. Washer, Dryer, Fridge']]],
                    ['name' => 'Furniture',          'fields' => [['label'=>'Material','key'=>'material','type'=>'text','placeholder'=>'e.g. Wood, Leather, Fabric'],['label'=>'Dimensions','key'=>'dimensions','type'=>'text','placeholder'=>'e.g. 60"W x 30"D x 18"H']]],
                    ['name' => 'Home Décor',         'fields' => []],
                    ['name' => 'Clothing',           'fields' => [['label'=>'Size','key'=>'size','type'=>'text','placeholder'=>'e.g. M, L, XL, 32x30'],['label'=>'Gender','key'=>'gender','type'=>'select','options'=>['Men','Women','Kids','Unisex']]]],
                    ['name' => 'Shoes',              'fields' => [['label'=>'Size','key'=>'size','type'=>'text','placeholder'=>'e.g. US 10, EU 43'],['label'=>'Gender','key'=>'gender','type'=>'select','options'=>['Men','Women','Kids']]]],
                    ['name' => 'Jewelry',            'fields' => [['label'=>'Metal','key'=>'metal','type'=>'select','options'=>['Gold','Silver','Platinum','Rose Gold','Other']],['label'=>'Stone','key'=>'stone','type'=>'text','placeholder'=>'e.g. Diamond, Ruby']]],
                    ['name' => 'Watches',            'fields' => [['label'=>'Brand','key'=>'watch_brand','type'=>'text','placeholder'=>'e.g. Rolex, Seiko'],['label'=>'Movement','key'=>'movement','type'=>'select','options'=>['Automatic','Quartz','Manual']]]],
                    ['name' => 'Sports Equipment',   'fields' => [['label'=>'Sport','key'=>'sport','type'=>'text','placeholder'=>'e.g. Hockey, Golf, Tennis']]],
                    ['name' => 'Musical Instruments','fields' => [['label'=>'Instrument','key'=>'instrument','type'=>'text','placeholder'=>'e.g. Guitar, Piano'],['label'=>'Brand','key'=>'brand','type'=>'text','placeholder'=>'e.g. Fender, Yamaha']]],
                    ['name' => 'Books',              'fields' => [['label'=>'Author','key'=>'author','type'=>'text','placeholder'=>'Author name'],['label'=>'ISBN','key'=>'isbn','type'=>'text','placeholder'=>'Optional']]],
                    ['name' => 'Collectibles',       'fields' => [['label'=>'Era / Year','key'=>'era','type'=>'text','placeholder'=>'e.g. 1980s, 2005']]],
                    ['name' => 'Toys & Games',       'fields' => [['label'=>'Age Group','key'=>'age_group','type'=>'text','placeholder'=>'e.g. 3+, 8-12']]],
                    ['name' => 'Baby Items',         'fields' => [['label'=>'Age Range','key'=>'age_range','type'=>'text','placeholder'=>'e.g. 0-6 months']]],
                    ['name' => 'Health & Beauty',    'fields' => [['label'=>'Category','key'=>'hb_category','type'=>'text','placeholder'=>'e.g. Skincare, Hair, Vitamins']]],
                    ['name' => 'Office Supplies',    'fields' => []],
                    ['name' => 'Free Stuff',         'fields' => [['label'=>'Reason','key'=>'reason','type'=>'text','placeholder'=>'Why are you giving it away?']]],
                    ['name' => 'Other Items',        'fields' => []],
                ],
            ],
            [
                'name' => 'Vehicles', 'icon' => '🚗',
                'fields' => [
                    ['label' => 'Year',          'key' => 'year',          'type' => 'number',  'placeholder' => 'e.g. 2020',      'required' => true],
                    ['label' => 'Make',          'key' => 'make',          'type' => 'text',    'placeholder' => 'e.g. Toyota',    'required' => true],
                    ['label' => 'Model',         'key' => 'model',         'type' => 'text',    'placeholder' => 'e.g. Camry',     'required' => true],
                    ['label' => 'Mileage (km)',  'key' => 'mileage',       'type' => 'number',  'placeholder' => 'e.g. 85000'],
                    ['label' => 'Transmission',  'key' => 'transmission',  'type' => 'select',  'options' => ['Automatic','Manual','CVT']],
                    ['label' => 'Fuel Type',     'key' => 'fuel_type',     'type' => 'select',  'options' => ['Gasoline','Diesel','Hybrid','Electric','Plug-in Hybrid']],
                    ['label' => 'Colour',        'key' => 'colour',        'type' => 'text',    'placeholder' => 'e.g. White'],
                    ['label' => 'Condition',     'key' => 'condition',     'type' => 'select',  'options' => ['Excellent','Good','Fair','Salvage']],
                ],
                'subs' => [
                    ['name' => 'Cars',                'fields' => [['label'=>'Body Style','key'=>'body_style','type'=>'select','options'=>['Sedan','Hatchback','Coupe','Convertible','Station Wagon']]]],
                    ['name' => 'SUVs',                'fields' => [['label'=>'Drive','key'=>'drive','type'=>'select','options'=>['AWD','4WD','FWD','RWD']]]],
                    ['name' => 'Trucks',              'fields' => [['label'=>'Drive','key'=>'drive','type'=>'select','options'=>['4WD','AWD','2WD']],['label'=>'Cab Size','key'=>'cab_size','type'=>'select','options'=>['Regular','Extended','Crew']]]],
                    ['name' => 'Vans',                'fields' => [['label'=>'Seating','key'=>'seating','type'=>'text','placeholder'=>'e.g. 7 passenger']]],
                    ['name' => 'Motorcycles',         'fields' => [['label'=>'Engine (cc)','key'=>'engine_cc','type'=>'number','placeholder'=>'e.g. 650']]],
                    ['name' => 'ATVs & UTVs',         'fields' => [['label'=>'Engine (cc)','key'=>'engine_cc','type'=>'number','placeholder'=>'e.g. 450']]],
                    ['name' => 'RVs & Campers',       'fields' => [['label'=>'Type','key'=>'rv_type','type'=>'select','options'=>['Motorhome','Travel Trailer','Fifth Wheel','Tent Trailer','Camper Van']],['label'=>'Length (ft)','key'=>'length_ft','type'=>'number','placeholder'=>'e.g. 32']]],
                    ['name' => 'Boats',               'fields' => [['label'=>'Boat Type','key'=>'boat_type','type'=>'text','placeholder'=>'e.g. Fishing, Pontoon, Sailboat'],['label'=>'Length (ft)','key'=>'length_ft','type'=>'number','placeholder'=>'e.g. 20']]],
                    ['name' => 'Trailers',            'fields' => [['label'=>'Trailer Type','key'=>'trailer_type','type'=>'text','placeholder'=>'e.g. Utility, Flatbed, Enclosed']]],
                    ['name' => 'Heavy Equipment',     'fields' => [['label'=>'Equipment Type','key'=>'equip_type','type'=>'text','placeholder'=>'e.g. Excavator, Bulldozer'],['label'=>'Hours','key'=>'hours','type'=>'number','placeholder'=>'Operating hours']]],
                    ['name' => 'Commercial Vehicles', 'fields' => [['label'=>'GVWR','key'=>'gvwr','type'=>'text','placeholder'=>'e.g. Class 5, 26,000 lb']]],
                    ['name' => 'Auto Parts',          'fields' => [['label'=>'Fits','key'=>'fits','type'=>'text','placeholder'=>'e.g. 2015-2020 Toyota Camry'],['label'=>'Part Name','key'=>'part_name','type'=>'text','placeholder'=>'e.g. Alternator, Hood']]],
                    ['name' => 'Tires & Wheels',      'fields' => [['label'=>'Tire Size','key'=>'tire_size','type'=>'text','placeholder'=>'e.g. 225/65R17'],['label'=>'Season','key'=>'season','type'=>'select','options'=>['All-Season','Winter','Summer']]]],
                    ['name' => 'Vehicle Accessories', 'fields' => [['label'=>'Compatible With','key'=>'compatible_with','type'=>'text','placeholder'=>'e.g. Honda Civic 2018+']]],
                ],
            ],
            [
                'name' => 'Real Estate', 'icon' => '🏠',
                'fields' => [
                    ['label' => 'Bedrooms',   'key' => 'bedrooms',   'type' => 'select', 'options' => ['Bachelor/Studio','1','2','3','4','5+'], 'required' => true],
                    ['label' => 'Bathrooms',  'key' => 'bathrooms',  'type' => 'select', 'options' => ['1','1.5','2','2.5','3','3+']],
                    ['label' => 'Size (sqft)','key' => 'size_sqft',  'type' => 'number', 'placeholder' => 'e.g. 1200'],
                    ['label' => 'Parking',    'key' => 'parking',    'type' => 'select', 'options' => ['None','Street','Driveway','Garage','Underground']],
                    ['label' => 'Pet Friendly','key'=>'pet_friendly','type' => 'checkbox'],
                    ['label' => 'Utilities Included','key'=>'utilities','type'=>'select','options'=>['None','Heat','Water','Hydro','All Inclusive']],
                    ['label' => 'Available From','key'=>'available_from','type'=>'text','placeholder'=>'e.g. Feb 1, 2025'],
                ],
                'subs' => [
                    ['name' => 'Apartments for Rent', 'fields' => [['label'=>'Floor','key'=>'floor','type'=>'number','placeholder'=>'Floor number']]],
                    ['name' => 'Houses for Rent',     'fields' => [['label'=>'Garage','key'=>'garage','type'=>'checkbox']]],
                    ['name' => 'Basement Suites',     'fields' => [['label'=>'Separate Entrance','key'=>'sep_entrance','type'=>'checkbox']]],
                    ['name' => 'Condos',              'fields' => [['label'=>'Strata Fee','key'=>'strata_fee','type'=>'number','placeholder'=>'Monthly strata fee $']]],
                    ['name' => 'Townhouses',          'fields' => [['label'=>'Levels','key'=>'levels','type'=>'number','placeholder'=>'e.g. 2']]],
                    ['name' => 'Rooms for Rent',      'fields' => [['label'=>'Shared Kitchen','key'=>'shared_kitchen','type'=>'checkbox'],['label'=>'Furnished','key'=>'furnished','type'=>'checkbox']]],
                    ['name' => 'Roommates',           'fields' => [['label'=>'Gender Preference','key'=>'gender_pref','type'=>'select','options'=>['Any','Male','Female']],['label'=>'Furnished','key'=>'furnished','type'=>'checkbox']]],
                    ['name' => 'Vacation Rentals',    'fields' => [['label'=>'Max Guests','key'=>'max_guests','type'=>'number','placeholder'=>'e.g. 6'],['label'=>'Min Nights','key'=>'min_nights','type'=>'number','placeholder'=>'e.g. 2']]],
                    ['name' => 'Commercial Space',    'fields' => [['label'=>'Zoning','key'=>'zoning','type'=>'text','placeholder'=>'e.g. C1, M1']]],
                    ['name' => 'Office Space',        'fields' => [['label'=>'Offices','key'=>'offices','type'=>'number','placeholder'=>'Number of private offices']]],
                    ['name' => 'Retail Space',        'fields' => [['label'=>'Frontage (ft)','key'=>'frontage','type'=>'number','placeholder'=>'Street frontage in feet']]],
                    ['name' => 'Land for Sale',       'fields' => [['label'=>'Lot Size (acres)','key'=>'lot_size','type'=>'text','placeholder'=>'e.g. 0.5 acres']]],
                    ['name' => 'Houses for Sale',     'fields' => [['label'=>'Garage','key'=>'garage','type'=>'checkbox'],['label'=>'Year Built','key'=>'year_built','type'=>'number','placeholder'=>'e.g. 2005']]],
                    ['name' => 'Condos for Sale',     'fields' => [['label'=>'Strata Fee','key'=>'strata_fee','type'=>'number','placeholder'=>'Monthly $'],['label'=>'Year Built','key'=>'year_built','type'=>'number','placeholder'=>'e.g. 2018']]],
                ],
            ],
            [
                'name' => 'Jobs', 'icon' => '💼',
                'fields' => [
                    ['label' => 'Job Type',   'key' => 'job_type',   'type' => 'select',   'options' => ['Full-Time','Part-Time','Contract','Temporary','Internship','Remote'], 'required' => true],
                    ['label' => 'Salary',     'key' => 'salary',     'type' => 'text',     'placeholder' => 'e.g. $20/hr or $50,000/year'],
                    ['label' => 'Experience', 'key' => 'experience', 'type' => 'select',   'options' => ['No Experience','Entry Level','1-2 Years','3-5 Years','5+ Years']],
                    ['label' => 'Apply Email','key' => 'apply_email','type' => 'text',     'placeholder' => 'hiring@company.com'],
                ],
                'subs' => [
                    ['name' => 'Full-Time',         'fields' => []],
                    ['name' => 'Part-Time',         'fields' => []],
                    ['name' => 'Contract',          'fields' => [['label'=>'Contract Length','key'=>'contract_length','type'=>'text','placeholder'=>'e.g. 6 months']]],
                    ['name' => 'Temporary',         'fields' => [['label'=>'Duration','key'=>'duration','type'=>'text','placeholder'=>'e.g. 3 months']]],
                    ['name' => 'Internship',        'fields' => [['label'=>'Paid / Unpaid','key'=>'paid','type'=>'select','options'=>['Paid','Unpaid']]]],
                    ['name' => 'Remote Jobs',       'fields' => []],
                    ['name' => 'Government Jobs',   'fields' => []],
                    ['name' => 'Healthcare',        'fields' => [['label'=>'Designation','key'=>'designation','type'=>'text','placeholder'=>'e.g. RN, LPN, PSW']]],
                    ['name' => 'IT & Technology',   'fields' => [['label'=>'Tech Stack','key'=>'tech_stack','type'=>'text','placeholder'=>'e.g. PHP, React, AWS']]],
                    ['name' => 'Construction',      'fields' => [['label'=>'Trade','key'=>'trade','type'=>'text','placeholder'=>'e.g. Carpenter, Electrician']]],
                    ['name' => 'Retail',            'fields' => []],
                    ['name' => 'Hospitality',       'fields' => []],
                    ['name' => 'Drivers',           'fields' => [['label'=>'License Class','key'=>'license_class','type'=>'select','options'=>['G','AZ','DZ','A','B','C']]]],
                    ['name' => 'Skilled Trades',    'fields' => [['label'=>'Trade','key'=>'trade','type'=>'text','placeholder'=>'e.g. Welder, Plumber, HVAC']]],
                    ['name' => 'Customer Service',  'fields' => []],
                    ['name' => 'Education',         'fields' => [['label'=>'Subject','key'=>'subject','type'=>'text','placeholder'=>'e.g. Math, ESL, Science']]],
                    ['name' => 'Accounting',        'fields' => [['label'=>'Designation','key'=>'designation','type'=>'text','placeholder'=>'e.g. CPA, Bookkeeper']]],
                    ['name' => 'Sales & Marketing', 'fields' => []],
                ],
            ],
            [
                'name' => 'Services', 'icon' => '🔧',
                'fields' => [
                    ['label' => 'Service Area', 'key' => 'service_area', 'type' => 'text',     'placeholder' => 'e.g. Greater Toronto Area'],
                    ['label' => 'Licensed',     'key' => 'licensed',     'type' => 'checkbox'],
                    ['label' => 'Insured',      'key' => 'insured',      'type' => 'checkbox'],
                    ['label' => 'Free Estimate','key' => 'free_estimate','type' => 'checkbox'],
                ],
                'subs' => [
                    ['name' => 'Home Cleaning',        'fields' => [['label'=>'Cleaning Type','key'=>'cleaning_type','type'=>'select','options'=>['Regular','Deep Clean','Move-in/Move-out','Post-Construction']]]],
                    ['name' => 'Plumbing',             'fields' => []],
                    ['name' => 'Electrical',           'fields' => []],
                    ['name' => 'HVAC',                 'fields' => [['label'=>'Service Type','key'=>'hvac_type','type'=>'select','options'=>['Installation','Repair','Maintenance','Inspection']]]],
                    ['name' => 'Roofing',              'fields' => [['label'=>'Roof Type','key'=>'roof_type','type'=>'text','placeholder'=>'e.g. Shingles, Flat, Metal']]],
                    ['name' => 'Painting',             'fields' => [['label'=>'Painting Type','key'=>'paint_type','type'=>'select','options'=>['Interior','Exterior','Both']]]],
                    ['name' => 'Renovation',           'fields' => [['label'=>'Area','key'=>'reno_area','type'=>'text','placeholder'=>'e.g. Kitchen, Bathroom, Basement']]],
                    ['name' => 'Landscaping',          'fields' => [['label'=>'Service','key'=>'landscape_service','type'=>'text','placeholder'=>'e.g. Lawn care, Garden design']]],
                    ['name' => 'Snow Removal',         'fields' => [['label'=>'Property Type','key'=>'property_type','type'=>'select','options'=>['Residential','Commercial']]]],
                    ['name' => 'Appliance Repair',     'fields' => [['label'=>'Appliance','key'=>'appliance','type'=>'text','placeholder'=>'e.g. Washer, Fridge, Oven']]],
                    ['name' => 'Auto Repair',          'fields' => [['label'=>'Specialty','key'=>'specialty','type'=>'text','placeholder'=>'e.g. Brakes, Engine, Transmission']]],
                    ['name' => 'Computer Repair',      'fields' => [['label'=>'Device','key'=>'device','type'=>'text','placeholder'=>'e.g. Laptop, Desktop, Mac']]],
                    ['name' => 'Web Design',           'fields' => [['label'=>'Platform','key'=>'platform','type'=>'text','placeholder'=>'e.g. WordPress, Shopify, Custom']]],
                    ['name' => 'Graphic Design',       'fields' => [['label'=>'Design Type','key'=>'design_type','type'=>'text','placeholder'=>'e.g. Logo, Branding, Flyers']]],
                    ['name' => 'Photography',          'fields' => [['label'=>'Specialty','key'=>'photo_type','type'=>'select','options'=>['Wedding','Portrait','Event','Real Estate','Commercial']]]],
                    ['name' => 'Videography',          'fields' => [['label'=>'Specialty','key'=>'video_type','type'=>'select','options'=>['Wedding','Event','Corporate','Music Video']]]],
                    ['name' => 'Event Planning',       'fields' => [['label'=>'Event Type','key'=>'event_type','type'=>'text','placeholder'=>'e.g. Wedding, Corporate, Birthday']]],
                    ['name' => 'Catering',             'fields' => [['label'=>'Cuisine','key'=>'cuisine','type'=>'text','placeholder'=>'e.g. Indian, Italian, BBQ'],['label'=>'Min Guests','key'=>'min_guests','type'=>'number','placeholder'=>'e.g. 20']]],
                    ['name' => 'DJ Services',          'fields' => [['label'=>'Event Type','key'=>'event_type','type'=>'text','placeholder'=>'e.g. Wedding, Party, Corporate']]],
                    ['name' => 'Moving Services',      'fields' => [['label'=>'Move Type','key'=>'move_type','type'=>'select','options'=>['Local','Long Distance','Office']]]],
                    ['name' => 'Tutoring',             'fields' => [['label'=>'Subject','key'=>'subject','type'=>'text','placeholder'=>'e.g. Math, English, Science'],['label'=>'Grade Level','key'=>'grade_level','type'=>'text','placeholder'=>'e.g. Grade 8, High School, University']]],
                    ['name' => 'Tax Preparation',      'fields' => [['label'=>'Filing Type','key'=>'filing_type','type'=>'select','options'=>['Personal','Business','Corporate','Self-Employed']]]],
                    ['name' => 'Legal Services',       'fields' => [['label'=>'Practice Area','key'=>'practice_area','type'=>'text','placeholder'=>'e.g. Family Law, Immigration']]],
                    ['name' => 'Immigration Services', 'fields' => [['label'=>'Service','key'=>'imm_service','type'=>'text','placeholder'=>'e.g. Study Permit, PR, Citizenship']]],
                ],
            ],
            [
                'name' => 'Community', 'icon' => '🤝',
                'fields' => [
                    ['label' => 'Date',     'key' => 'event_date', 'type' => 'text', 'placeholder' => 'e.g. Jan 15, 2025'],
                    ['label' => 'Location', 'key' => 'location',   'type' => 'text', 'placeholder' => 'Venue or area'],
                ],
                'subs' => [
                    ['name' => 'Local Events',       'fields' => [['label'=>'Event Type','key'=>'event_type','type'=>'text','placeholder'=>'e.g. Festival, Meetup']]],
                    ['name' => 'Classes',            'fields' => [['label'=>'Schedule','key'=>'schedule','type'=>'text','placeholder'=>'e.g. Every Saturday 10am']]],
                    ['name' => 'Workshops',          'fields' => [['label'=>'Duration','key'=>'duration','type'=>'text','placeholder'=>'e.g. 3 hours']]],
                    ['name' => 'Volunteers Wanted',  'fields' => [['label'=>'Commitment','key'=>'commitment','type'=>'text','placeholder'=>'e.g. 4 hrs/week']]],
                    ['name' => 'Lost & Found',       'fields' => [['label'=>'Item Type','key'=>'item_type','type'=>'select','options'=>['Lost','Found']],['label'=>'Date','key'=>'incident_date','type'=>'text','placeholder'=>'When was it lost/found?']]],
                    ['name' => 'Announcements',      'fields' => []],
                    ['name' => 'Local Groups',       'fields' => [['label'=>'Group Type','key'=>'group_type','type'=>'text','placeholder'=>'e.g. Sports, Religious, Cultural']]],
                    ['name' => 'Charity Events',     'fields' => [['label'=>'Cause','key'=>'cause','type'=>'text','placeholder'=>'e.g. Food Bank, Shelter']]],
                    ['name' => 'Religious Events',   'fields' => [['label'=>'Faith','key'=>'faith','type'=>'text','placeholder'=>'e.g. Hindu, Muslim, Sikh, Christian']]],
                    ['name' => 'Cultural Programs',  'fields' => [['label'=>'Culture','key'=>'culture','type'=>'text','placeholder'=>'e.g. Gujarati, Punjabi, Tamil']]],
                ],
            ],
            [
                'name' => 'Pets', 'icon' => '🐾',
                'fields' => [
                    ['label' => 'Pet Type',  'key' => 'pet_type',  'type' => 'text',     'placeholder' => 'e.g. Dog, Cat, Bird'],
                    ['label' => 'Breed',     'key' => 'breed',     'type' => 'text',     'placeholder' => 'e.g. Golden Retriever'],
                    ['label' => 'Age',       'key' => 'age',       'type' => 'text',     'placeholder' => 'e.g. 2 years, 6 months'],
                    ['label' => 'Vaccinated','key' => 'vaccinated','type' => 'checkbox'],
                ],
                'subs' => [
                    ['name' => 'Dogs',              'fields' => [['label'=>'Size','key'=>'dog_size','type'=>'select','options'=>['Small','Medium','Large','Giant']]]],
                    ['name' => 'Cats',              'fields' => [['label'=>'Indoor/Outdoor','key'=>'indoor_outdoor','type'=>'select','options'=>['Indoor','Outdoor','Both']]]],
                    ['name' => 'Birds',             'fields' => [['label'=>'Species','key'=>'species','type'=>'text','placeholder'=>'e.g. Parrot, Budgie, Canary']]],
                    ['name' => 'Fish',              'fields' => [['label'=>'Tank Size','key'=>'tank_size','type'=>'text','placeholder'=>'e.g. 20 gallon']]],
                    ['name' => 'Small Pets',        'fields' => [['label'=>'Animal','key'=>'animal','type'=>'text','placeholder'=>'e.g. Rabbit, Hamster, Guinea Pig']]],
                    ['name' => 'Pet Supplies',      'fields' => [['label'=>'For Pet','key'=>'for_pet','type'=>'text','placeholder'=>'e.g. Dog, Cat'],['label'=>'Condition','key'=>'condition','type'=>'select','options'=>['New','Like New','Used']]]],
                    ['name' => 'Pet Food',          'fields' => [['label'=>'For Pet','key'=>'for_pet','type'=>'text','placeholder'=>'e.g. Dog, Cat']]],
                    ['name' => 'Pet Adoption',      'fields' => [['label'=>'Adoption Fee','key'=>'adoption_fee','type'=>'text','placeholder'=>'e.g. Free, $50'],['label'=>'Spayed/Neutered','key'=>'spayed','type'=>'checkbox']]],
                    ['name' => 'Pet Grooming',      'fields' => [['label'=>'Service','key'=>'groom_service','type'=>'text','placeholder'=>'e.g. Bath, Full Groom, Nail Trim']]],
                    ['name' => 'Pet Boarding',      'fields' => [['label'=>'Rate','key'=>'rate','type'=>'text','placeholder'=>'e.g. $35/night']]],
                    ['name' => 'Veterinary Services','fields'=> [['label'=>'Specialty','key'=>'vet_specialty','type'=>'text','placeholder'=>'e.g. General, Emergency, Exotic']]],
                ],
            ],
            [
                'name' => 'Business Directory', 'icon' => '🏢',
                'fields' => [
                    ['label' => 'Business Hours', 'key' => 'hours',   'type' => 'text', 'placeholder' => 'e.g. Mon-Fri 9am-5pm'],
                    ['label' => 'Website',        'key' => 'website', 'type' => 'text', 'placeholder' => 'https://'],
                ],
                'subs' => [
                    ['name' => 'Restaurants',       'fields' => [['label'=>'Cuisine','key'=>'cuisine','type'=>'text','placeholder'=>'e.g. Indian, Chinese, Italian']]],
                    ['name' => 'Grocery Stores',    'fields' => []],
                    ['name' => 'Retail Stores',     'fields' => [['label'=>'Specialty','key'=>'specialty','type'=>'text','placeholder'=>'e.g. Clothing, Electronics']]],
                    ['name' => 'Pharmacies',        'fields' => []],
                    ['name' => 'Clinics',           'fields' => [['label'=>'Specialty','key'=>'specialty','type'=>'text','placeholder'=>'e.g. Walk-in, Dental, Physio']]],
                    ['name' => 'Salons',            'fields' => []],
                    ['name' => 'Auto Shops',        'fields' => [['label'=>'Specialty','key'=>'specialty','type'=>'text','placeholder'=>'e.g. Tires, Oil Change, Body Shop']]],
                    ['name' => 'Hotels',            'fields' => [['label'=>'Stars','key'=>'stars','type'=>'select','options'=>['1 Star','2 Stars','3 Stars','4 Stars','5 Stars']]]],
                    ['name' => 'Travel Agencies',   'fields' => []],
                    ['name' => 'Financial Services','fields' => []],
                    ['name' => 'Real Estate Agents','fields' => []],
                    ['name' => 'Contractors',       'fields' => [['label'=>'Trade','key'=>'trade','type'=>'text','placeholder'=>'e.g. General, Electrical, Plumbing']]],
                    ['name' => 'Lawyers',           'fields' => [['label'=>'Practice Area','key'=>'practice_area','type'=>'text','placeholder'=>'e.g. Immigration, Family, Criminal']]],
                    ['name' => 'Accountants',       'fields' => []],
                ],
            ],
            [
                'name' => 'Deals & Coupons', 'icon' => '🏷️',
                'fields' => [
                    ['label' => 'Discount',    'key' => 'discount',    'type' => 'text', 'placeholder' => 'e.g. 30% off'],
                    ['label' => 'Valid Until', 'key' => 'valid_until', 'type' => 'text', 'placeholder' => 'e.g. Dec 31, 2025'],
                    ['label' => 'Coupon Code', 'key' => 'coupon_code', 'type' => 'text', 'placeholder' => 'e.g. SAVE20'],
                ],
                'subs' => [
                    ['name' => 'Grocery Deals',      'fields' => []],
                    ['name' => 'Restaurant Offers',  'fields' => []],
                    ['name' => 'Retail Discounts',   'fields' => []],
                    ['name' => 'Clearance Sales',    'fields' => []],
                    ['name' => 'Coupons',            'fields' => []],
                    ['name' => 'Daily Deals',        'fields' => []],
                ],
            ],
            [
                'name' => 'Events & Tickets', 'icon' => '🎫',
                'fields' => [
                    ['label' => 'Event Date',  'key' => 'event_date',  'type' => 'text', 'placeholder' => 'e.g. Mar 15, 2025', 'required' => true],
                    ['label' => 'Venue',       'key' => 'venue',       'type' => 'text', 'placeholder' => 'Venue name'],
                    ['label' => 'Ticket Price','key' => 'ticket_price','type' => 'text', 'placeholder' => 'e.g. $45 or Free'],
                    ['label' => 'Quantity',    'key' => 'quantity',    'type' => 'number','placeholder' => 'Number of tickets'],
                ],
                'subs' => [
                    ['name' => 'Concerts',            'fields' => [['label'=>'Artist','key'=>'artist','type'=>'text','placeholder'=>'e.g. Arijit Singh']]],
                    ['name' => 'Sports Events',       'fields' => [['label'=>'Sport','key'=>'sport','type'=>'text','placeholder'=>'e.g. Hockey, Cricket'],['label'=>'Teams','key'=>'teams','type'=>'text','placeholder'=>'e.g. Leafs vs Canadiens']]],
                    ['name' => 'Cultural Events',     'fields' => [['label'=>'Culture','key'=>'culture','type'=>'text','placeholder'=>'e.g. Navratri, Diwali Mela']]],
                    ['name' => 'Community Festivals', 'fields' => []],
                    ['name' => 'Theatre',             'fields' => [['label'=>'Show Name','key'=>'show_name','type'=>'text','placeholder'=>'e.g. The Lion King']]],
                    ['name' => 'Workshops',           'fields' => [['label'=>'Topic','key'=>'topic','type'=>'text','placeholder'=>'e.g. Photography, Cooking']]],
                    ['name' => 'Conferences',         'fields' => [['label'=>'Industry','key'=>'industry','type'=>'text','placeholder'=>'e.g. Tech, Healthcare, Business']]],
                    ['name' => 'Tickets Wanted',      'fields' => []],
                ],
            ],
            [
                'name' => 'Education', 'icon' => '🎓',
                'fields' => [
                    ['label' => 'Mode',     'key' => 'mode',     'type' => 'select', 'options' => ['In-Person','Online','Hybrid']],
                    ['label' => 'Schedule', 'key' => 'schedule', 'type' => 'text',   'placeholder' => 'e.g. Weekdays 6pm, Saturdays'],
                ],
                'subs' => [
                    ['name' => 'Schools',        'fields' => [['label'=>'Grade Range','key'=>'grade_range','type'=>'text','placeholder'=>'e.g. K-8, 9-12']]],
                    ['name' => 'Colleges',       'fields' => [['label'=>'Programs','key'=>'programs','type'=>'text','placeholder'=>'e.g. Business, IT, Nursing']]],
                    ['name' => 'Universities',   'fields' => [['label'=>'Programs','key'=>'programs','type'=>'text','placeholder'=>'e.g. Engineering, Medicine']]],
                    ['name' => 'Tutors',         'fields' => [['label'=>'Subject','key'=>'subject','type'=>'text','placeholder'=>'e.g. Math, Chemistry'],['label'=>'Grade Level','key'=>'grade_level','type'=>'text','placeholder'=>'e.g. Grade 10, University']]],
                    ['name' => 'Online Courses', 'fields' => [['label'=>'Platform','key'=>'platform','type'=>'text','placeholder'=>'e.g. Udemy, Own Platform']]],
                    ['name' => 'Driving Schools','fields' => [['label'=>'License Class','key'=>'license_class','type'=>'select','options'=>['G1/G2/G','M','AZ/DZ']]]],
                    ['name' => 'Music Classes',  'fields' => [['label'=>'Instrument','key'=>'instrument','type'=>'text','placeholder'=>'e.g. Guitar, Piano, Tabla']]],
                    ['name' => 'Language Classes','fields'=> [['label'=>'Language','key'=>'language','type'=>'text','placeholder'=>'e.g. English, French, Hindi']]],
                ],
            ],
            [
                'name' => 'Matrimony', 'icon' => '💍',
                'fields' => [
                    ['label' => 'Age',          'key' => 'age',          'type' => 'number', 'placeholder' => 'Age',                  'required' => true],
                    ['label' => 'Religion',     'key' => 'religion',     'type' => 'text',   'placeholder' => 'e.g. Hindu, Muslim, Sikh'],
                    ['label' => 'Community',    'key' => 'community',    'type' => 'text',   'placeholder' => 'e.g. Patel, Punjabi'],
                    ['label' => 'Education',    'key' => 'education',    'type' => 'text',   'placeholder' => 'e.g. MBA, Engineer'],
                    ['label' => 'Occupation',   'key' => 'occupation',   'type' => 'text',   'placeholder' => 'e.g. Doctor, IT Professional'],
                    ['label' => 'Height',       'key' => 'height',       'type' => 'text',   'placeholder' => "e.g. 5'6\""],
                    ['label' => 'Marital Status','key'=> 'marital_status','type'=> 'select',  'options' => ['Never Married','Divorced','Widowed']],
                    ['label' => 'Immigration Status','key'=>'immigration_status','type'=>'select','options'=>['Citizen','PR','Work Permit','Student']],
                ],
                'subs' => [
                    ['name' => 'Bride',              'fields' => []],
                    ['name' => 'Groom',              'fields' => []],
                    ['name' => 'Professional Match', 'fields' => [['label'=>'Profession','key'=>'profession','type'=>'text','placeholder'=>'e.g. Doctor, Engineer, Lawyer']]],
                    ['name' => 'Community Match',    'fields' => [['label'=>'Community','key'=>'community_match','type'=>'text','placeholder'=>'e.g. Gujarati, Punjabi, Tamil']]],
                    ['name' => 'NRI Match',          'fields' => [['label'=>'Country','key'=>'nri_country','type'=>'text','placeholder'=>'e.g. Canada, USA, UK']]],
                ],
            ],
            [
                'name' => 'Travel & Rideshare', 'icon' => '✈️',
                'fields' => [
                    ['label' => 'From',      'key' => 'from_location', 'type' => 'text', 'placeholder' => 'Departure city'],
                    ['label' => 'To',        'key' => 'to_location',   'type' => 'text', 'placeholder' => 'Destination city'],
                    ['label' => 'Date',      'key' => 'travel_date',   'type' => 'text', 'placeholder' => 'e.g. Jan 20, 2025'],
                    ['label' => 'Seats',     'key' => 'seats',         'type' => 'number','placeholder' => 'Available seats'],
                ],
                'subs' => [
                    ['name' => 'Carpool',           'fields' => [['label'=>'Route','key'=>'route','type'=>'text','placeholder'=>'e.g. Brampton to Downtown Toronto']]],
                    ['name' => 'Airport Ride',      'fields' => [['label'=>'Airport','key'=>'airport','type'=>'text','placeholder'=>'e.g. YYZ, YUL']]],
                    ['name' => 'Ride Sharing',      'fields' => []],
                    ['name' => 'Vacation Packages', 'fields' => [['label'=>'Duration','key'=>'duration','type'=>'text','placeholder'=>'e.g. 7 nights, 8 days'],['label'=>'Includes','key'=>'includes','type'=>'text','placeholder'=>'e.g. Hotel, Flights, Tours']]],
                    ['name' => 'Travel Partners',   'fields' => [['label'=>'Destination','key'=>'destination','type'=>'text','placeholder'=>'e.g. India, Europe, Caribbean']]],
                ],
            ],
            [
                'name' => 'Business Opportunities', 'icon' => '📈',
                'fields' => [
                    ['label' => 'Investment Required','key'=>'investment','type'=>'text','placeholder'=>'e.g. $50,000'],
                    ['label' => 'ROI Expected',      'key'=>'roi',       'type'=>'text','placeholder'=>'e.g. 20% annually'],
                ],
                'subs' => [
                    ['name' => 'Franchise Opportunities','fields'=>[['label'=>'Brand','key'=>'brand','type'=>'text','placeholder'=>'e.g. Tim Hortons, Subway']]],
                    ['name' => 'Businesses for Sale',   'fields'=>[['label'=>'Annual Revenue','key'=>'revenue','type'=>'text','placeholder'=>'e.g. $200K/year'],['label'=>'Reason for Sale','key'=>'reason','type'=>'text','placeholder'=>'e.g. Retirement']]],
                    ['name' => 'Investments',           'fields'=>[['label'=>'Sector','key'=>'sector','type'=>'text','placeholder'=>'e.g. Real Estate, Tech, Agriculture']]],
                    ['name' => 'Partnerships',          'fields'=>[['label'=>'Industry','key'=>'industry','type'=>'text','placeholder'=>'e.g. Food, IT, Construction']]],
                    ['name' => 'Distributors Wanted',   'fields'=>[['label'=>'Product','key'=>'product','type'=>'text','placeholder'=>'e.g. Food Products, Electronics']]],
                ],
            ],
            [
                'name' => 'Agriculture', 'icon' => '🌾',
                'fields' => [
                    ['label' => 'Quantity', 'key' => 'quantity', 'type' => 'text', 'placeholder' => 'e.g. 500 kg, 2 acres'],
                ],
                'subs' => [
                    ['name' => 'Farm Equipment', 'fields' => [['label'=>'Equipment','key'=>'equipment','type'=>'text','placeholder'=>'e.g. Tractor, Seeder, Sprayer'],['label'=>'Condition','key'=>'condition','type'=>'select','options'=>['New','Good','Fair']]]],
                    ['name' => 'Livestock',      'fields' => [['label'=>'Animal','key'=>'animal','type'=>'text','placeholder'=>'e.g. Cattle, Goats, Chickens']]],
                    ['name' => 'Seeds',          'fields' => [['label'=>'Crop','key'=>'crop','type'=>'text','placeholder'=>'e.g. Wheat, Canola, Corn']]],
                    ['name' => 'Fertilizers',    'fields' => [['label'=>'Type','key'=>'fertilizer_type','type'=>'text','placeholder'=>'e.g. Organic, NPK']]],
                    ['name' => 'Greenhouses',    'fields' => [['label'=>'Size','key'=>'size','type'=>'text','placeholder'=>'e.g. 1000 sq ft']]],
                ],
            ],
            [
                'name' => 'Industrial & Commercial', 'icon' => '🏭',
                'fields' => [
                    ['label' => 'Condition', 'key' => 'condition', 'type' => 'select', 'options' => ['New','Used','Refurbished']],
                    ['label' => 'Year',      'key' => 'year',      'type' => 'number', 'placeholder' => 'Year of manufacture'],
                ],
                'subs' => [
                    ['name' => 'Machinery',              'fields' => [['label'=>'Machine Type','key'=>'machine_type','type'=>'text','placeholder'=>'e.g. CNC, Lathe, Press']]],
                    ['name' => 'Manufacturing Equipment','fields' => []],
                    ['name' => 'Warehouse Equipment',    'fields' => [['label'=>'Equipment','key'=>'equipment','type'=>'text','placeholder'=>'e.g. Forklift, Shelving, Pallet Jack']]],
                    ['name' => 'Safety Equipment',       'fields' => [['label'=>'Type','key'=>'safety_type','type'=>'text','placeholder'=>'e.g. PPE, Fire Safety, Harness']]],
                ],
            ],
            [
                'name' => 'Health & Fitness', 'icon' => '💪',
                'fields' => [],
                'subs' => [
                    ['name' => 'Gym Memberships',   'fields' => [['label'=>'Gym Name','key'=>'gym_name','type'=>'text','placeholder'=>'e.g. GoodLife, LA Fitness'],['label'=>'Duration','key'=>'duration','type'=>'text','placeholder'=>'e.g. 6 months, 1 year']]],
                    ['name' => 'Personal Trainers', 'fields' => [['label'=>'Specialty','key'=>'specialty','type'=>'text','placeholder'=>'e.g. Weight Loss, Bodybuilding, Rehab']]],
                    ['name' => 'Yoga',              'fields' => [['label'=>'Level','key'=>'level','type'=>'select','options'=>['Beginner','Intermediate','Advanced','All Levels']]]],
                    ['name' => 'Martial Arts',      'fields' => [['label'=>'Style','key'=>'style','type'=>'text','placeholder'=>'e.g. Karate, BJJ, Muay Thai, MMA']]],
                    ['name' => 'Wellness Services', 'fields' => [['label'=>'Service','key'=>'wellness_service','type'=>'text','placeholder'=>'e.g. Massage, Acupuncture, Meditation']]],
                ],
            ],
            [
                'name' => 'Free Stuff', 'icon' => '🎁',
                'fields' => [
                    ['label' => 'Condition', 'key' => 'condition', 'type' => 'select', 'options' => ['Good','Fair','As-Is']],
                    ['label' => 'Reason',    'key' => 'reason',    'type' => 'text',   'placeholder' => 'Why are you giving it away?'],
                ],
                'subs' => [
                    ['name' => 'Furniture',          'fields' => []],
                    ['name' => 'Electronics',        'fields' => []],
                    ['name' => 'Household Items',    'fields' => []],
                    ['name' => 'Building Materials', 'fields' => []],
                    ['name' => 'Garden Supplies',    'fields' => []],
                    ['name' => 'Miscellaneous',      'fields' => []],
                ],
            ],
            [
                'name' => 'Wanted', 'icon' => '🔍',
                'fields' => [
                    ['label' => 'Budget', 'key' => 'budget', 'type' => 'text', 'placeholder' => 'e.g. Up to $500'],
                ],
                'subs' => [
                    ['name' => 'Wanted to Buy',    'fields' => [['label'=>'Item','key'=>'item','type'=>'text','placeholder'=>'What are you looking for?']]],
                    ['name' => 'Wanted to Rent',   'fields' => [['label'=>'Item/Space','key'=>'item','type'=>'text','placeholder'=>'e.g. Storage space, Parking spot']]],
                    ['name' => 'Wanted Jobs',      'fields' => [['label'=>'Skill','key'=>'skill','type'=>'text','placeholder'=>'e.g. Truck Driver, Software Developer']]],
                    ['name' => 'Wanted Services',  'fields' => [['label'=>'Service','key'=>'service','type'=>'text','placeholder'=>'e.g. Plumber, Accountant']]],
                    ['name' => 'Wanted Roommate',  'fields' => [['label'=>'Area','key'=>'area','type'=>'text','placeholder'=>'Preferred neighbourhood']]],
                ],
            ],
            [
                'name' => 'Garage Sales', 'icon' => '🏷️',
                'fields' => [
                    ['label' => 'Date',    'key' => 'sale_date', 'type' => 'text', 'placeholder' => 'e.g. Sat Jul 20, 8am–2pm'],
                    ['label' => 'Address', 'key' => 'address',   'type' => 'text', 'placeholder' => 'Full address'],
                ],
                'subs' => [
                    ['name' => 'Garage Sales', 'fields' => []],
                    ['name' => 'Estate Sales', 'fields' => []],
                    ['name' => 'Yard Sales',   'fields' => []],
                    ['name' => 'Flea Markets', 'fields' => [['label'=>'Booth Number','key'=>'booth','type'=>'text','placeholder'=>'e.g. Booth 42 (optional)']]],
                ],
            ],
            [
                'name' => 'Announcements', 'icon' => '📢',
                'fields' => [],
                'subs' => [
                    ['name' => 'Birthdays',          'fields' => [['label'=>'Name','key'=>'honoree','type'=>'text','placeholder'=>'Name of the birthday person']]],
                    ['name' => 'Engagements',        'fields' => [['label'=>'Couple','key'=>'couple','type'=>'text','placeholder'=>'e.g. Raj & Priya']]],
                    ['name' => 'Weddings',           'fields' => [['label'=>'Couple','key'=>'couple','type'=>'text','placeholder'=>'e.g. Arjun & Anisha'],['label'=>'Wedding Date','key'=>'wedding_date','type'=>'text','placeholder'=>'e.g. May 10, 2025']]],
                    ['name' => 'Birth Announcements','fields' => [['label'=>'Baby Name','key'=>'baby_name','type'=>'text','placeholder'=>"Baby's name (optional)"]]],
                    ['name' => 'Obituaries',         'fields' => [['label'=>'Name','key'=>'name','type'=>'text','placeholder'=>'Full name']]],
                    ['name' => 'Public Notices',     'fields' => []],
                ],
            ],
            [
                'name' => 'Lost & Found', 'icon' => '🔎',
                'fields' => [
                    ['label' => 'Type',     'key' => 'lf_type',  'type' => 'select', 'options' => ['Lost','Found'], 'required' => true],
                    ['label' => 'Date',     'key' => 'lf_date',  'type' => 'text',   'placeholder' => 'When was it lost/found?'],
                    ['label' => 'Location', 'key' => 'lf_location','type'=> 'text',  'placeholder' => 'Where?'],
                ],
                'subs' => [
                    ['name' => 'Lost Pets',  'fields' => [['label'=>'Pet Name','key'=>'pet_name','type'=>'text'],['label'=>'Breed','key'=>'breed','type'=>'text','placeholder'=>'e.g. Labrador, Persian Cat'],['label'=>'Colour','key'=>'colour','type'=>'text']]],
                    ['name' => 'Lost Items', 'fields' => [['label'=>'Item','key'=>'item','type'=>'text','placeholder'=>'What was lost?']]],
                    ['name' => 'Found Pets', 'fields' => [['label'=>'Pet Type','key'=>'pet_type','type'=>'text'],['label'=>'Breed','key'=>'breed','type'=>'text'],['label'=>'Colour','key'=>'colour','type'=>'text']]],
                    ['name' => 'Found Items','fields' => [['label'=>'Item','key'=>'item','type'=>'text','placeholder'=>'What was found?']]],
                ],
            ],
        ];

        $sort = 1;
        foreach ($data as $parent) {
            // Insert parent
            $parentSlug = Str::slug($parent['name']);
            $slugBase = $parentSlug; $i = 2;
            while (DB::table('categories')->where('slug', $parentSlug)->exists()) {
                $parentSlug = $slugBase . '-' . $i++;
            }

            $parentId = DB::table('categories')->insertGetId([
                'type'       => 'classifieds',
                'name'       => $parent['name'],
                'slug'       => $parentSlug,
                'icon'       => $parent['icon'],
                'parent_id'  => null,
                'is_active'  => 1,
                'sort_order' => $sort++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Parent-level custom fields
            foreach ($parent['fields'] as $fieldSort => $field) {
                DB::table('category_fields')->insert([
                    'category_id' => $parentId,
                    'label'       => $field['label'],
                    'key'         => $field['key'],
                    'type'        => $field['type'],
                    'options'     => isset($field['options']) ? json_encode($field['options']) : null,
                    'placeholder' => $field['placeholder'] ?? null,
                    'is_required' => !empty($field['required']),
                    'sort_order'  => $fieldSort,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            // Subcategories
            $subSort = 1;
            foreach ($parent['subs'] as $sub) {
                $subName = is_string($sub) ? $sub : $sub['name'];
                $subFields = is_array($sub) ? ($sub['fields'] ?? []) : [];

                $subSlug = Str::slug($subName);
                $slugBase = $subSlug; $i = 2;
                while (DB::table('categories')->where('slug', $subSlug)->exists()) {
                    $subSlug = $slugBase . '-' . $i++;
                }

                $subId = DB::table('categories')->insertGetId([
                    'type'       => 'classifieds',
                    'name'       => $subName,
                    'slug'       => $subSlug,
                    'icon'       => null,
                    'parent_id'  => $parentId,
                    'is_active'  => 1,
                    'sort_order' => $subSort++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Sub-specific custom fields
                foreach ($subFields as $fieldSort => $field) {
                    DB::table('category_fields')->insert([
                        'category_id' => $subId,
                        'label'       => $field['label'],
                        'key'         => $field['key'],
                        'type'        => $field['type'],
                        'options'     => isset($field['options']) ? json_encode($field['options']) : null,
                        'placeholder' => $field['placeholder'] ?? null,
                        'is_required' => !empty($field['required']),
                        'sort_order'  => $fieldSort,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }
        }

        $this->command->info('Classified categories + custom fields seeded successfully.');
    }
}
