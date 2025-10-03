<?php

/**
 * Konfiguracja charakterystyk miast dla systemu estymacji populacji.
 *
 * Ten plik zawiera listy miast według ich charakterystyk:
 * - Miasta uniwersyteckie (uczelnie wyższe)
 * - Destynacje turystyczne
 * - Centra dojazdowe (regionalne centra pracy)
 *
 * Używane przez GUSApiService do wykrywania kontekstu lokalizacji
 * i stosowania odpowiednich współczynników korekcyjnych.
 *
 * @see POPULATION_ESTIMATION_COEFFICIENTS.md
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Miasta uniwersyteckie
    |--------------------------------------------------------------------------
    |
    | Lista miast posiadających uczelnie wyższe (uniwersytety, politechniki,
    | akademie). Obecność uczelni wpływa na współczynnik K_students.
    |
    | Źródła:
    | - POL-on (Zintegrowany System Informacji o Szkolnictwie Wyższym i Nauce)
    | - Lista uczelni publicznych i niepublicznych w Polsce
    |
    */

    'university_cities' => [
        // Duże ośrodki akademickie (>100k studentów rocznie)
        'warszawa',
        'kraków',
        'wrocław',
        'poznań',
        'łódź',
        'gdańsk',
        'katowice',
        'lublin',

        // Średnie ośrodki akademickie (30k-100k studentów)
        'szczecin',
        'bydgoszcz',
        'białystok',
        'toruń',
        'rzeszów',
        'olsztyn',
        'kielce',
        'opole',
        'zielona góra',
        'częstochowa',

        // Mniejsze ośrodki akademickie (uczelnie wyższe, akademie)
        'radom',
        'gliwice',
        'zabrze',
        'sosnowiec',
        'tychy',
        'koszalin',
        'elbląg',
        'legnica',
        'tarnów',
        'płock',
        'włocławek',
        'słupsk',
        'gorzów wielkopolski',
        'jelenia góra',
        'wałbrzych',
        'chełm',
        'siedlce',
        'kalisz',
        'konin',
        'piotrków trybunalski',
        'inowrocław',
        'ostrołęka',
        'tarnobrzeg',
        'suwałki',
        'zamość',
        'nowy sącz',
        'krosno',
        'przemyśl',
        'biała podlaska',
        'piła',
        'gniezno',
        'oświęcim',
        'racibórz',
        'nysa',
        'sanok',
        'cieszyn',
        'wadowice',
        'myszków',
        'jaworzno',
        'będzin',
        'rybnik',
        'dąbrowa górnicza',

        // Miasta z wyższymi szkołami zawodowymi/filiami
        'grudziądz',
        'tczew',
        'starogard gdański',
        'chorzów',
        'ruda śląska',
        'mysłowice',
        'siemianowice śląskie',
        'piekary śląskie',
        'świętochłowice',
        'zgierz',
        'pabianice',
        'tomaszów mazowiecki',
        'skierniewice',
        'kutno',
        'sieradz',
        'bełchatów',
        'puławy',
        'świdnica',
        'lubin',
        'głogów',
        'bolesławiec',
        'kędzierzyn-koźle',
        'brzeg',
        'oława',
    ],

    /*
    |--------------------------------------------------------------------------
    | Destynacje turystyczne
    |--------------------------------------------------------------------------
    |
    | Lista głównych destynacji turystycznych w Polsce. Ruch turystyczny
    | wpływa na współczynnik K_tourism.
    |
    | Kategorie:
    | - Miasta historyczne
    | - Kurorty nadmorskie
    | - Kurorty górskie
    | - Miejsca pielgrzymkowe
    | - Atrakcje kulturowe
    |
    | Źródła:
    | - GUS - Turystyka w Polsce
    | - POT (Polska Organizacja Turystyczna)
    |
    */

    'tourist_destinations' => [
        // Miasta historyczne i kulturowe
        'warszawa',
        'kraków',
        'wrocław',
        'poznań',
        'gdańsk',
        'toruń',
        'lublin',
        'zamość',
        'kazimierz dolny',
        'sandomierz',
        'chełmno',
        'gniezno',
        'płock',
        'łowicz',
        'łęczyca',
        'wiślica',
        'tarnów',
        'przemyśl',
        'jarosław',
        'lanckorona',
        'pułtusk',

        // Kurorty nadmorskie (Bałtyk)
        'sopot',
        'gdynia',
        'świnoujście',
        'kołobrzeg',
        'ustka',
        'łeba',
        'hel',
        'międzyzdroje',
        'władysławowo',
        'jastarnia',
        'darłowo',
        'mielno',
        'rewal',
        'dziwnówek',
        'pobierowo',
        'ustronie morskie',
        'niechorze',
        'sarbinowo',
        'rowy',
        'krynica morska',
        'stegna',
        'jurata',

        // Kurorty górskie (Tatry, Sudety, Beskidy)
        'zakopane',
        'karpacz',
        'szklarska poręba',
        'wisła',
        'ustroń',
        'szczyrk',
        'krynica-zdrój',
        'rabka-zdrój',
        'poronin',
        'białka tatrzańska',
        'bukowina tatrzańska',
        'korbielów',
        'zawoja',
        'międzybrodzie',
        'szczawnica',
        'muszyna',
        'duszniki-zdrój',
        'kudowa-zdrój',
        'lądek-zdrój',
        'polanica-zdrój',
        'świeradów-zdrój',
        'szczawno-zdrój',

        // Miejsca pielgrzymkowe i religijne
        'częstochowa',
        'wieliczka',
        'wadowice',
        'kalwaria zebrzydowska',
        'licheń stary',
        'niepokalanów',
        'łagiewniki',
        'góra św. anny',
        'ludźmierz',
        'święta lipka',
        'gietrzwałd',
        'grabarka',

        // Miejsca pamięci narodowej
        'oświęcim',
        'auschwitz',
        'malbork',
        'westerplatte',
        'treblinka',
        'majdanek',
        'stutthof',
        'monte cassino',
        'westerplatte',

        // Uzdrowiska i miasta spa
        'ciechocinek',
        'konstancin-jeziorna',
        'iwonicz-zdrój',
        'rymanów-zdrój',
        'solec-zdrój',
        'nałęczów',
        'busko-zdrój',
        'połczyn-zdrój',
        'kamień pomorski',
        'augustów',
        'supraśl',
        'horyniec-zdrój',

        // Regiony jezior i przyrody
        'mikołajki',
        'giżycko',
        'węgorzewo',
        'ruciane-nida',
        'pisz',
        'iława',
        'mrągowo',
        'ełk',
        'augustów',
        'suwałki',
        'wigry',

        // Miasta z atrakcjami turystycznymi
        'malbork',
        'łańcut',
        'kórnik',
        'gołuchów',
        'książ',
        'czocha',
        'niedzica',
        'dunajec',
        'ojców',
    ],

    /*
    |--------------------------------------------------------------------------
    | Centra dojazdowe (commuter hubs)
    |--------------------------------------------------------------------------
    |
    | Lista miast będących centrami regionalnymi - miejscami dojazdów do pracy.
    | Ruch dojazdowy wpływa na współczynnik K_commuters.
    |
    | Kryteria:
    | - Stolice województw
    | - Duże miasta powyżej 100k mieszkańców
    | - Miasta z rozwiniętym przemysłem
    | - Centra biznesowe regionów
    |
    */

    'commuter_hubs' => [
        // Stolice województw
        'warszawa',
        'kraków',
        'wrocław',
        'poznań',
        'gdańsk',
        'łódź',
        'katowice',
        'lublin',
        'białystok',
        'szczecin',
        'bydgoszcz',
        'olsztyn',
        'rzeszów',
        'toruń',
        'kielce',
        'opole',
        'zielona góra',
        'gorzów wielkopolski',

        // Duże miasta >100k (centra regionalne)
        'radom',
        'częstochowa',
        'sosnowiec',
        'gliwice',
        'zabrze',
        'bielsko-biała',
        'bytom',
        'rybnik',
        'ruda śląska',
        'tychy',
        'dąbrowa górnicza',
        'płock',
        'elbląg',
        'wałbrzych',
        'włocławek',
        'tarnów',
        'chorzów',
        'koszalin',
        'kalisz',
        'legnica',
        'grudziądz',

        // Ważne centra przemysłowe i biznesowe
        'jaworzno',
        'będzin',
        'mysłowice',
        'siemianowice śląskie',
        'piekary śląskie',
        'świętochłowice',
        'pabianice',
        'zgierz',
        'tomaszów mazowiecki',
        'starachowice',
        'ostrowiec świętokrzyski',
        'stalowa wola',
        'mielec',
        'tarnobrzeg',
        'puławy',
        'świdnica',
        'jelenia góra',
        'lubin',
        'głogów',
        'polkowice',
        'bolesławiec',
        'konin',
        'piła',
        'inowrocław',
        'gniezno',
        'leszno',
        'ostrów wielkopolski',
        'kędzierzyn-koźle',
        'nysa',
        'nowy sącz',
        'przemyśl',
        'zamość',
        'chełm',
        'biała podlaska',
        'siedlce',
        'ciechanów',
        'ostrołęka',
        'suwałki',
        'ełk',
        'słupsk',
        'koszalin',
        'stargard',
        'kołobrzeg',
    ],

    /*
    |--------------------------------------------------------------------------
    | Aglomeracje miejskie
    |--------------------------------------------------------------------------
    |
    | Definicje głównych aglomeracji miejskich w Polsce. Miasta w aglomeracji
    | mają zwiększone współczynniki ze względu na wzajemny ruch dojazdowy.
    |
    */

    'agglomerations' => [
        'warszawska' => [
            'warszawa', 'pruszków', 'piaseczno', 'legionowo', 'marki', 'ząbki',
            'otwock', 'wołomin', 'grodzisk mazowiecki', 'podkowa leśna',
            'józefów', 'konstancin-jeziorna', 'sulejówek', 'kobyłka', 'zielonka',
            'łomianki', 'michałowice', 'raszyn', 'piastów', 'brwinów',
        ],

        'krakowska' => [
            'kraków', 'skawina', 'wieliczka', 'niepołomice', 'krzeszowice',
            'zabierzów', 'zielonki', 'świątniki górne',
        ],

        'górnośląska' => [
            'katowice', 'gliwice', 'zabrze', 'bytom', 'sosnowiec', 'dąbrowa górnicza',
            'rybnik', 'tychy', 'chorzów', 'ruda śląska', 'mysłowice', 'będzin',
            'piekary śląskie', 'siemianowice śląskie', 'świętochłowice', 'jaworzno',
            'mikołów', 'łaziska górne', 'knurów', 'czerwionka-leszczyny',
        ],

        'trójmiejska' => [
            'gdańsk', 'gdynia', 'sopot', 'wejherowo', 'reda', 'rumia', 'pruszcz gdański',
        ],

        'łódzka' => [
            'łódź', 'pabianice', 'zgierz', 'konstantynów łódzki', 'aleksandrów łódzki',
            'ozorków', 'głowno',
        ],

        'wrocławska' => [
            'wrocław', 'oława', 'brzeg dolny', 'siechnice', 'czernica', 'długołęka',
        ],

        'poznańska' => [
            'poznań', 'luboń', 'swarzędz', 'puszczykowo', 'mosina', 'kórnik',
            'kostrzyn', 'stęszew', 'tarnowo podgórne', 'dopiewo',
        ],

        'szczecińska' => [
            'szczecin', 'police', 'stargard', 'goleniów',
        ],

        'bydgosko-toruńska' => [
            'bydgoszcz', 'toruń', 'solec kujawski', 'koronowo', 'białe błota',
        ],
    ],

];
