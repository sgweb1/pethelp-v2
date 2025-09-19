{{-- FAQ Section Component --}}
<section class="py-16 lg:py-24 bg-gray-50 dark:bg-gray-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Często zadawane pytania
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-300">
                Znajdź odpowiedzi na najczęściej zadawane pytania
            </p>
        </div>

        <div class="space-y-6">
            <x-homepage.faq-item
                question="Jak sprawdzani są opiekunowie?"
                answer="Każdy opiekun przechodzi dokładną weryfikację dokumentów, wywiad wideo oraz sprawdzenie referencji. Wszyscy opiekunowie mają ubezpieczenie OC i przechodzą regularne szkolenia." />

            <x-homepage.faq-item
                question="Ile kosztują usługi opiekunów?"
                answer="Ceny różnią się w zależności od rodzaju usługi i lokalizacji. Spacer z psem kosztuje średnio 25-40 PLN, opieka dzienna 80-150 PLN, a opieka nocna 120-250 PLN. Każdy opiekun ustala własne stawki." />

            <x-homepage.faq-item
                question="Co jeśli coś pójdzie nie tak podczas opieki?"
                answer="Wszystkie usługi są objęte ubezpieczeniem OC. W przypadku problemów, nasze wsparcie 24/7 natychmiast podejmie działania. Oferujemy także gwarancję zadowolenia - jeśli nie jesteś zadowolony, zwrócimy pieniądze." />

            <x-homepage.faq-item
                question="Czy mogę anulować rezerwację?"
                answer="Tak, możesz anulować rezerwację bezpłatnie do 24 godzin przed planowaną usługą. W przypadku późniejszej anulacji może zostać naliczona opłata zgodnie z polityką opiekuna." />
        </div>

        <div class="text-center mt-12">
            <a href="#"
               class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 font-medium">
                Zobacz wszystkie pytania i odpowiedzi
                <x-icon name="heroicon-s-arrow-right" class="w-4 h-4 ml-2" />
            </a>
        </div>
    </div>
</section>