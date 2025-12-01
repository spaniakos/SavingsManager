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
        'essentials' => 'Αναγκαία',
        'lifestyle' => 'Τρόπος Ζωής',
        'savings' => 'Οικονομίες',
    ],
    'expense' => [
        // Housing
        'rent' => 'Ενοίκιο',
        'utilities_common' => 'Κοινόχρηστα',
        'electricity_deh' => 'ΔΕΗ',
        'water' => 'Νερό',
        'home_insurance' => 'Ασφάλεια Κατοικίας',
        'maintenance' => 'Συντήρηση',
        'home_office' => 'Σπίτι-Γραφείο',
        
        // Transportation
        'fuel' => 'Καύσιμα',
        'car_maintenance' => 'Συντήρηση Αυτοκινήτου',
        'car_insurance' => 'Ασφάλεια Αυτοκινήτου',
        'tolls' => 'Διόδια',
        'public_transport' => 'Δημόσιες Συγκοινωνίες',
        'parking' => 'Στάθμευση',
        'car' => 'Αυτοκίνητο',
        'motorcycle' => 'Μοτοσυκλέτα',
        'mobile_transport' => 'Κινητό (Μεταφορές)',
        
        // Food
        'supermarket' => 'Σούπερ Μάρκετ',
        'restaurants' => 'Εστιατόρια',
        'coffee' => 'Καφές',
        'drinks' => 'Ποτά',
        'beers' => 'Μπύρες',
        'potatoes' => 'Πατάτες',
        'groceries' => 'Είδη Προμήθειας',
        
        // Utilities
        'landline' => 'Σταθερό Τηλέφωνο',
        'mobile_phone' => 'Κινητό',
        'internet' => 'Ίντερνετ',
        'spotify' => 'Spotify',
        'netflix' => 'Netflix',
        'disney_plus' => 'Disney+',
        'log' => 'Κούτσουρο',
        'stathero' => 'Σταθερό',
        
        // Health
        'medical_visits' => 'Ιατρικές Επισκέψεις',
        'medications' => 'Φάρμακα',
        'dental_care' => 'Οδοντιατρική',
        'health_insurance' => 'Ασφάλεια Υγείας',
        
        // Insurance
        'self_insured' => 'Αυτοασφάλιση',
        'life_insurance' => 'Ασφάλεια Ζωής',
        'home_insurance_insurance' => 'Ασφάλεια Κατοικίας',
        'car_insurance_insurance' => 'Ασφάλεια Αυτοκινήτου',
        'insurance' => 'Ασφάλεια',
        'tax' => 'Φόρος',
        
        // Education
        'seminars' => 'Σεμινάρια',
        'tuition' => 'Δίδακτρα',
        'educational_materials' => 'Εκπαιδευτικό Υλικό',
        
        // Entertainment
        'gym' => 'Γυμναστήριο',
        'travel' => 'Ταξίδια',
        'events' => 'Εκδηλώσεις',
        'gymnastirio' => 'Γυμναστήριο',
        'taksidia' => 'Ταξίδια',
        
        // Personal
        'clothing' => 'Ρουχισμός',
        'cosmetics' => 'Καλλυντικά',
        'hair_salon' => 'Κομμωτήριο',
        'gifts' => 'Δώρα',
        'tech' => 'Τεχνολογία',
        'dwra' => 'Δώρα',
        'personal_life' => 'Προσωπική Ζωή',
        
        // Work/Business
        'work_materials' => 'Υλικά Δουλειάς',
        'server' => 'Server',
        'domains' => 'Domains',
        'ai_services' => 'AI Υπηρεσίες',
        'ylika_douleias' => 'Υλικά Δουλειάς',
        'ai' => 'AI',
        'aproblepta' => 'Απρόβλεπτα',
        
        // Other
        'unexpected' => 'Απρόβλεπτα',
        'donations' => 'Δωρεές',
        'other_expenses' => 'Λοιπά Έξοδα',
        'eksodoi' => 'Έξοδα',
        'erini' => 'Ερήνη',
        'spanos' => 'Σπανός',
        'revma' => 'Ρεύμα',
        'super' => 'Σούπερ',
        'loipa' => 'Λοιπά',
        'savings' => 'Αποταμιεύσεις',
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

