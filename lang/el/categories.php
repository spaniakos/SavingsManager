<?php

return [
    'income' => [
        'salary' => 'Μισθός',
        'bonus' => 'Μπόνους',
        'raises' => 'Προσαυξήσεις',
        'business_income' => 'Επιχειρηματικά Έσοδα',
        'freelancer' => 'Ελεύθερος Επαγγελματίας',
        'property_rent' => 'Ενοίκια Ακινήτων',
        'vehicle_rent' => 'Ενοίκια Οχημάτων',
        'dividends' => 'Μερίσματα',
        'interest' => 'Τόκοι',
        'capital_gains' => 'Κεφαλαιακές Αξίες',
        'donations_received' => 'Δωρεές',
        'inheritance' => 'Κληρονομιές',
        'other_income' => 'Λοιπά Έσοδα',
    ],
    'expense_super' => [
        'housing' => 'Στέγαση',
        'transportation' => 'Μεταφορές',
        'food' => 'Τρόφιμα',
        'utilities' => 'Λογαριασμοί',
        'health' => 'Υγεία',
        'insurance' => 'Ασφάλειες',
        'education' => 'Εκπαίδευση',
        'entertainment' => 'Ψυχαγωγία',
        'personal' => 'Προσωπικά',
        'work_business' => 'Επαγγελματικά',
        'other' => 'Λοιπά',
    ],
    'expense' => [
        // Housing
        'rent' => 'Ενοίκιο',
        'utilities_common' => 'Κοινόχρηστα',
        'electricity_deh' => 'ΔΕΗ',
        'water' => 'Νερό',
        'home_insurance' => 'Ασφάλεια Κατοικίας',
        'maintenance' => 'Συντήρηση',
        'home_office' => 'Spiti-grafeio',
        
        // Transportation
        'fuel' => 'Καύσιμα',
        'car_maintenance' => 'Συντήρηση Αυτοκινήτου',
        'car_insurance' => 'Ασφάλεια Αυτοκινήτου',
        'tolls' => 'Διόδια',
        'public_transport' => 'Δημόσιες Συγκοινωνίες',
        'parking' => 'Στάθμευση',
        'car' => 'Amaksi',
        'motorcycle' => 'Mhxanh',
        'mobile_transport' => 'Kin',
        
        // Food
        'supermarket' => 'Σούπερ Μάρκετ',
        'restaurants' => 'Εστιατόρια',
        'coffee' => 'Καφές',
        'potatoes' => 'Patata',
        'groceries' => 'Keao',
        
        // Utilities
        'landline' => 'Σταθερό Τηλέφωνο',
        'mobile_phone' => 'Κινητό',
        'internet' => 'Ίντερνετ',
        'spotify' => 'Spotify',
        'netflix' => 'Netflix',
        'disney_plus' => 'Disney+',
        'log' => 'Log',
        'stathero' => 'Stathero',
        
        // Health
        'medical_visits' => 'Ιατρικές Επισκέψεις',
        'medications' => 'Φάρμακα',
        'dental_care' => 'Οδοντιατρική',
        'health_insurance' => 'Ασφάλεια Υγείας',
        
        // Insurance
        'efka' => 'ΕΦΚΑ',
        'life_insurance' => 'Ασφάλεια Ζωής',
        'home_insurance_insurance' => 'Ασφάλεια Κατοικίας',
        'car_insurance_insurance' => 'Ασφάλεια Αυτοκινήτου',
        'insurance' => 'Asfaleia',
        'tax' => 'Eforia',
        
        // Education
        'seminars' => 'Σεμινάρια',
        'tuition' => 'Δίδακτρα',
        'educational_materials' => 'Εκπαιδευτικό Υλικό',
        
        // Entertainment
        'gym' => 'Γυμναστήριο',
        'travel' => 'Ταξίδια',
        'events' => 'Εκδηλώσεις',
        'gymnastirio' => 'Gymnastirio',
        'taksidia' => 'Taksidia',
        
        // Personal
        'clothing' => 'Ρουχισμός',
        'cosmetics' => 'Καλλυντικά',
        'hair_salon' => 'Κομμωτήριο',
        'gifts' => 'Δώρα',
        'dwra' => 'Dwra',
        'personal_life' => 'Prosopikh zwh+',
        
        // Work/Business
        'work_materials' => 'Υλικά Δουλειάς',
        'server' => 'Server',
        'domains' => 'Domains',
        'ai_services' => 'AI Υπηρεσίες',
        'ylika_douleias' => 'Ylika douleias',
        'ai' => 'AI',
        'aproblepta' => 'Aproblepta',
        
        // Other
        'unexpected' => 'Απρόβλεπτα',
        'donations' => 'Δωρεές',
        'other_expenses' => 'Λοιπά Έξοδα',
        'eksodoi' => 'Eksodoi',
        'erini' => 'Erini',
        'spanos' => 'Spanos',
        'revma' => 'Revma',
        'super' => 'Super',
        'loipa' => 'Loipa',
    ],
    
    // Category Management
    'translation_key' => 'Κλειδί Μετάφρασης',
    'translation_key_help' => 'Εισάγετε ένα κλειδί μετάφρασης (π.χ., categories.income.salary). Αυτό θα χρησιμοποιηθεί για πολυγλωσσική υποστήριξη.',
    'translation_info' => 'Πληροφορίες Μετάφρασης',
    'translation_instructions' => 'Για να προσθέσετε μεταφράσεις, προσθέστε καταχωρήσεις στα αρχεία lang/en/categories.php και lang/el/categories.php. Για παράδειγμα, αν το κλειδί σας είναι "categories.income.my_category", προσθέστε "my_category" => "Η Κατηγορία μου" στον πίνακα income.',
    'system_category' => 'Κατηγορία Συστήματος',
    'created_by' => 'Δημιουργήθηκε Από',
    'system' => 'Σύστημα',
    'usage_count' => 'Αριθμός Χρήσεων',
    'type' => 'Τύπος',
    'system_categories' => 'Κατηγορίες Συστήματος',
    'custom_categories' => 'Προσαρμοσμένες Κατηγορίες',
    'cannot_delete_system' => 'Δεν μπορείτε να διαγράψετε κατηγορίες συστήματος',
];

