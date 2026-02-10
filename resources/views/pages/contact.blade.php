<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="Επικοινωνία — Dressman"
            description="Επικοινωνήστε μαζί μας. Dressman — Ανδρικά ρούχα υψηλής ποιότητας στην Αθήνα από το 1974."
        />
    </x-slot:seo>

    <x-slot:breadcrumb>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Επικοινωνία'],
        ]" />
    </x-slot:breadcrumb>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-3xl font-bold text-gray-900">Επικοινωνία</h1>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-12">
            {{-- Store 1 --}}
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Boutique — Κολωνάκι</h2>
                <div class="mt-4 space-y-3 text-gray-600">
                    <p>
                        <strong>Διεύθυνση:</strong><br>
                        Σκουφά 10, 10673 Κολωνάκι, Αθήνα
                    </p>
                    <p>
                        <strong>Τηλέφωνο:</strong><br>
                        <a href="tel:+302155004038" class="text-primary-600 hover:text-primary-800">+30 215 500 4038</a>
                    </p>
                </div>
            </div>

            {{-- Store 2 --}}
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Boutique & Stockhouse — Άνω Λιόσια</h2>
                <div class="mt-4 space-y-3 text-gray-600">
                    <p>
                        <strong>Διεύθυνση:</strong><br>
                        Λεωφόρος Φυλής 116, 13341 Α. Λιόσια, Αθήνα
                    </p>
                    <p>
                        <strong>Τηλέφωνο:</strong><br>
                        <a href="tel:+302102483370" class="text-primary-600 hover:text-primary-800">+30 210 2483 370</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Email --}}
        <div class="mt-12 pt-8 border-t border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Email</h2>
            <p class="mt-4 text-gray-600">
                <a href="mailto:info@dressman.gr" class="text-primary-600 hover:text-primary-800">info@dressman.gr</a>
            </p>
        </div>
    </div>
</x-layouts.app>
