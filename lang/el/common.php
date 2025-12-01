<?php

return [
    'app_name' => 'Διαχειριστής Αποταμίευσης',
    'dashboard' => 'Πίνακας Ελέγχου',
    'income' => 'Έσοδα',
    'expenses' => 'Έξοδα',
    'savings_goals' => 'Στόχοι Αποταμίευσης',
    'reports' => 'Αναφορές',
    'settings' => 'Ρυθμίσεις',
    
    // Common actions
    'create' => 'Δημιουργία',
    'edit' => 'Επεξεργασία',
    'delete' => 'Διαγραφή',
    'save' => 'Αποθήκευση',
    'cancel' => 'Ακύρωση',
    'back' => 'Πίσω',
    'search' => 'Αναζήτηση',
    'yes' => 'Ναι',
    'no' => 'Όχι',
    'filter' => 'Φίλτρο',
    'export' => 'Εξαγωγή',
    'import' => 'Εισαγωγή',
    'actions' => 'Ενέργειες',
    
    // Common fields
    'name' => 'Όνομα',
    'amount' => 'Ποσό',
    'date' => 'Ημερομηνία',
    'notes' => 'Σημειώσεις',
    'category' => 'Κατηγορία',
    'super_category' => 'Υπερ-Κατηγορία',
    'created_at' => 'Δημιουργήθηκε',
    'updated_at' => 'Ενημερώθηκε',
    
    // Income
    'income_category' => 'Κατηγορία Εσόδων',
    'income_entry' => 'Καταχώρηση Εσόδων',
    'income_entries' => 'Καταχωρήσεις Εσόδων',
    'add_income' => 'Προσθήκη Εσόδων',
    'total_income' => 'Συνολικά Έσοδα',
    
    // Expenses
    'expense_category' => 'Κατηγορία Εξόδων',
    'expense_entry' => 'Καταχώρηση Εξόδων',
    'expense_entries' => 'Καταχωρήσεις Εξόδων',
    'add_expense' => 'Προσθήκη Εξόδων',
    'total_expenses' => 'Συνολικά Έξοδα',
    
    // Savings Goals
    'goal_name' => 'Όνομα Στόχου',
    'target_amount' => 'Ποσό Στόχου',
    'current_amount' => 'Τρέχον Ποσό',
    'start_date' => 'Ημερομηνία Έναρξης',
    'target_date' => 'Ημερομηνία Στόχου',
    'progress' => 'Πρόοδος',
    'joint_goal' => 'Κοινός Στόχος',
    'individual' => 'Ατομικός',
    'joint' => 'Κοινός',
    'members' => 'Μέλη',
    'type' => 'Τύπος',
    'monthly_saving_needed' => 'Απαιτούμενη Μηνιαία Αποταμίευση',
    'months_remaining' => 'Απομένουσες Μήνες',
    'if_no_spending' => 'Αν δεν ξοδέψετε τίποτα περισσότερο μέχρι το τέλος του μήνα, θα αποταμιεύσετε',
    'overall' => 'Συνολικά',
    'monthly' => 'Μηνιαία',
    'current_month_savings' => 'Αποταμίευση Τρέχοντος Μήνα',
    'projected_savings' => 'Εκτιμώμενη Αποταμίευση',
    'no_savings_goals' => 'Δεν βρέθηκαν στόχοι αποταμίευσης. Δημιουργήστε έναν για να ξεκινήσετε!',
    
    // Reports
    'monthly_report' => 'Μηνιαία Αναφορά',
    'category_report' => 'Αναφορά Κατηγορίας',
    'savings_report' => 'Αναφορά Αποταμίευσης',
    'mom_comparison' => 'Σύγκριση Μήνα προς Μήνα',
    'expenses_by_category' => 'Έξοδα ανά Κατηγορία',
    'income_trends' => 'Τάσεις Εσόδων',
    'uncategorized' => 'Μη Κατηγοριοποιημένα',
    
    // Messages
    'created_successfully' => 'Δημιουργήθηκε επιτυχώς',
    'updated_successfully' => 'Ενημερώθηκε επιτυχώς',
    'deleted_successfully' => 'Διαγράφηκε επιτυχώς',
    'are_you_sure' => 'Είστε σίγουροι;',
    'this_action_cannot_be_undone' => 'Αυτή η ενέργεια δεν μπορεί να αναιρεθεί.',
    'allow_other_users_to_contribute' => 'Επιτρέψτε σε άλλους χρήστες να συνεισφέρουν σε αυτόν τον στόχο',
    
    // Category Management
    'category_management' => 'Διαχείριση Κατηγοριών',
    'income_categories' => 'Κατηγορίες Εσόδων',
    'expense_categories' => 'Κατηγορίες Εξόδων',
    'expense_super_categories' => 'Υπερ-Κατηγορίες Εξόδων',
    
    // Financial Settings
    'seed_capital' => 'Αρχικό Κεφάλαιο',
    'median_monthly_income' => 'Μέσος Μηνιαίος Εισόδημα',
    'income_last_verified_at' => 'Εισόδημα Επαληθεύτηκε Τελευταία',
    'verify_income' => 'Επαλήθευση Εισοδήματος',
    'net_worth' => 'Καθαρή Αξία',
    
    // Recurring Expenses
    'recurring_expenses' => 'Παρα recurring Έξοδα',
    'recurring_expense' => 'Παρα recurring Έξοδο',
    'frequency' => 'Συχνότητα',
    'week' => 'Εβδομάδα',
    'month' => 'Μήνας',
    'quarter' => 'Τρίμηνο',
    'year' => 'Έτος',
    'start_date' => 'Ημερομηνία Έναρξης',
    'end_date' => 'Ημερομηνία Λήξης',
    'is_active' => 'Ενεργό',
    'last_generated_at' => 'Τελευταία Δημιουργία',
    'next_due_date' => 'Επόμενη Προθεσμία',
    'generate_expenses' => 'Δημιουργία Εξόδων',
    'recurring_expense_generated' => 'Παρα recurring έξοδο: :name',
    
    // Save for Later
    'save_for_later' => 'Αποταμίευση για Μετά',
    'save_for_later_target' => 'Στόχος Αποταμίευσης',
    'save_for_later_frequency' => 'Συχνότητα Αποταμίευσης',
    'save_for_later_amount' => 'Ποσό ανά Περίοδο',
    'save_progress' => 'Πρόοδος Αποταμίευσης',
    'remaining_to_save' => 'Απομένει να Αποταμιευτεί',
    
    // Budget Allocation
    'budget_allocation' => 'Κατανομή Προϋπολογισμού',
    'allocation_percentage' => 'Ποσοστό Κατανομής',
    'allowance' => 'Προϋπόλογισμός',
    'spent' => 'Ξοδεύτηκε',
    'remaining_allowance' => 'Απομένων Προϋπόλογισμός',
    'over_budget' => 'Υπέρβαση Προϋπολογισμού',
    'under_budget' => 'Κάτω από Προϋπολογισμό',
    
    // Positive Reinforcement
    'encouragement_days_remaining' => 'Μπράβο! Ο μήνας λήγει σε :days ημέρες και έχετε ακόμα :amount προϋπόλογισμο να ξοδέψετε!',
    'encouragement_excellent' => 'Τέλεια! Έχετε ξοδέψει μόνο :spent από :allowance αυτόν τον μήνα στο :category!',
    'encouragement_good' => 'Καλή πρόοδος! Έχετε ξοδέψει :spent από :allowance στο :category, με :remaining που απομένουν.',
    'encouragement_ok' => 'Έχετε ακόμα :remaining που απομένουν στο :category αυτόν τον μήνα.',
    
    // Savings Goal Checkpoint
    'initial_checkpoint' => 'Αρχικό Σημείο Ελέγχου',
    'initial_checkpoint_help' => 'Το ποσό που είχε ήδη αποταμιευτεί όταν δημιουργήθηκε αυτός ο στόχος',
    'expenses_generated' => ':count έξοδο(α) δημιουργήθηκαν επιτυχώς',
    'positive_reinforcement' => 'Θετική Ενίσχυση',
    'no_save_for_later_categories' => 'Δεν βρέθηκαν κατηγορίες με στόχους αποταμίευσης.',
    'seed_capital_help' => 'Το αρχικό σας κεφάλαιο ή υπόλοιπο',
    'median_monthly_income_help' => 'Το τυπικό σας μηνιαίο εισόδημα για υπολογισμούς προϋπολογισμού',
    'income_last_verified_at_help' => 'Πότε επαληθεύσατε τελευταία το μηνιαίο σας εισόδημα',
    'financial_settings' => 'Οικονομικές Ρυθμίσεις',
];

