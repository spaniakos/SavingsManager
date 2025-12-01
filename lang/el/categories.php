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
        'public_transport' => 'Δημόσιες Συγκοινωνίες',
        'car' => 'Αυτοκίνητο',
        'motorcycle' => 'Μοτοσυκλέτα',
        'car_maintenance' => 'Συντήρηση Αυτοκινήτου',
        'car_insurance' => 'Ασφάλεια Αυτοκινήτου',
        'self_insured' => 'Αυτοασφάλιση',
        'parking' => 'Στάθμευση',
        'tolls' => 'Διόδια',

        // Food
        'supermarket' => 'Σούπερ Μάρκετ',
        'groceries' => 'Είδη Προμήθειας',

        // Utilities
        'landline' => 'Σταθερό Τηλέφωνο',
        'mobile_phone' => 'Κινητό',
        'internet' => 'Ίντερνετ',

        // Health
        'medical_visits' => 'Ιατρικές Επισκέψεις',
        'medications' => 'Φάρμακα',
        'dental_care' => 'Οδοντιατρική',

        // Pets
        'vet' => 'Κτηνίατρος',
        'pet_food' => 'Τροφή Κατοικίδιων',

        // Work/Business
        'work_materials' => 'Υλικά Δουλειάς',
        'server' => 'Server',
        'domains' => 'Domains',
        'ai_services' => 'AI Υπηρεσίες',

        // Education
        'seminars' => 'Σεμινάρια',
        'tuition' => 'Δίδακτρα',
        'educational_materials' => 'Εκπαιδευτικό Υλικό',

        // Financial Obligations
        'loan_payments' => 'Πληρωμές Δανείων',
        'credit_card_payments' => 'Πληρωμές Πιστωτικών',

        // Lifestyle - Food & Drinks
        'restaurants' => 'Εστιατόρια',
        'coffee' => 'Καφές',
        'drinks' => 'Ποτά',
        'beers' => 'Μπύρες',

        // Lifestyle - Entertainment
        'subscriptions' => 'Συνδρομές',
        'gaming' => 'Παιχνίδια',
        'e_games' => 'Ηλεκτρονικά Παιχνίδια',
        'delivery' => 'Παράδοση',
        'gym' => 'Γυμναστήριο',
        'travel' => 'Ταξίδια',
        'events' => 'Εκδηλώσεις',

        // Lifestyle - Personal
        'clothing' => 'Ρουχισμός',
        'cosmetics' => 'Καλλυντικά',
        'hair_salon' => 'Κομμωτήριο',
        'gifts' => 'Δώρα',
        'tech' => 'Τεχνολογία',

        // Lifestyle - Insurance (optional)
        'life_insurance' => 'Ασφάλεια Ζωής',
        'home_insurance' => 'Ασφάλεια Κατοικίας',
        'private_health_insurance' => 'Ιδιωτική Ασφάλεια Υγείας',

        // Lifestyle - Children (optional)
        'childcare' => 'Παιδική Φροντίδα',
        'school' => 'Σχολείο',

        // Lifestyle - Soft categories
        'donations' => 'Δωρεές',
        'unexpected' => 'Απρόβλεπτα',
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
