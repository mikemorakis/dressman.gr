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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.seo-meta','data' => ['title' => 'Made to Measure — Dressman','description' => 'Σχεδιάστε το κοστούμι σας όπως το θέλετε. Made to Measure υπηρεσία από τη Dressman — δεν είναι απλά ένα ρούχο, είναι τρόπος ζωής.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('seo-meta'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Made to Measure — Dressman','description' => 'Σχεδιάστε το κοστούμι σας όπως το θέλετε. Made to Measure υπηρεσία από τη Dressman — δεν είναι απλά ένα ρούχο, είναι τρόπος ζωής.']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.breadcrumb','data' => ['items' => [['label' => 'Made to Measure']]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('breadcrumb'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['label' => 'Made to Measure']])]); ?>
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

    
    <section class="relative bg-black overflow-hidden">
        <div class="absolute inset-0">
            <?php if (isset($component)) { $__componentOriginal2aec210aa697e232bc2172dc47781d0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aec210aa697e232bc2172dc47781d0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.picture','data' => ['src' => 'images/mtm-hero.jpeg','alt' => 'Made to Measure Dressman','class' => 'w-full h-full object-cover','loading' => 'eager','fetchpriority' => 'high']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('picture'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => 'images/mtm-hero.jpeg','alt' => 'Made to Measure Dressman','class' => 'w-full h-full object-cover','loading' => 'eager','fetchpriority' => 'high']); ?>
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
        
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Σχεδιάστε το Κοστούμι σας Όπως το Θέλετε</h2>
            <p class="mt-4 text-gray-600 leading-relaxed">
                Η αφοσίωσή μας στην ποιότητα και τη χειροποίητη δεξιοτεχνία αποτελεί την παράδοσή μας. Κάθε κοστούμι Made to Measure δημιουργείται αποκλειστικά για εσάς, βάσει των μέτρων σας και των προσωπικών σας επιλογών.
            </p>
        </div>

        
        <div class="mt-12">
            <?php if (isset($component)) { $__componentOriginal2aec210aa697e232bc2172dc47781d0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aec210aa697e232bc2172dc47781d0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.picture','data' => ['src' => 'images/mtm-process.jpg','alt' => 'Bespoke tailoring process','class' => 'w-full h-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('picture'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => 'images/mtm-process.jpg','alt' => 'Bespoke tailoring process','class' => 'w-full h-auto']); ?>
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

        
        <div class="mt-16 grid sm:grid-cols-2 gap-12 items-start">
            <div>
                <?php if (isset($component)) { $__componentOriginal2aec210aa697e232bc2172dc47781d0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aec210aa697e232bc2172dc47781d0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.picture','data' => ['src' => 'images/mtm-shirt.jpg','alt' => 'Made to Measure πουκάμισο','class' => 'w-full h-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('picture'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => 'images/mtm-shirt.jpg','alt' => 'Made to Measure πουκάμισο','class' => 'w-full h-auto']); ?>
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
                <?php if (isset($component)) { $__componentOriginal2aec210aa697e232bc2172dc47781d0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aec210aa697e232bc2172dc47781d0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.picture','data' => ['src' => 'images/mtm-fabric-1.jpeg','alt' => 'Επιλογή υφασμάτων','class' => 'w-full h-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('picture'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => 'images/mtm-fabric-1.jpeg','alt' => 'Επιλογή υφασμάτων','class' => 'w-full h-auto']); ?>
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
        </div>

        
        <div class="mt-16 max-w-sm mx-auto">
            <?php if (isset($component)) { $__componentOriginal2aec210aa697e232bc2172dc47781d0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aec210aa697e232bc2172dc47781d0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.picture','data' => ['src' => 'images/mtm-tuxedo.jpg','alt' => 'Made to Measure white tuxedo','class' => 'w-full h-auto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('picture'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => 'images/mtm-tuxedo.jpg','alt' => 'Made to Measure white tuxedo','class' => 'w-full h-auto']); ?>
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

        
        <div class="mt-16 text-center bg-gray-50 py-10 px-6">
            <h2 class="text-xl font-bold text-gray-900">Κλείστε Ραντεβού</h2>
            <p class="mt-2 text-gray-600">Επισκεφθείτε μας για να σχεδιάσουμε μαζί το δικό σας κοστούμι.</p>
            <div class="mt-6 space-y-2 text-sm text-gray-700">
                <p><strong>Boutique:</strong> Σκουφά 10, Κολωνάκι, Αθήνα &mdash; <a href="tel:+302155004038" class="underline hover:text-black">215 500 4038</a></p>
                <p><strong>Boutique &amp; Stockhouse:</strong> Λεωφ. Φυλής 116, Άνω Λιόσια &mdash; <a href="tel:+302102483370" class="underline hover:text-black">210 248 3370</a></p>
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
<?php /**PATH C:\Users\micha\Desktop\websites\shops\dressman\resources\views/pages/made-to-measure.blade.php ENDPATH**/ ?>