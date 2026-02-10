<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="Σήματα για Πλύσιμο Ρούχων — Dressman"
            description="Οδηγός συμβόλων φροντίδας ρούχων. Μάθετε τι σημαίνουν τα σύμβολα πλυσίματος, στεγνώματος, σιδερώματος και στεγνού καθαρισμού."
        />
    </x-slot:seo>

    <x-slot:breadcrumb>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Σήματα Πλυσίματος'],
        ]" />
    </x-slot:breadcrumb>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900">Σήματα για Πλύσιμο Ρούχων</h1>
        <p class="mt-4 text-gray-600">Τα σύμβολα φροντίδας στις ετικέτες των ρούχων σας δείχνουν πώς να τα πλένετε, στεγνώνετε, σιδερώνετε και καθαρίζετε σωστά. Ακολουθήστε τις οδηγίες για να διατηρήσετε τα ρούχα σας σε άριστη κατάσταση.</p>

        <div class="mt-12 space-y-12">
            {{-- Πλύσιμο --}}
            <section>
                <h2 class="text-2xl font-semibold text-gray-900 border-b border-gray-200 pb-3">Πλύσιμο</h2>
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">🫧</span>
                        <div>
                            <p class="font-medium text-gray-900">Κανονικό πλύσιμο</p>
                            <p class="text-sm text-gray-600">Το ρούχο μπορεί να πλυθεί στο πλυντήριο σε κανονικό πρόγραμμα.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">30°</span>
                        <div>
                            <p class="font-medium text-gray-900">Πλύσιμο στους 30°C</p>
                            <p class="text-sm text-gray-600">Πλύνετε σε χαμηλή θερμοκρασία για ευαίσθητα υφάσματα.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">40°</span>
                        <div>
                            <p class="font-medium text-gray-900">Πλύσιμο στους 40°C</p>
                            <p class="text-sm text-gray-600">Κατάλληλο για τα περισσότερα καθημερινά ρούχα.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">60°</span>
                        <div>
                            <p class="font-medium text-gray-900">Πλύσιμο στους 60°C</p>
                            <p class="text-sm text-gray-600">Για ανθεκτικά υφάσματα που χρειάζονται βαθύ καθαρισμό.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">✋</span>
                        <div>
                            <p class="font-medium text-gray-900">Πλύσιμο στο χέρι</p>
                            <p class="text-sm text-gray-600">Μόνο πλύσιμο στο χέρι, με ήπιο απορρυπαντικό.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">🚫</span>
                        <div>
                            <p class="font-medium text-gray-900">Μην πλένετε</p>
                            <p class="text-sm text-gray-600">Το ρούχο δεν πρέπει να πλυθεί με νερό. Μόνο στεγνό καθαρισμό.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Στέγνωμα --}}
            <section>
                <h2 class="text-2xl font-semibold text-gray-900 border-b border-gray-200 pb-3">Στέγνωμα</h2>
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">⬜</span>
                        <div>
                            <p class="font-medium text-gray-900">Στέγνωμα σε στεγνωτήριο</p>
                            <p class="text-sm text-gray-600">Μπορείτε να χρησιμοποιήσετε στεγνωτήριο.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">🔘</span>
                        <div>
                            <p class="font-medium text-gray-900">Χαμηλή θερμοκρασία στεγνώματος</p>
                            <p class="text-sm text-gray-600">Στεγνωτήριο σε χαμηλή θερμοκρασία μόνο.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">🚫</span>
                        <div>
                            <p class="font-medium text-gray-900">Μη στεγνώνετε σε στεγνωτήριο</p>
                            <p class="text-sm text-gray-600">Αφήστε το ρούχο να στεγνώσει φυσικά, κρεμασμένο ή απλωμένο.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Σιδέρωμα --}}
            <section>
                <h2 class="text-2xl font-semibold text-gray-900 border-b border-gray-200 pb-3">Σιδέρωμα</h2>
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">♨️</span>
                        <div>
                            <p class="font-medium text-gray-900">Σιδέρωμα σε υψηλή θερμοκρασία</p>
                            <p class="text-sm text-gray-600">Έως 200°C. Κατάλληλο για βαμβακερά και λινά.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">🔸</span>
                        <div>
                            <p class="font-medium text-gray-900">Σιδέρωμα σε μεσαία θερμοκρασία</p>
                            <p class="text-sm text-gray-600">Έως 150°C. Για μαλλί και μείγματα πολυεστέρα.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">🔹</span>
                        <div>
                            <p class="font-medium text-gray-900">Σιδέρωμα σε χαμηλή θερμοκρασία</p>
                            <p class="text-sm text-gray-600">Έως 110°C. Για συνθετικά υφάσματα, ακρυλικό, νάιλον.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">🚫</span>
                        <div>
                            <p class="font-medium text-gray-900">Μη σιδερώνετε</p>
                            <p class="text-sm text-gray-600">Το ύφασμα μπορεί να καταστραφεί από τη θερμότητα.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Στεγνό Καθάρισμα --}}
            <section>
                <h2 class="text-2xl font-semibold text-gray-900 border-b border-gray-200 pb-3">Στεγνό Καθάρισμα</h2>
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">🅿️</span>
                        <div>
                            <p class="font-medium text-gray-900">Στεγνό καθάρισμα (P)</p>
                            <p class="text-sm text-gray-600">Επιτρέπεται στεγνό καθάρισμα με τα περισσότερα διαλύματα.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">🅰️</span>
                        <div>
                            <p class="font-medium text-gray-900">Στεγνό καθάρισμα (A)</p>
                            <p class="text-sm text-gray-600">Επιτρέπεται στεγνό καθάρισμα με οποιοδήποτε διάλυμα.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">🚫</span>
                        <div>
                            <p class="font-medium text-gray-900">Μη στεγνοκαθαρίζετε</p>
                            <p class="text-sm text-gray-600">Δεν επιτρέπεται κανένα είδος στεγνού καθαρισμού.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Λεύκανση --}}
            <section>
                <h2 class="text-2xl font-semibold text-gray-900 border-b border-gray-200 pb-3">Λεύκανση</h2>
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">△</span>
                        <div>
                            <p class="font-medium text-gray-900">Επιτρέπεται η λεύκανση</p>
                            <p class="text-sm text-gray-600">Μπορείτε να χρησιμοποιήσετε χλωρίνη ή άλλο λευκαντικό.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <span class="text-3xl flex-shrink-0">🚫</span>
                        <div>
                            <p class="font-medium text-gray-900">Μη λευκαίνετε</p>
                            <p class="text-sm text-gray-600">Μην χρησιμοποιείτε λευκαντικά ή χλωρίνη.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Tips --}}
            <section class="bg-gray-50 rounded-lg p-8">
                <h2 class="text-2xl font-semibold text-gray-900">Συμβουλές Φροντίδας Κοστουμιών</h2>
                <ul class="mt-4 space-y-3 text-gray-600">
                    <li class="flex items-start gap-2">
                        <span class="text-primary-600 mt-1">&#10003;</span>
                        <span>Κρεμάστε πάντα τα κοστούμια σε ξύλινη κρεμάστρα μετά τη χρήση.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-primary-600 mt-1">&#10003;</span>
                        <span>Αφήστε το κοστούμι να αερίζεται 24 ώρες πριν το αποθηκεύσετε.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-primary-600 mt-1">&#10003;</span>
                        <span>Προτιμήστε στεγνό καθαρισμό αντί πλύσιμο στο πλυντήριο.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-primary-600 mt-1">&#10003;</span>
                        <span>Σιδερώστε πάντα στην ανάποδη ή με πανί ενδιάμεσα.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-primary-600 mt-1">&#10003;</span>
                        <span>Αποθηκεύστε σε θήκη ρούχων μακριά από υγρασία και ηλιακό φως.</span>
                    </li>
                </ul>
            </section>
        </div>
    </div>
</x-layouts.app>
