<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('seo', null, []); ?> 
        <?php if (isset($component)) { $__componentOriginal84f9df3f620371229981225e7ba608d7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal84f9df3f620371229981225e7ba608d7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.seo-meta','data' => ['title' => 'Μεταποίηση Ρούχων — Dressman','description' => 'Επιδιορθώσεις και μεταποιήσεις ρούχων από τη μόδιστρά μας. Πάνω από 40 χρόνια εμπειρίας στην εφαρμογή ανδρικών ρούχων.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('seo-meta'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Μεταποίηση Ρούχων — Dressman','description' => 'Επιδιορθώσεις και μεταποιήσεις ρούχων από τη μόδιστρά μας. Πάνω από 40 χρόνια εμπειρίας στην εφαρμογή ανδρικών ρούχων.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal84f9df3f620371229981225e7ba608d7)): ?>
<?php $attributes = $__attributesOriginal84f9df3f620371229981225e7ba608d7; ?>
<?php unset($__attributesOriginal84f9df3f620371229981225e7ba608d7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal84f9df3f620371229981225e7ba608d7)): ?>
<?php $component = $__componentOriginal84f9df3f620371229981225e7ba608d7; ?>
<?php unset($__componentOriginal84f9df3f620371229981225e7ba608d7); ?>
<?php endif; ?>
     <?php $__env->endSlot(); ?>

     <?php $__env->slot('breadcrumb', null, []); ?> 
        <?php if (isset($component)) { $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [['label' => 'Μεταποίηση']]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['label' => 'Μεταποίηση']])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $attributes = $__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__attributesOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2)): ?>
<?php $component = $__componentOriginale19f62b34dfe0bfdf95075badcb45bc2; ?>
<?php unset($__componentOriginale19f62b34dfe0bfdf95075badcb45bc2); ?>
<?php endif; ?>
     <?php $__env->endSlot(); ?>

    
    <section class="relative bg-black">
        <?php if (isset($component)) { $__componentOriginal2aec210aa697e232bc2172dc47781d0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aec210aa697e232bc2172dc47781d0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.picture','data' => ['src' => 'images/metapoiisi-hero.jpeg','alt' => 'Μεταποίηση ρούχων Dressman','class' => 'w-full h-[50vh] sm:h-[60vh] object-cover','loading' => 'eager','fetchpriority' => 'high']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('picture'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => 'images/metapoiisi-hero.jpeg','alt' => 'Μεταποίηση ρούχων Dressman','class' => 'w-full h-[50vh] sm:h-[60vh] object-cover','loading' => 'eager','fetchpriority' => 'high']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2aec210aa697e232bc2172dc47781d0d)): ?>
<?php $attributes = $__attributesOriginal2aec210aa697e232bc2172dc47781d0d; ?>
<?php unset($__attributesOriginal2aec210aa697e232bc2172dc47781d0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2aec210aa697e232bc2172dc47781d0d)): ?>
<?php $component = $__componentOriginal2aec210aa697e232bc2172dc47781d0d; ?>
<?php unset($__componentOriginal2aec210aa697e232bc2172dc47781d0d); ?>
<?php endif; ?>
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="max-w-3xl mx-auto px-6 text-center text-white">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight">Επιδιορθώσεις & Μεταποιήσεις Ρούχων</h1>
                <p class="mt-4 text-base sm:text-lg leading-relaxed text-white/90">
                    Εμπιστευτείτε τα ρούχα σας στην εμπειρία μας. Πάνω από 40 χρόνια εξειδίκευσης στην εφαρμογή ανδρικών ρούχων.
                </p>
            </div>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        
        <section class="mt-4">
            <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-2">Σακάκι</h2>
            <div class="mt-4 divide-y divide-gray-100">
                <div class="flex justify-between py-3"><span class="text-gray-700">Κοντύνω μανίκια με μανσέτα</span><span class="font-medium text-gray-900">&euro;25</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Κοντύνω μανίκια από ώμο</span><span class="font-medium text-gray-900">&euro;40</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Κοντύνω κάτω μέρος</span><span class="font-medium text-gray-900">&euro;25</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Μακρύνω μανίκια</span><span class="font-medium text-gray-900">&euro;25</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Στενεύω πλάτη</span><span class="font-medium text-gray-900">&euro;15</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Στενεύω μανίκια</span><span class="font-medium text-gray-900">&euro;15</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Αλλαγή κουμπιών (εργασία)</span><span class="font-medium text-gray-900">&euro;5</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Αλλαγή τσέπης</span><span class="font-medium text-gray-900">&euro;15</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Αλλαγή φόδρας μανικιών</span><span class="font-medium text-gray-900">&euro;25</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Αλλαγή φόδρας (εργασία)</span><span class="font-medium text-gray-900">&euro;40</span></div>
            </div>
        </section>

        
        <section class="mt-10">
            <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-2">Παντελόνι</h2>
            <div class="mt-4 divide-y divide-gray-100">
                <div class="flex justify-between py-3"><span class="text-gray-700">Απλό κοντύνω</span><span class="font-medium text-gray-900">&euro;10</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Κοντύνω με ίδιο τελείωμα</span><span class="font-medium text-gray-900">&euro;15</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Κοντύνω με ρεβέρ</span><span class="font-medium text-gray-900">&euro;20</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Στενεύω μέση</span><span class="font-medium text-gray-900">&euro;15</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Στενεύω μέση (τζιν)</span><span class="font-medium text-gray-900">&euro;25</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Στενεύω γοφό</span><span class="font-medium text-gray-900">&euro;15</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Στενεύω μπατζάκι</span><span class="font-medium text-gray-900">&euro;25</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Αλλαγή φερμουάρ</span><span class="font-medium text-gray-900">&euro;30</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Αλλαγή τσέπης</span><span class="font-medium text-gray-900">&euro;15</span></div>
            </div>
        </section>

        
        <section class="mt-10">
            <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-2">Πουκάμισο</h2>
            <div class="mt-4 divide-y divide-gray-100">
                <div class="flex justify-between py-3"><span class="text-gray-700">Κοντύνω μανίκια</span><span class="font-medium text-gray-900">&euro;25</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Κοντύνω κάτω μέρος</span><span class="font-medium text-gray-900">&euro;25</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Στενεύω κορμό</span><span class="font-medium text-gray-900">&euro;35</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Στενεύω μανίκια</span><span class="font-medium text-gray-900">&euro;15</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Αλλαγή γιακά (εργασία)</span><span class="font-medium text-gray-900">&euro;10</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Μονόγραμμα</span><span class="font-medium text-gray-900">&euro;5</span></div>
            </div>
        </section>

        
        <section class="mt-10">
            <h2 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-2">Γιλέκο</h2>
            <div class="mt-4 divide-y divide-gray-100">
                <div class="flex justify-between py-3"><span class="text-gray-700">Κοντύνω κάτω μέρος</span><span class="font-medium text-gray-900">&euro;25</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Στενεύω</span><span class="font-medium text-gray-900">&euro;15</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Αλλαγή φόδρας</span><span class="font-medium text-gray-900">&euro;25</span></div>
                <div class="flex justify-between py-3"><span class="text-gray-700">Αλλαγή κουμπιών</span><span class="font-medium text-gray-900">&euro;5</span></div>
            </div>
        </section>

        <p class="mt-8 text-sm text-gray-500 text-center">* Οι τιμές δεν περιλαμβάνουν ΦΠΑ 24%</p>

        
        <div class="mt-12 grid sm:grid-cols-2 gap-4">
            <?php if (isset($component)) { $__componentOriginal2aec210aa697e232bc2172dc47781d0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aec210aa697e232bc2172dc47781d0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.picture','data' => ['src' => 'images/metapoiisi-2.jpg','alt' => 'Μεταποίηση ρούχων','class' => 'w-full h-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('picture'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => 'images/metapoiisi-2.jpg','alt' => 'Μεταποίηση ρούχων','class' => 'w-full h-auto']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2aec210aa697e232bc2172dc47781d0d)): ?>
