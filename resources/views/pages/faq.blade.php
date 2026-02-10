<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="Συχνές Ερωτήσεις (FAQ) — Dressman"
            description="Βρείτε απαντήσεις στις πιο συχνές ερωτήσεις σχετικά με παραγγελίες, αποστολές, επιστροφές και υπηρεσίες της Dressman."
        />
    </x-slot:seo>

    <x-slot:breadcrumb>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'FAQ'],
        ]" />
    </x-slot:breadcrumb>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900">Συχνές Ερωτήσεις</h1>

        <div class="mt-8 space-y-6" x-data="{ open: null }">
            {{-- Question 1 --}}
            <div class="border border-gray-200 rounded-lg">
                <button type="button" @click="open = open === 1 ? null : 1" class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-base font-medium text-gray-900">Πώς μπορώ να κάνω μια παραγγελία;</span>
                    <svg class="h-5 w-5 text-gray-500 transition-transform" :class="open === 1 && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>
                <div x-show="open === 1" x-collapse x-cloak class="px-6 pb-4">
                    <p class="text-sm text-gray-600 leading-relaxed">Επιλέξτε τα προϊόντα που επιθυμείτε, προσθέστε τα στο καλάθι σας και ολοκληρώστε την παραγγελία σας μέσω της σελίδας checkout. Μπορείτε επίσης να επικοινωνήσετε μαζί μας τηλεφωνικά για βοήθεια με την παραγγελία σας.</p>
                </div>
            </div>

            {{-- Question 2 --}}
            <div class="border border-gray-200 rounded-lg">
                <button type="button" @click="open = open === 2 ? null : 2" class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-base font-medium text-gray-900">Ποιοι είναι οι τρόποι πληρωμής;</span>
                    <svg class="h-5 w-5 text-gray-500 transition-transform" :class="open === 2 && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>
                <div x-show="open === 2" x-collapse x-cloak class="px-6 pb-4">
                    <p class="text-sm text-gray-600 leading-relaxed">Δεχόμαστε πληρωμή με πιστωτική/χρεωστική κάρτα, αντικαταβολή και τραπεζική κατάθεση. Για περισσότερες πληροφορίες επισκεφτείτε τη σελίδα <a href="{{ url('/payment-methods') }}" class="text-primary-600 hover:text-primary-800 underline">Τρόποι Πληρωμής</a>.</p>
                </div>
            </div>

            {{-- Question 3 --}}
            <div class="border border-gray-200 rounded-lg">
                <button type="button" @click="open = open === 3 ? null : 3" class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-base font-medium text-gray-900">Πόσο κοστίζουν τα μεταφορικά;</span>
                    <svg class="h-5 w-5 text-gray-500 transition-transform" :class="open === 3 && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>
                <div x-show="open === 3" x-collapse x-cloak class="px-6 pb-4">
                    <p class="text-sm text-gray-600 leading-relaxed">Τα μεταφορικά εξαρτώνται από τον τόπο αποστολής. Για παραγγελίες άνω ενός συγκεκριμένου ποσού, τα μεταφορικά είναι δωρεάν. Δείτε αναλυτικά στη σελίδα <a href="{{ url('/shipping') }}" class="text-primary-600 hover:text-primary-800 underline">Αποστολές</a>.</p>
                </div>
            </div>

            {{-- Question 4 --}}
            <div class="border border-gray-200 rounded-lg">
                <button type="button" @click="open = open === 4 ? null : 4" class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-base font-medium text-gray-900">Μπορώ να επιστρέψω ένα προϊόν;</span>
                    <svg class="h-5 w-5 text-gray-500 transition-transform" :class="open === 4 && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>
                <div x-show="open === 4" x-collapse x-cloak class="px-6 pb-4">
                    <p class="text-sm text-gray-600 leading-relaxed">Ναι, μπορείτε να επιστρέψετε τα προϊόντα εντός 14 ημερών από την παραλαβή τους, εφόσον βρίσκονται στην αρχική τους κατάσταση. Δείτε αναλυτικά στη σελίδα <a href="{{ url('/returns') }}" class="text-primary-600 hover:text-primary-800 underline">Επιστροφές</a>.</p>
                </div>
            </div>

            {{-- Question 5 --}}
            <div class="border border-gray-200 rounded-lg">
                <button type="button" @click="open = open === 5 ? null : 5" class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-base font-medium text-gray-900">Πόσο χρόνο παίρνει η αποστολή;</span>
                    <svg class="h-5 w-5 text-gray-500 transition-transform" :class="open === 5 && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>
                <div x-show="open === 5" x-collapse x-cloak class="px-6 pb-4">
                    <p class="text-sm text-gray-600 leading-relaxed">Οι παραγγελίες αποστέλλονται εντός 1-3 εργάσιμων ημερών. Ο χρόνος παράδοσης εξαρτάται από την περιοχή σας και συνήθως είναι 1-2 εργάσιμες ημέρες για Αττική και 2-4 εργάσιμες ημέρες για υπόλοιπη Ελλάδα.</p>
                </div>
            </div>

            {{-- Question 6 --}}
            <div class="border border-gray-200 rounded-lg">
                <button type="button" @click="open = open === 6 ? null : 6" class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-base font-medium text-gray-900">Κάνετε μεταποιήσεις;</span>
                    <svg class="h-5 w-5 text-gray-500 transition-transform" :class="open === 6 && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>
                <div x-show="open === 6" x-collapse x-cloak class="px-6 pb-4">
                    <p class="text-sm text-gray-600 leading-relaxed">Ναι, η Dressman διαθέτει υπηρεσία μεταποιήσεων με πάνω από 40 χρόνια εμπειρίας. Μάθετε περισσότερα στη σελίδα <a href="{{ url('/metapoiisi') }}" class="text-primary-600 hover:text-primary-800 underline">Μεταποίηση</a>.</p>
                </div>
            </div>

            {{-- Question 7 --}}
            <div class="border border-gray-200 rounded-lg">
                <button type="button" @click="open = open === 7 ? null : 7" class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-base font-medium text-gray-900">Μπορώ να ενοικιάσω κοστούμι;</span>
                    <svg class="h-5 w-5 text-gray-500 transition-transform" :class="open === 7 && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>
                <div x-show="open === 7" x-collapse x-cloak class="px-6 pb-4">
                    <p class="text-sm text-gray-600 leading-relaxed">Ναι, προσφέρουμε υπηρεσία ενοικίασης σμόκιν και κοστουμιών για γάμους και ειδικές εκδηλώσεις. Επισκεφτείτε τη σελίδα <a href="{{ url('/product-category/rent') }}" class="text-primary-600 hover:text-primary-800 underline">Ενοικίαση Σμόκιν</a> για περισσότερες πληροφορίες.</p>
                </div>
            </div>

            {{-- Question 8 --}}
            <div class="border border-gray-200 rounded-lg">
                <button type="button" @click="open = open === 8 ? null : 8" class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-base font-medium text-gray-900">Τι είναι η υπηρεσία Made to Measure;</span>
                    <svg class="h-5 w-5 text-gray-500 transition-transform" :class="open === 8 && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>
                <div x-show="open === 8" x-collapse x-cloak class="px-6 pb-4">
                    <p class="text-sm text-gray-600 leading-relaxed">Η υπηρεσία Made to Measure σας επιτρέπει να σχεδιάσετε το κοστούμι σας ακριβώς όπως το θέλετε — επιλέγοντας ύφασμα, χρώμα, λεπτομέρειες και μέγεθος στα μέτρα σας. Μάθετε περισσότερα στη σελίδα <a href="{{ url('/made-to-measure') }}" class="text-primary-600 hover:text-primary-800 underline">Made to Measure</a>.</p>
                </div>
            </div>

            {{-- Question 9 --}}
            <div class="border border-gray-200 rounded-lg">
                <button type="button" @click="open = open === 9 ? null : 9" class="w-full flex items-center justify-between px-6 py-4 text-left">
                    <span class="text-base font-medium text-gray-900">Πώς μπορώ να επικοινωνήσω μαζί σας;</span>
                    <svg class="h-5 w-5 text-gray-500 transition-transform" :class="open === 9 && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </button>
                <div x-show="open === 9" x-collapse x-cloak class="px-6 pb-4">
                    <p class="text-sm text-gray-600 leading-relaxed">Μπορείτε να μας καλέσετε, να μας στείλετε email ή να επισκεφτείτε ένα από τα καταστήματά μας. Δείτε όλα τα στοιχεία επικοινωνίας στη σελίδα <a href="{{ url('/contact') }}" class="text-primary-600 hover:text-primary-800 underline">Επικοινωνία</a>.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
