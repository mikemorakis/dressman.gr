<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="Made to Measure — Dressman"
            description="Σχεδιάστε το κοστούμι σας όπως το θέλετε. Made to Measure υπηρεσία από τη Dressman — δεν είναι απλά ένα ρούχο, είναι τρόπος ζωής."
        />
    </x-slot:seo>

    <x-slot:breadcrumb>
        <x-breadcrumb :items="[['label' => 'Made to Measure']]" />
    </x-slot:breadcrumb>

    {{-- Hero --}}
    <section class="relative bg-black overflow-hidden">
        <div class="absolute inset-0">
            <x-picture src="images/mtm-hero.jpeg" alt="Made to Measure Dressman" class="w-full h-full object-cover" loading="eager" fetchpriority="high" />
        </div>
        <div class="absolute inset-0" style="background:rgba(0,0,0,0.6)"></div>
        <div class="relative flex items-center justify-center h-[50vh] sm:h-[60vh]">
            <div class="max-w-3xl mx-auto px-6 text-center text-white">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight">Made to Measure</h1>
                <p class="mt-2 text-lg sm:text-xl text-white/80 italic">The Luxury is Style</p>
                <p class="mt-6 text-base sm:text-lg leading-relaxed text-white/90">
                    Δεν είναι απλά ένα ρούχο, είναι τρόπος ζωής.
                </p>
            </div>
        </div>
    </section>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        {{-- Intro --}}
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Σχεδιάστε το Κοστούμι σας Όπως το Θέλετε</h2>
            <p class="mt-4 text-gray-600 leading-relaxed">
                Η αφοσίωσή μας στην ποιότητα και τη χειροποίητη δεξιοτεχνία αποτελεί την παράδοσή μας. Κάθε κοστούμι Made to Measure δημιουργείται αποκλειστικά για εσάς, βάσει των μέτρων σας και των προσωπικών σας επιλογών.
            </p>
        </div>

        {{-- Process image --}}
        <div class="mt-12">
            <x-picture src="images/mtm-process.jpg" alt="Bespoke tailoring process" class="w-full h-auto" />
        </div>

        {{-- Features with images --}}
        <div class="mt-16 grid sm:grid-cols-2 gap-12 items-start">
            <div>
                <x-picture src="images/mtm-shirt.jpg" alt="Made to Measure πουκάμισο" class="w-full h-auto" />
            </div>
            <div class="space-y-8 sm:pt-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Εξατομίκευση Λεπτομερειών</h3>
                    <p class="mt-2 text-gray-600 leading-relaxed">
                        Επιλέξτε πέτα, τσέπες, φόδρα, κουμπιά και κάθε λεπτομέρεια του Custom Suit σας. Κάθε στοιχείο προσαρμόζεται στο στυλ και τις ανάγκες σας.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Χειροποίητο Φινίρισμα</h3>
                    <p class="mt-2 text-gray-600 leading-relaxed">
                        Οι τελευταίες πινελιές γίνονται στο χέρι. Κάθε ρούχο ολοκληρώνεται σε περίπου ένα μήνα, με προσοχή σε κάθε ραφή.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-16 grid sm:grid-cols-2 gap-12 items-start">
            <div class="space-y-8 sm:pt-4 order-2 sm:order-1">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Επιλογή Υφασμάτων</h3>
                    <p class="mt-2 text-gray-600 leading-relaxed">
                        Μεγάλη ποικιλία υφασμάτων υψηλής ποιότητας. Σας στέλνουμε δωρεάν δείγματα υφασμάτων στο σπίτι σας.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Πέρα από το Κοστούμι</h3>
                    <p class="mt-2 text-gray-600 leading-relaxed">
                        Η υπηρεσία Made to Measure επεκτείνεται σε πουκάμισα, παντελόνια, πλεκτά και αξεσουάρ. Κάθε ρούχο φτιαγμένο στα μέτρα σας.
                    </p>
                </div>
            </div>
            <div class="order-1 sm:order-2">
                <x-picture src="images/mtm-fabric-1.jpeg" alt="Επιλογή υφασμάτων" class="w-full h-auto" />
            </div>
        </div>

        {{-- Tuxedo image --}}
        <div class="mt-16 max-w-sm mx-auto">
            <x-picture src="images/mtm-tuxedo.jpg" alt="Made to Measure white tuxedo" class="w-full h-auto" />
        </div>

        {{-- CTA --}}
        <div class="mt-16 text-center bg-gray-50 py-10 px-6">
            <h2 class="text-xl font-bold text-gray-900">Κλείστε Ραντεβού</h2>
            <p class="mt-2 text-gray-600">Επισκεφθείτε μας για να σχεδιάσουμε μαζί το δικό σας κοστούμι.</p>
            <div class="mt-6 space-y-2 text-sm text-gray-700">
                <p><strong>Boutique:</strong> Σκουφά 10, Κολωνάκι, Αθήνα &mdash; <a href="tel:+302155004038" class="underline hover:text-black">215 500 4038</a></p>
                <p><strong>Boutique &amp; Stockhouse:</strong> Λεωφ. Φυλής 116, Άνω Λιόσια &mdash; <a href="tel:+302102483370" class="underline hover:text-black">210 248 3370</a></p>
            </div>
        </div>
    </div>
</x-layouts.app>
