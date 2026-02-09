<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="Πολιτική Cookies — Dressman"
            description="Πολιτική Cookies του ηλεκτρονικού καταστήματος Dressman. Μάθετε πώς χρησιμοποιούμε τα cookies."
        />
    </x-slot:seo>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900">Πολιτική Cookies</h1>
        <p class="mt-2 text-sm text-gray-500">Τελευταία ενημέρωση: {{ date('d/m/Y') }}</p>

        <div class="mt-8 prose prose-gray max-w-none">
            <h2>Τι είναι τα Cookies;</h2>
            <p>Τα cookies είναι μικρά αρχεία κειμένου που αποθηκεύονται στη συσκευή σας όταν επισκέπτεστε μια ιστοσελίδα. Μας βοηθούν να κάνουμε την ιστοσελίδα να λειτουργεί σωστά και να βελτιώνουμε την εμπειρία σας.</p>

            <h2>Κατηγορίες Cookies</h2>

            <h3>1. Απαραίτητα Cookies</h3>
            <p>Αυτά τα cookies είναι απολύτως απαραίτητα για τη λειτουργία της ιστοσελίδας. Δεν μπορούν να απενεργοποιηθούν.</p>
            <table>
                <thead><tr><th>Cookie</th><th>Σκοπός</th><th>Διάρκεια</th></tr></thead>
                <tbody>
                    <tr><td>dressman_session</td><td>Αναγνώριση συνεδρίας</td><td>2 ώρες</td></tr>
                    <tr><td>XSRF-TOKEN</td><td>Προστασία CSRF</td><td>2 ώρες</td></tr>
                    <tr><td>cookie_consent</td><td>Αποθήκευση προτιμήσεων cookies</td><td>1 έτος</td></tr>
                </tbody>
            </table>

            <h3>2. Cookies Ανάλυσης / Στατιστικών</h3>
            <p>Μας βοηθούν να κατανοήσουμε πώς χρησιμοποιούν οι επισκέπτες την ιστοσελίδα (π.χ. Google Analytics).</p>
            <table>
                <thead><tr><th>Cookie</th><th>Σκοπός</th><th>Διάρκεια</th></tr></thead>
                <tbody>
                    <tr><td>_ga</td><td>Google Analytics — αναγνώριση χρήστη</td><td>2 έτη</td></tr>
                    <tr><td>_ga_*</td><td>Google Analytics — κατάσταση συνεδρίας</td><td>2 έτη</td></tr>
                </tbody>
            </table>

            <h3>3. Cookies Marketing / Διαφήμισης</h3>
            <p>Χρησιμοποιούνται για την εμφάνιση σχετικών διαφημίσεων (π.χ. Facebook Pixel, Google Ads).</p>
            <table>
                <thead><tr><th>Cookie</th><th>Σκοπός</th><th>Διάρκεια</th></tr></thead>
                <tbody>
                    <tr><td>_fbp</td><td>Facebook Pixel — παρακολούθηση</td><td>3 μήνες</td></tr>
                    <tr><td>_gcl_au</td><td>Google Ads — μετατροπές</td><td>3 μήνες</td></tr>
                </tbody>
            </table>

            <h2>Πώς να Διαχειριστείτε τα Cookies</h2>
            <p>Μπορείτε να αλλάξετε τις προτιμήσεις σας ανά πάσα στιγμή κάνοντας κλικ στο κουμπί «Ρυθμίσεις Cookies» στο κάτω μέρος κάθε σελίδας, ή μέσω των ρυθμίσεων του προγράμματος περιήγησής σας.</p>

            <h2>Περισσότερες Πληροφορίες</h2>
            <p>Για θέματα απορρήτου, δείτε την <a href="{{ url('/privacy') }}">Πολιτική Απορρήτου</a> μας ή επικοινωνήστε μαζί μας στο <a href="mailto:info@dressman.gr">info@dressman.gr</a>.</p>
        </div>
    </div>
</x-layouts.app>