<?php $attributes = $__attributesOriginal2aec210aa697e232bc2172dc47781d0d; ?>
<?php unset($__attributesOriginal2aec210aa697e232bc2172dc47781d0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2aec210aa697e232bc2172dc47781d0d)): ?>
<?php $component = $__componentOriginal2aec210aa697e232bc2172dc47781d0d; ?>
<?php unset($__componentOriginal2aec210aa697e232bc2172dc47781d0d); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal2aec210aa697e232bc2172dc47781d0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aec210aa697e232bc2172dc47781d0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.picture','data' => ['src' => 'images/metapoiisi-bg.webp','alt' => 'Κοστούμι Dressman','class' => 'w-full h-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('picture'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => 'images/metapoiisi-bg.webp','alt' => 'Κοστούμι Dressman','class' => 'w-full h-auto']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2aec210aa697e232bc2172dc47781d0d)): ?>
<?php $attributes = $__attributesOriginal2aec210aa697e232bc2172dc47781d0d; ?>
<?php unset($__attributesOriginal2aec210aa697e232bc2172dc47781d0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2aec210aa697e232bc2172dc47781d0d)): ?>
<?php $component = $__componentOriginal2aec210aa697e232bc2172dc47781d0d; ?>
<?php unset($__componentOriginal2aec210aa697e232bc2172dc47781d0d); ?>
<?php endif; ?>
        </div>

        
        <div class="mt-16 grid sm:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Ραφή &ndash; Προσαρμογή</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                    Προσαρμόζουμε κάθε ρούχο σε κάθε σωματότυπο. Αμέτρητες επιλογές customization για τα ρούχα σας με πάνω από 40 χρόνια εμπειρίας.
                </p>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">Μεταποιήσεις</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                    Μεταποιούμε τα αγαπημένα σας ρούχα καθώς αλλάζει το σώμα σας. Διατηρούμε το αρχικό πατρόν και προσαρμόζουμε στα νέα μέτρα.
                </p>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">Made to Measure</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                    Δημιουργούμε ρούχα από την αρχή βάσει των μέτρων σας. Επιλέξτε υφάσματα και λεπτομέρειες κατά τις προτιμήσεις σας.
                </p>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\Users\micha\Desktop\websites\shops\dressman\resources\views\pages\metapoiisi.blade.php ENDPATH**/ ?>